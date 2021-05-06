<template>
<div>
    <nav-bar></nav-bar>
    <section class="section">
        <div class="container text-center">
            <img alt="Vue logo" class="rounded-circle" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1f/Woman_1.jpg/220px-Woman_1.jpg">
            <h1>Software Developer</h1>
            <p>Este sistema foi desenvolvido por Uendel Couto como Trabalho de Conclusão de curso no ano de 2021.</p>
        </div>
    </section>
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
  img {
    margin: 0 auto;
  }
</style>