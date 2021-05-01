<template>
    <v-app-bar
      color="deep-purple"
      dark
    >
      <v-app-bar-nav-icon></v-app-bar-nav-icon>

      <v-toolbar-title>Title</v-toolbar-title>
       <v-toolbar-items>
         <v-btn
           to="#"
         > Sobre
         </v-btn>
        </v-toolbar-items>
    </v-app-bar>
</template>

<script>
  export default {
    data: () => ({
      drawer: false,
      group: null,
    }),
  }
</script>