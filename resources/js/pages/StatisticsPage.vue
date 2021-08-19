<template>
  <div>
    <nav-bar></nav-bar>
    <div class="container">
      <b-row>
        <b-col>
          <label class="typo__label">Participantes</label>
          <multiselect
            v-model="selectedParticipants"
            placeholder="Selecione participantes"
            label="name"
            track-by="code"
            :options="participants"
            :multiple="true"
            :taggable="false"
            select-label="Pressione enter para selecionar"
            selected-label="Selecionado"
            deselect-label="Pressione enter para remover"
            noResult="Nenhum element encontrado. Busque novamente"
            :loading="false"
            :disabled="false"
          >
            <span slot="noResult">Oops! Nenhum element encontrado. Busque novamente.</span>
            <span slot="noOptions">Lista vazia</span>
          </multiselect>
        </b-col>
        <b-col>
          <label class="typo__label">Categorias</label>
          <multiselect
            v-model="selectedCategories"
            placeholder="Selecione categorias"
            label="name"
            track-by="code"
            :options="categories"
            :multiple="true"
            :taggable="false"
            select-label="Pressione enter para selecionar"
            selected-label="Selecionado"
            deselect-label="Pressione enter para remover"
            noResult="Nenhum element encontrado. Busque novamente"
            :loading="false"
            :disabled="false"
          >
            <span slot="noResult">Oops! Nenhum element encontrado. Busque novamente.</span>
            <span slot="noOptions">Lista vazia</span>
          </multiselect>
        </b-col>
      </b-row>
    </div>
    <iframe class="col-md-12" src="https://datastudio.google.com/embed/reporting/8fa71982-e217-42a7-b1cd-a78e7ed8ba3e/page/UxgAC" frameborder="0" style="border:0" allowfullscreen></iframe>
  </div>
</template>

<script>
import NavBar from '../components/NavBar';
import Multiselect from 'vue-multiselect';

export default {
  components: {
    NavBar,
    Multiselect,
  },

  data() {
    return {
      selectedParticipants: [],
      selectedCategories: [],
      participants: [
        {
          "name": "Javascript",
          "code": "js"
        }
      ],
      categories: [],
    }
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css">
</style>