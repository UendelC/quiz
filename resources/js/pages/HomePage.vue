<template>
  <div>
    <nav-bar></nav-bar>
    <example-component>
    </example-component>
  </div>
</template>

<script>
import ExampleComponent from '../components/ExampleComponent';
import NavBar from '../components/NavBar';
export default {
  components: {
    ExampleComponent,
    NavBar
  }
}
</script>

<style>

</style>