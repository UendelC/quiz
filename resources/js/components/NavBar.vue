<template>
    <div class="pb-3">
  <b-navbar toggleable="lg" type="dark" variant="dark">
    <b-navbar-brand href="" @click="homeHandler()">Quiz App</b-navbar-brand>

    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

    <b-collapse id="nav-collapse" is-nav>
      <b-navbar-nav v-if="user.type==='teacher'">
        <b-nav-item @click="handleForm()">Gerenciar Avaliações</b-nav-item>
        <b-nav-item @click="handleReport()">Relatórios</b-nav-item>
      </b-navbar-nav>

      <b-navbar-nav v-if="user.type==='participant'">
        <b-nav-item @click="handleExam()">Realizar Prova</b-nav-item>
        <b-nav-item @click="handleGrade()">Boletim</b-nav-item>
      </b-navbar-nav>

      <!-- Right aligned nav items -->
      <b-navbar-nav class="ml-auto">
        <b-nav-item @click="aboutHandler()">Sobre</b-nav-item>
        <b-nav-item-dropdown right>
          <!-- Using 'button-content' slot -->
          <template #button-content>
            <em>{{ user ? user.name : 'user' }}</em>
          </template>
          <b-dropdown-item>Turma: {{ user.subject }}</b-dropdown-item>
          <b-dropdown-item @click="logout()">Sair</b-dropdown-item>
        </b-nav-item-dropdown>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</div>
</template>

<script>
  import Cookie from '../service/cookie';
  import { mapState } from 'vuex';
  import store from '../store';

  export default {
    data: () => ({
      drawer: false,
      group: null,
    }),

    computed: {
      ...mapState({
        user: state => state.user.user.user,
      }),
    },

    methods: {
      async logout() {
        await axios.post('api/logout');
        Cookie.deleteToken();
        store.commit('user/CLEAR_USER');
        this.$router.push({name: 'login'});
      },

      aboutHandler() {
        this.$router.push({name: 'about'});
      },

      homeHandler() {
        this.$router.push({name: 'about'});
      },

      handleForm() {
        this.$router.push({name: 'exam-management'});
      },

      handleReport() {
        this.$router.push({name: 'report'});
      },

      handleExam() {
        this.$router.push({name: 'exam'});
      },

      handleGrade() {
        this.$router.push({name: 'grades'});
      }
    }
  }
</script>

<style scoped>
  /* nav bar azul */
  .navbar.navbar-dark.bg-dark{
    background-color: #03396c!important;
 }
</style>