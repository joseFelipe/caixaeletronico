<template>
  <div class="container">
    <div class="row mt-5">
      <div class="col-lg-6 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3>{{ balance }}</h3>
            <p>Saldo</p>
          </div>
          <div class="icon">
            <i class="fas fa-comment-dollar"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
          <div class="inner">
            <h3>{{ withdrawal }}</h3>
            <p>Saques</p>
          </div>
          <div class="icon">
            <i class="fas fa-arrow-alt-circle-down"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
          <div class="inner">
            <h3>{{ deposit }}</h3>
            <p>Depósitos</p>
          </div>
          <div class="icon">
            <i class="fas fa-arrow-alt-circle-up"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-5">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Transações</h3>

            <div class="card-tools">
              <button class="btn btn-primary" @click="newModal">
                Nova transação
                <i class="fas fa-plus-circle"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table class="table table-hover">
              <tbody>
                <tr>
                  <th>ID</th>
                  <th>Valor</th>
                  <th>Data</th>
                  <th>Tipo</th>
                  <!-- <th>Ações</th> -->
                </tr>
                <tr v-for="transaction in transactions" :key="transaction.id">
                  <td>{{ transaction.id }}</td>
                  <td>{{ transaction.value }}</td>
                  <td>{{ transaction.created_at | myDate }}</td>
                  <td>{{ transaction.type === 0 ? 'Saque' : 'Depósito' }}</td>
                  <!-- <td>
                    <a
                      href="#"
                      class="btn btn-primary btn-sm"
                      @click="editModal(transaction)"
                    >Editar</a>
                    <a
                      href="#"
                      class="btn btn-danger btn-sm"
                      @click="deleteUser(transaction.id)"
                    >Excluir</a>
                  </td>-->
                </tr>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
    </div>

    <!-- Modal -->
    <div
      class="modal fade"
      id="addNew"
      tabindex="-1"
      role="dialog"
      aria-labelledby="addNew"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 v-show="editMode" class="modal-title" id="addNew">Atualizar transação</h5>
            <h5 v-show="!editMode" class="modal-title" id="addNew">Nova transação</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="editMode ? updateTransaction() : createTransaction()">
            <div class="modal-body">
              <div class="form-group">
                <input
                  v-model="form.value"
                  minlength="1"
                  maxlength="999"
                  type="text"
                  value="value"
                  placeholder="Valor"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('value') }"
                >
                <has-error :form="form" field="value"></has-error>
              </div>

              <div class="form-group">
                <select
                  name="type"
                  v-model="form.type"
                  class="form-control"
                  id="type"
                  :class="{ 'is-invalid': form.errors.has('type') }"
                >
                  <option value="0" selected>Saque</option>
                  <option value="1">Depósito</option>
                </select>
                <has-error :form="form" field="type"></has-error>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
              <button v-show="editMode" type="submit" class="btn btn-primary">Atualizar</button>
              <button v-show="!editMode" type="submit" class="btn btn-primary">Salvar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      editMode: true,
      transactions: {},
      form: new Form({
        id: "",
        value: "",
        type: ""
      })
    };
  },
  methods: {
    newModal() {
      this.editMode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },
    editModal(transaction) {
      this.editMode = true;
      this.form.fill(transaction);
      $("#addNew").modal("show");
    },
    loadTransactions() {
      axios
        .get("api/transaction")
        .then(
          ({ data }) => (
            (this.transactions = data.transaction.data),
            (this.balance = data.balance.balance),
            (this.withdrawal = data.withdrawal.withdrawal),
            (this.deposit = data.deposit.deposit)
          )
        );
    },
    createTransaction() {
      this.$Progress.start();

      var balance, formValue;

      balance = parseInt(this.balance);
      formValue = parseInt(this.form.value);

      if (typeTransaction != "") {
        console.log("typeTransaction: " + typeTransaction);
        typeTransaction = parseInt(typeTransaction);
      } else {
        console.log("typeTransaction empty");
      }

      typeTransaction = this.form.type ? parseInt(this.form.type) : "";

      if (formValue > balance && this.form.type == 0) {
        toast.fire({
          type: "error",
          title: "Saldo insuficiente"
        });
        return false;
      }

      if (this.form.type != "0" || this.form.type != "1") {
        toast.fire({
          type: "warning",
          title: "Informe o tipo de transação"
        });
        return false;
      }

      this.form
        .post("api/transaction")
        .then(() => {
          Fire.$emit("TriggerLoad");

          $("#addNew").modal("hide");

          toast.fire({
            type: "success",
            title: "Transação efetuada com sucesso"
          });

          this.$Progress.finish();
        })
        .catch(() => {});
    },
    updateTransaction() {
      this.form
        .put("api/transaction/" + this.form.id)
        .then(() => {
          Fire.$emit("TriggerLoad");

          $("#addNew").modal("hide");

          toast.fire({
            type: "success",
            title: "Transação atualizada com sucesso"
          });

          this.$Progress.finish();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    deleteUser(id) {
      swal
        .fire({
          title: "Tem certeza?",
          text: "Não será possível desfazer esta exclusão!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Sim, excluir!"
        })
        .then(result => {
          this.form
            .delete("api/transaction/" + id)
            .then(() => {
              if (result.value) {
                toast.fire({
                  type: "success",
                  title: "Transação excluída com sucesso"
                });
                Fire.$emit("TriggerLoad");
              }
            })
            .catch(() => {
              swal.fire({
                title: "Ops!",
                text: "Ocorreu algum problema.",
                type: "error"
              });
            });
        });
    }
  },
  created() {
    this.loadTransactions();
    Fire.$on("TriggerLoad", () => {
      this.loadTransactions();
    });
  }
};
</script>
