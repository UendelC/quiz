<template>
<div>
    <nav-bar></nav-bar>
    <section class="section">
        <div class="container text-center">
            <img alt="Univasf Logo" class="" src="https://portais.univasf.edu.br/imagens-noticias/MarcaUnivasfCompletaComfundo.jpg">
            <h1 class="title"><strong>Trabalho apresentado à Universidade Federal do Vale do São Francisco - UNIVASF, Campus Juazeiro, como requisito para obtenção do título de Bacharel em Engenharia da Computação.</strong></h1>
            <div class="body-text">
              <p>Aluno: Uendel Luiz Neto Couto da Silva</p>
              <p>Orientador: Brauliro Gonçalves Leal</p>
            </div>

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
<style scoper>
  img {
    margin: 0 auto;
    width: 300px;
  }

  .title {
    padding: 50px 0;
    font-size: 25px;
    text-align: justify;
  }

  .body-text {
    padding: 30px 0;
  }


</style>