<template>
  <div class="container">
    <div class="row mt-5">
      <div class="col-lg-4 col-6">
        <div class="small-box bg-info-gradient">
          <div class="inner">
            <h5>R$</h5>
            <h3>{{ current_account != 0 ? current_account : '0' }}</h3>
            <h5>Conta Corrente</h5>
          </div>
          <div class="icon">
            <i class="fas fa-comment-dollar"></i>
          </div>
          <span class="small-box-footer"></span>
        </div>
      </div>
      <div class="col-lg-4 col-6">
        <div class="small-box bg-warning-gradient">
          <div class="inner">
            <h5>R$</h5>
            <h3>{{ saving_account != 0 ? saving_account : '0' }}</h3>
            <h5>Conta Poupança</h5>
          </div>
          <div class="icon">
            <i class="fas fas fa-wallet"></i>
          </div>
          <span class="small-box-footer"></span>
        </div>
      </div>
      <div class="col-lg-4 col-6">
        <div class="small-box bg-success-gradient">
          <div class="inner">
            <h5>R$</h5>
            <h3>{{ salary_account != 0 ? salary_account : '0' }}</h3>
            <h5>Conta Salário</h5>
          </div>
          <div class="icon">
            <i class="fas fa-comments-dollar"></i>
          </div>
          <span class="small-box-footer"></span>
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
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Valor</th>
                  <th class="column-account-type">Tipo transação</th>
                  <th class="column-account-origin">Conta Origem</th>
                  <th></th>
                  <th class="column-account-destiny">Conta Destino</th>
                  <th>Data</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="transaction in transactions" :key="transaction.id">
                  <td>{{ transaction.id }}</td>
                  <td>R$ {{ transaction.value }}</td>
                  <td class="column-account-type">
                    <span v-bind:class="transaction.type | TypeBadge">{{ transaction.type | Type }}</span>
                  </td>
                  <td class="column-account-origin">{{ transaction.account_origin | Account }}</td>
                  <td>
                    <p v-if="transaction.account_destiny != null">
                      <i class="icon-transfer fas fa-arrow-right"></i>
                    </p>
                  </td>
                  <td class="column-account-destiny">{{ transaction.account_destiny | Account }}</td>
                  <td>{{ transaction.created_at | myDate }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
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
                <label for="account-destiny">Tipo transação</label>
                <select
                  name="type"
                  v-model="form.type"
                  class="form-control"
                  id="type"
                  :class="{ 'is-invalid': form.errors.has('type') }"
                >
                  <option value="0" selected>Saque</option>
                  <option value="1">Depósito</option>
                  <option value="2">Transferência</option>
                </select>
                <has-error :form="form" field="type"></has-error>
              </div>

              <div class="form-group">
                <label for="account-origin" v-if="form.type == 2">Conta Origem</label>
                <label for="account-origin" v-if="form.type != 2">Conta</label>
                <select
                  name="account-origin"
                  id="account-origin"
                  v-model="form.accountOrigin"
                  class="form-control"
                >
                  <option
                    v-for="account in accounts"
                    v-bind:value="account.id"
                    :key="account.id"
                  >{{ account.title }}</option>
                </select>
              </div>

              <div class="form-group" v-if="form.type == 2">
                <label for="account-destiny">Conta Destino</label>
                <select
                  name="account-destiny"
                  id="account-destiny"
                  v-model="form.accountDestiny"
                  class="form-control"
                >
                  <option
                    v-for="account in accounts"
                    v-bind:value="account.id"
                    :key="account.id"
                  >{{ account.title }}</option>
                </select>
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
        type: "",
        accountOrigin: "",
        accountDestiny: ""
      }),
      sbadge: `<small class="badge badge-danger">Transferência</small>`
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
            (this.current_account = data.current_account),
            (this.saving_account = data.saving_account),
            (this.salary_account = data.salary_account)
          )
        );
    },
    loadAccounts() {
      axios
        .get("api/account")
        .then(({ data }) => (this.accounts = data.accounts.data));
    },
    createTransaction() {
      this.$Progress.start();

      var typeTransaction,
        accountOrigin,
        accountDestiny,
        current_account_value,
        saving_account_value,
        salary_account_value,
        value;

      value = parseInt(this.form.value);
      current_account_value = parseInt(this.current_account);
      saving_account_value = parseInt(this.saving_account);
      salary_account_value = parseInt(this.salary_account);

      typeTransaction = this.form.type ? parseInt(this.form.type) : "";

      accountOrigin = this.form.accountOrigin
        ? parseInt(this.form.accountOrigin)
        : "";
      accountDestiny = this.form.accountDestiny
        ? parseInt(this.form.accountDestiny)
        : "";

      if (isNaN(value)) {
        toast.fire({
          type: "warning",
          title: "Informe o valor"
        });
        return false;
      }

      if (value <= 0) {
        toast.fire({
          type: "warning",
          title: "O valor deve ser maior que 0"
        });
        return false;
      } else if (value > 999) {
        toast.fire({
          type: "warning",
          title: "O valor deve ser menor que 999"
        });
        return false;
      }

      if (typeTransaction === "") {
        toast.fire({
          type: "warning",
          title: "Selecione o tipo de transação"
        });
        return false;
      }

      if (typeTransaction == 2) {
        if (accountOrigin === "") {
          toast.fire({
            type: "warning",
            title: "Selecione a conta de origem"
          });
          return false;
        }
        if (accountDestiny === "") {
          toast.fire({
            type: "warning",
            title: "Selecione a conta de destino"
          });
          return false;
        }
      }

      if (this.form.accountOrigin === "") {
        toast.fire({
          type: "warning",
          title: "Selecione a conta"
        });
        return false;
      }

      if (typeTransaction != 1) {
        switch (this.form.accountOrigin) {
          case 1:
            if (this.form.value > current_account_value) {
              toast.fire({
                type: "warning",
                title: "Saldo insuficiente na conta corrente"
              });
              return false;
            }
            break;
          case 2:
            if (this.form.value > saving_account_value) {
              toast.fire({
                type: "warning",
                title: "Saldo insuficiente na conta poupança"
              });
              return false;
            }
            break;
          case 3:
            if (this.form.value > salary_account_value) {
              toast.fire({
                type: "warning",
                title: "Saldo insuficiente na conta salário"
              });
              return false;
            }
            break;
          default:
            console.log("erro switch transaction type");
        }
      }

      if (this.form.accountDestiny !== "") {
        if (this.form.accountOrigin == this.form.accountDestiny) {
          toast.fire({
            type: "error",
            title: "A conta de destino não pode ser igual a conta de origem"
          });
          return false;
        }
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
    this.loadAccounts();
    Fire.$on("TriggerLoad", () => {
      this.loadTransactions();
    });
  }
};
</script>
