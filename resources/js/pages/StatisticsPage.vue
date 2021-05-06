<template>
  <div>
    <nav-bar></nav-bar>
    <iframe class="col-md-12" src="https://datastudio.google.com/embed/reporting/8fa71982-e217-42a7-b1cd-a78e7ed8ba3e/page/UxgAC" frameborder="0" style="border:0" allowfullscreen></iframe>
  </div>
</template>

<script>
import NavBar from '../components/NavBar';
export default {
  components: {
    NavBar
  }
}
</script>

<style>
iframe
{
  height: 100vh;
  width: 100% !important;
}
</style>