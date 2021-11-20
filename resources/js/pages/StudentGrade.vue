<template>
  <div>
    <nav-bar></nav-bar>
    <div class="container">
      <b-table
        striped
        hover
        :items="items"
        :fields="fields"
        bordered
        sticky-header
        :busy="loading"
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
import Cookie from '../service/cookie';

export default {
  components: {
    NavBar
  },

  created() {
    this.token = Cookie.getToken();
    this.loading = true;
    this.getGrades();
  },

  data() {
    return {
      loading: true,
      items: [],
      fields: [
          {
            key: 'category_name',
            label: 'Categoria',
            sortable: true,
          },
          {
            key: 'date',
            label: 'Data',
            sortable: true
          },
          {
            key: 'score',
            label: 'Nota',
            sortable: true
          }
      ],
      token: '',
    }
  },

  methods: {
    getGrades() {
      axios.get('api/grades', {
          headers: {
            Authorization: 'Bearer ' + this.token
          }
        })
        .then( response => {
          this.loading = false;
          this.items = response.data.exams;
        });
    }
  }
}
</script>

<style>

</style>