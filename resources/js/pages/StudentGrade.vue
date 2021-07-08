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
      ></b-table>
    </div>
  </div>
</template>

<script>
import NavBar from '../components/NavBar';
import Cookie from '../service/cookie';

const token = Cookie.getToken();

export default {
  components: {
    NavBar
  },

  created() {
    this.loading = true;
    this.getGrades();
  },

  data() {
    return {
      items: [],
      fields: [
          {
            key: 'created_at',
            label: 'Data',
            sortable: true
          },
          {
            key: 'score',
            label: 'Nota',
            sortable: true
          }
      ],
    }
  },

  methods: {
    getGrades() {
      axios.get('api/grades', {
          headers: {
            Authorization: 'Bearer ' + token
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