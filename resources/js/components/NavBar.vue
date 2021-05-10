<template>
    <div class="pb-3">
  <b-navbar toggleable="lg" type="dark" variant="info">
    <b-navbar-brand href="" @click="homeHandler()">Quiz App</b-navbar-brand>

    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

    <b-collapse id="nav-collapse" is-nav>
      <b-navbar-nav v-if="user.type==='teacher'">
        <b-nav-item @click="handleForm()">Cadastrar Informações</b-nav-item>
        <b-nav-item @click="handleReport()">Relatórios</b-nav-item>
      </b-navbar-nav>

      <b-navbar-nav v-if="user.type==='participant'">
        <b-nav-item>Realizar Prova</b-nav-item>
        <b-nav-item>Boletim</b-nav-item>
      </b-navbar-nav>

      <!-- Right aligned nav items -->
      <b-navbar-nav class="ml-auto">
        <b-nav-item @click="aboutHandler()">Sobre</b-nav-item>
        <b-nav-item-dropdown right>
          <!-- Using 'button-content' slot -->
          <template #button-content>
            <em>User</em>
          </template>
          <b-dropdown-item href="#">Profile</b-dropdown-item>
          <b-dropdown-item @click="logout()">Sign Out</b-dropdown-item>
        </b-nav-item-dropdown>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</div>
</template>

<script>
  import Cookie from '../service/cookie';
  import { mapState } from 'vuex';

  export default {
    data: () => ({
      drawer: false,
      group: null,
    }),

    computed: {
      ...mapState({
        user: state => state.user.user,
      }),
    },

    methods: {
      async logout() {
        await axios.post('api/logout');
        Cookie.deleteToken();
        this.$router.push({name: 'login'});
      },

      aboutHandler() {
        this.$router.push({name: 'about'});
      },

      homeHandler() {
        this.$router.push({name: 'index'});
      },

      handleForm() {
        this.$router.push({name: 'register-info'});
      },

      handleReport() {
        this.$router.push({name: 'report'});
      }
    }
  }
</script>