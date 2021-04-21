<template>
  <div>
    <!-- <nav-bar></nav-bar> -->
    <router-view></router-view>
  </div>
</template>

<script>
import NavBar from './NavBar.vue'
export default {
  components: { NavBar },
    name: 'App',
}
</script>

<style>

</style>