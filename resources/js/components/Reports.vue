<template>
  <div class="container">
    <div class="row mt-5">
      <div class="col-md-11 col-lg-11">
        <h3>Selecione a conta</h3>
        <div class="input-group">
          <select
            name="account-origin"
            @change="onChangeAccount($event)"
            id="account-origin"
            v-model="form.account"
            class="custom-select"
          >
            <option
              v-for="account in accounts"
              v-bind:value="{ id: account.id, text: account.title }"
              :key="account.id"
            >{{ account.title }}</option>
          </select>
          <div class="input-group-append">
            <button class="btn btn-outline-primary" @click="exportPDF">
              Gerar relatório
              <i class="fas fa-file-download"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <hr>
    <div class="row mt-5">
      <div class="col-md-11 col-lg-11" id="div-transactions">
        <div class="card">
          <div class="card-header">
            <h3
              class="card-title"
            >Transações {{ form.account.text == null ? '' : " - Conta " + form.account.text }}</h3>
          </div>
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
                <tr v-for="transaction in transactions" :key="transaction.id"></tr>

                <tr v-for="transacao in transacoes" :key="transacao.id">
                  <td>{{ transacao.id }}</td>
                  <td>R$ {{ transacao.value }}</td>
                  <td class="column-account-type">
                    <span v-bind:class="transacao.type | TypeBadge">{{ transacao.type | Type }}</span>
                  </td>
                  <td class="column-account-origin">{{ transacao.account_origin | Account }}</td>
                  <td>
                    <p v-if="transacao.account_destiny != null">
                      <i class="icon-transfer fas fa-arrow-right"></i>
                    </p>
                  </td>
                  <td class="column-account-destiny">{{ transacao.account_destiny | Account }}</td>
                  <td>{{ transacao.created_at | myDate }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer clearfix">
            <h5 v-if="this.account_value">Total: R$ {{ this.account_value }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selected: "",
      editMode: true,
      transactions: {},
      transacoes: {},
      form: new Form({
        account: ""
      })
    };
  },
  methods: {
    loadAccounts() {
      axios
        .get("api/account")
        .then(({ data }) => (this.accounts = data.accounts.data));
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

    onChangeAccount(event) {
      this.loadTransactionsByAccount(this.form.account.id);
      if (this.form.account.id == 1) {
        this.account_value = this.current_account;
      } else if (this.form.account.id == 2) {
        this.account_value = this.saving_account;
      } else {
        this.account_value = this.salary_account;
      }
    },

    loadTransactionsByAccount() {
      axios
        .get("api/transaction/" + this.form.account.id)
        .then(({ data }) => (this.transacoes = data.transacoes.data));
    },

    exportPDF() {
      if (this.form.account === "") {
        toast.fire({
          type: "warning",
          title: "Selecione a conta"
        });
        return false;
      }

      html2canvas(document.getElementById("div-transactions"), {
        onrendered: function(canvas) {
          var img = canvas.toDataURL("image/png");

          var doc = new jsPDF({
            orientation: "landscape"
          });

          doc.addImage(img, "JPEG", 5, 5);
          doc.save("transaction.pdf");
        }
      });
    }
  },
  created() {
    this.loadAccounts();
    this.loadTransactions();
    Fire.$on("TriggerLoad", () => {
      //this.loadTransactionsByAccount();
    });
  }
};
</script>
