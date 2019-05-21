<template>
  <div class="container">
    <div class="row mt-5">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Perfil</h3>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover">
              <tbody>
                <tr>
                  <th>Nome</th>
                  <th>CPF</th>
                  <th>Data Registro</th>
                  <th>Ações</th>
                </tr>
                <tr v-for="user in users" :key="user.id">
                  <td>{{ user.name }}</td>
                  <td>{{ user.cpf }}</td>
                  <td>{{ user.created_at | myDate }}</td>
                  <td>
                    <a
                      href="#"
                      class="btn btn-primary btn-sm"
                      @click="editModal(user)"
                    >Alterar senha</a>
                    <!-- <a href="#" class="btn btn-danger btn-sm" @click="deleteUser(user.id)">Excluir</a> -->
                  </td>
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
            <h5 class="modal-title" id="addNew">Alterar senha</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="updatePassword()">
            <div class="modal-body">
              <div class="form-group">
                <input
                  v-model="form.name"
                  type="text"
                  name="name"
                  placeholder="Name"
                  class="form-control"
                  disabled
                  :class="{ 'is-invalid': form.errors.has('name') }"
                >
                <has-error :form="form" field="name"></has-error>
              </div>

              <div class="form-group">
                <input
                  v-model="form.password"
                  type="password"
                  name="currentPassword"
                  id="currentPassword"
                  placeholder="Senha atual"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('password') }"
                >
                <has-error :form="form" field="password"></has-error>
              </div>
              <div class="form-group">
                <input
                  v-model="form.newPassword1"
                  type="password"
                  name="newPassword1"
                  id="newPassword1"
                  placeholder="Nova senha"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('newPassword1') }"
                >
                <has-error :form="form" field="newPassword1"></has-error>
              </div>
              <div class="form-group">
                <input
                  v-model="form.newPassword2"
                  type="password"
                  name="newPassword2"
                  id="newPassword2"
                  placeholder="Repita a nova senha"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('newPassword2') }"
                >
                <has-error :form="form" field="newPassword2"></has-error>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
              <button type="submit" class="btn btn-primary">Atualizar</button>
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
      users: {},
      form: new Form({
        id: "",
        name: "",
        email: "",
        currentPassword: "",
        newPassword1: "",
        newPassword2: "",
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
    editModal(user) {
      this.editMode = true;
      this.form.fill(user);
      console.log("user: " + user);
      $("#addNew").modal("show");
    },
    loadUsers() {
      axios.get("api/user").then(({ data }) => (this.users = data.data));
    },
    updatePassword() {
      console.log("- - -");
      console.log("CurrentPassword1: " + this.form.currentPassword);
      console.log("NewPassword1: " + this.form.newPassword1);
      console.log("NewPassword2: " + this.form.newPassword2);
      console.log("- - -");

      return false;

      this.form
        .put("api/user/" + this.form.id)
        .then(() => {
          Fire.$emit("TriggerLoad");

          $("#addNew").modal("hide");

          toast.fire({
            type: "success",
            title: "Senha alterada com sucesso"
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
          text: "Você não poderá desfazer esta operação.",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Sim"
        })
        .then(result => {
          this.form
            .delete("api/user/" + id)
            .then(() => {
              if (result.value) {
                toast.fire({
                  type: "success",
                  title: "Usuário excluído com sucesso."
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
    this.loadUsers();
    Fire.$on("TriggerLoad", () => {
      this.loadUsers();
    });
  }
};
</script>
