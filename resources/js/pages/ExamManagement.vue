<template>
  <div>
    <nav-bar></nav-bar>
      <div class="container">
        <h2>Avaliações</h2>
        <b-table
          striped
          hover
          :items="items"
          :fields="fields"
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
import Cookie from '../service/cookie';

const token = Cookie.getToken();

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
      fields: [
          {
            key: 'title',
            label: 'Avaliação',
            sortable: true,
          },
          {
            key: 'category_name',
            label: 'Categoria',
            sortable: true,
          },
          {
            key: 'creation_date',
            label: 'Data',
            sortable: true,
          },
          {
            key: 'published',
            label: 'Publicado',
            sortable: true,
          },
          {
            key: 'actions',
            label: 'Ações',
            sortable: true,
          }
      ],
    }
  },

  methods: {
    fetchExams() {
      axios.get('/api/exams', {
          headers: {
            Authorization: 'Bearer ' + token
          }
        })
        .then(response => {
          this.items = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
  },
}
</script>

<style>

</style>