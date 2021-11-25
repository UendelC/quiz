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
          <table class="table table-hover table-striped table-bordered">
            <caption>Lista de Avaliações</caption>
            <thead>
              <th scope="col">Avaliação</th>
              <th scope="col">Categoria</th>
              <th scope="col">Data</th>
              <th scope="col">Publicado?</th>
              <th scope="col">Ações</th>
            </thead>
            <tbody>
              <tr v-for="exam in items" :key="exam.id">
                <td>{{ exam.title }}</td>
                <td>{{ exam.category.name }}</td>
                <td>{{ exam.creation_date }}</td>
                <td>
                  <b-form-checkbox
                    :id="'exam'+ exam.id"
                    :name="exam.title"
                    :checked="isPublished(exam.published)"
                    :disabled="!exam.actions"
                    size='lg'
                    @change="togglePublished(exam)"
                  >
                  </b-form-checkbox>
                </td>
                <td>
                  <b-button size="sm" variant='success' v-b-modal.modal-1 @click="setExam(exam)">
                    <b-icon icon='eye'></b-icon>
                  </b-button>
                  <b-button
                    variant="primary"
                    @click="editExam(exam.id)"
                    size="sm"
                    :disabled="!exam.actions"
                  >
                    <b-icon icon='pencil-square'></b-icon>
                  </b-button>
                  <b-button variant="danger" @click="confirmDeletionExam(exam.id)" size="sm" :disabled="!exam.actions">
                    <b-icon icon="trash"></b-icon>
                  </b-button>
                </td>
              </tr>
            </tbody>
          </table>
          <b-modal id="modal-1" title="Avaliação" ok-only scrollable>
            <exam-preview :exam="selected"></exam-preview>
          </b-modal>
      </div>
  </div>
</template>

<script>
import NavBar from '../components/NavBar';
import ExamPreview from '../components/ExamPreview';
import Cookie from '../service/cookie';


export default {
  components: {
    NavBar,
    ExamPreview,
  },

  created() {
    this.token = Cookie.getToken();
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
            class: 'text-center',
          }
      ],
      token: '',
      selected: '',
    }
  },

  methods: {
    fetchExams() {
      axios.get('/api/exams', {
          headers: {
            Authorization: 'Bearer ' + this.token
          }
        })
        .then(response => {
          this.items = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },

    isPublished(value) {
      return value === true || value === '1';
    },

    info() {

    },

    setExam(exam) {
      this.selected = exam;
    },

    togglePublished(exam) {
      axios.patch(`/api/exams/${exam.id}`, {
          published: this.isPublished(exam.published) ? '0' : '1',
        },
        {
          headers: {
            Authorization: 'Bearer ' + this.token
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

    confirmDeletionExam(id) {
      this.$swal({
        title: 'Você tem certeza?',
        text: 'Você não poderá reverter isso!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Não, cancelar!',
        reverseButtons: true,
        confirmButtonColor: '#007BFF',
      }).then((result) => {
        if (result.value) {
          this.deleteExam(id);
        }
      });
    },

    deleteExam(exam_id) {
      axios.delete(`/api/exams/${exam_id}`, {
        headers: {
          Authorization: 'Bearer ' + this.token
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

  td {
    text-align: center;
    vertical-align: middle;
  }
</style>