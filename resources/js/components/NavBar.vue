<template>
    <v-app-bar
      color="deep-purple"
      dark
    >
      <v-btn>
        <v-toolbar-title @click="homeHandler()">Quiz App</v-toolbar-title>
      </v-btn>

      <v-spacer></v-spacer>

      <v-btn @click="aboutHandler()">
        Sobre
      </v-btn>

      <v-btn @click="logout()">
        Sair
      </v-btn>
    </v-app-bar>
</template>

<script>
  import Cookie from '../service/cookie';
  export default {
    data: () => ({
      drawer: false,
      group: null,
    }),

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
      }
    }
  }
</script>