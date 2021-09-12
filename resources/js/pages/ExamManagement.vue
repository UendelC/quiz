<template>
  <div>
    <nav-bar></nav-bar>
      <div class="container-lg">
        <div class="exam_header">
          <h2>Avaliações</h2>
          <b-button variant='primary' @click="handleCreate">
            Criar nova Avaliação
            <b-icon icon="plus"></b-icon>
            </b-button>
        </div>
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
          <template #cell(published)="row">
            <b-form-checkbox
              :id="'exam'+row.item.id"
              :name="row.item.title"
              :checked="row.item.published === '1'"
              :disabled="!row.item.actions"
              size='lg'
              @input="togglePublished(row)"
            >
            </b-form-checkbox>
          </template>

          <template #cell(actions)="row">
            <b-button size="sm" @click="row.toggleDetails" variant='success'>
              <b-icon icon='eye'></b-icon>
              {{ row.detailsShowing ? 'Esconder' : 'Mostrar' }} Detalhes
            </b-button>
            <b-button
              size='sm'
              variant='primary'
              :disabled="!row.item.actions"
              @click="editExam(row.item.id)"
            >
              <b-icon icon='pencil-square'></b-icon>
              Editar
            </b-button>
            <b-button
              size='sm'
              variant='danger'
              :disabled="!row.item.actions"
              @click="deleteExam(row.item.id)"
            >
              <b-icon icon='trash'></b-icon>
              Excluir
            </b-button>
          </template>

          <template #row-details="row">
            <b-card>
              <exam-preview :exam="row.item">
              </exam-preview>
            </b-card>
          </template>
        </b-table>
      </div>
  </div>
</template>

<script>
import NavBar from '../components/NavBar';
import ExamPreview from '../components/ExamPreview';
import Cookie from '../service/cookie';

const token = Cookie.getToken();

export default {
  components: {
    NavBar,
    ExamPreview,
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
            label: 'Publicado?',
            sortable: true,
            class: 'text-center',
          },
          {
            key: 'actions',
            label: 'Ações',
            sortable: true,
            class: 'text-center',
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

    info() {

    },

    togglePublished(row) {
      axios.patch(`/api/exams/${row.item.id}`, {
          published: row.item.published == '0' ? '1' : '0',
        },
        {
          headers: {
            Authorization: 'Bearer ' + token
          },
        }
      )
        .then(response => {
          this.fetchExams();
        })
        .catch(error => {
          console.log(error);
        });
    },

    deleteExam(exam_id) {
      axios.delete(`/api/exams/${exam_id}`, {
        headers: {
          Authorization: 'Bearer ' + token
        }
      })
      .then(response => {
        this.fetchExams();
      })
      .catch(error => {
        console.log(error);
      })
    },

    handleCreate() {
        this.$router.push({name: 'exam-create'});
    },

    editExam(exam_id) {
        this.$router.push({name: 'exam-create', params: {exam_id}});
    }
  },

}

</script>

<style scoped>
  .exam_header {
    display: flex;
    justify-content: space-between;
    padding-bottom: 15px;
  }
</style>