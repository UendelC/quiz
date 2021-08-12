<template>
  <div>
    <nav-bar></nav-bar>
    <h2>Avaliações</h2>
    <div class="container">
        <b-table
          striped
          hover
          :items="[]"
          :fields="[]"
          bordered
          sticky-header
          :busy="false"
          show-empty
          empty-text="Não há avaliações"
        >
          <template #empty="scope">
            <h4>{{ scope.emptyText }}</h4>
          </template>
        </b-table>
      </div>
  </div>
</template>

<script>
import NavBar from '../components/NavBar';

export default {
  components: {
    NavBar,
  },

  created() {
    this.fetchExams();
  },

  data() {
    return {
      items: [],
      fields: [],
    }
  },

  methods: {
    fetchExams() {
      axios.get('/api/exams')
        .then(response => {
          this.items = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
  },
</script>

<style>

</style>