<template>
  <div>
    <nav-bar></nav-bar>
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
}
</script>

<style>

</style>