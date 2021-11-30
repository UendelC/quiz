<template>
<div>
  <nav-bar></nav-bar>
  <div class="container">
    <b-form @submit="onSubmit" @reset="onReset" v-if="show">
      <b-form-group id="input-group-0" label="Título da Avaliação" label-for="input-0">
        <b-form-input
          id="input-0"
          v-model="form.title"
          placeholder="Digite o título"
          required
        ></b-form-input>
      </b-form-group>

      <b-form-group
        id="input-group-1"
        label="Categoria:"
        label-for="input-1"
      >
        <b-form-group>
          <b-form-row>
            <b-form-select
              :plain="true"
              v-model="form.category"
              :options="options"
              class="mb-3"
            >
            </b-form-select>
            <b-button v-b-modal.modal-categoria>
              Cadastrar nova categoria
            </b-button>

          </b-form-row>
          <b-modal id="modal-categoria" title="Cadastre uma nova categoria" @ok="handleOk">
            <b-form-group label="Nome da categoria">
              <b-form-input v-model="newCategory" placeholder="Digite uma categoria">
              </b-form-input>
            </b-form-group>
          </b-modal>
        </b-form-group>
      </b-form-group>
      <div class='question-body'>
        <b-form-group id="input-group-2" label="Enunciado da Questão:" label-for="input-2">
          <b-form-input
            id="input-2"
            v-model="currentQuestion.title"
            placeholder="Digite o enunciado"
            required
          ></b-form-input>
        </b-form-group>

        <b-form-group id="input-group-3" label="Explicação da Questão:" label-for="input-3">
          <b-form-input
            id="input-3"
            v-model="currentQuestion.explanation"
            placeholder="Explicação da questão"
            required
          ></b-form-input>
        </b-form-group>

        <b-form-group id="input-group-4" label="Alternativas:" label-for="input-4">
          <div v-for="(choice, index) in currentQuestion.choices" :key="index">
            <b-form-group label="Opção:">
              <div class="d-flex align-items-center justify-content space-between">
                <b-form-input v-model="choice.description" class="mr-2"></b-form-input>
                <b-button @click="removeChoice(index)" variant='danger'>
                  <b-icon icon='trash'></b-icon>
                </b-button>
              </div>
              <b-form-checkbox v-model="choice.is_right">Correta?</b-form-checkbox>
            </b-form-group>
          </div>
          <b-button @click="addChoiceField()">Adicionar Alternativa</b-button>
        </b-form-group>

        <div class='button-box'>
          <b-button @click="addNewQuestion()" v-if="!disableNewQuestion">Cadastrar Questão</b-button>
          <b-button @click="changeQuestion(form.questions.length)" v-else>Cadastrar Nova Questão</b-button>
          <b-button type="reset" variant="danger" @click="removeQuestion()" class="ml-2">Remover Questão</b-button>
        </div>
      </div>
      <div class="pagination-hand">
        <b-button
          variant="outline-primary"
          pill
          v-for="question, idx in form.questions" :key="question.id"
          class="m-1"
          @click="changeQuestion(idx)"
        >
          {{ idx + 1 }}
        </b-button>
      </div>
      <b-button @click="onSubmit" variant="primary">Finalizar Cadastro de Avaliação</b-button>
    </b-form>
  </div>
</div>
</template>

<script>
import NavBar from '../components/NavBar';
import axios from 'axios';
import Cookie from '../service/cookie';

const token = Cookie.getToken();

  export default {
    data() {
      return {
        form: {
          title: '',
          category: '',
          questions: [],
          // category: '',
          // question: '',
          // explanation: '',
          // choices: [],
        },
        currentQuestion: {
          title: '',
          explanation: '',
          choices: [],
        },
        show: true,
        showCategory: true,
        options: [{
          value: '',
          text: 'Selecione uma categoria',
        }],
        exam: [],
        newCategory: '',
        currentQuestionIndex: 0,
        questionsSize: 1,
      }
    },

    components: {
      NavBar,
    },

    created() {
      this.getCategories();
      if (this.$route.params.exam_id) {
        this.mountExam(this.$route.params.exam_id);
      }
    },

    watch: {
      currentQuestionIndex(newValue) {
        // this.disableNewQuestion = newValue !== this.form.questions.length;
      },
    },

    computed: {
      disableNewQuestion() {
        return this.currentQuestionIndex !== this.form.questions.length;
      },
    },

    methods: {
      handleOk() {
        this.options.push({
          text: this.newCategory,
          value: this.newCategory,
        });

        this.form.category = this.newCategory;
      },

      onSubmit(event) {
        event.preventDefault();

        if (this.form.category) {
          this.showCategory = false;
        }

        if (this.currentQuestion.choices.length > 0) {
          this.$swal(
            {
              text: 'Questão escrita mas não cadastrada!',
              icon: 'warning',
              title: 'Aviso',
              confirmButtonColor: '#007BFF',
            }
          );
          return;
        }

        if (this.form.questions.length >= 1) {
          if (this.$route.params.exam_id) {
            let form = this.form;
            axios.patch(`/api/exams/${this.$route.params.exam_id}`, {form} , {
              headers: {
                Authorization: `Bearer ${token}`,
              },
            },
            ).then(response => {
              this.$swal({
                title: 'Sucesso!',
                text: 'Avaliação atualizada com sucesso!',
                icon: 'success',
                confirmButtonColor: '#007BFF',
              }).then(() => {
                this.$router.push({name: 'exam-management'});
              });
            });
          } else {
            axios.post('api/exams', this.form, {
              headers: {
                Authorization: 'Bearer ' + token
              }
            }).then( response => {
              this.resetForm();
              this.form.questions = [];
              this.$swal(
                {
                  title: 'Avaliação cadastrada com sucesso',
                  icon: 'success',
                  confirmButtonColor: '#007BFF',
                }).then( () => {
                  this.$router.push({name: 'exam-management'});
                });
            });
          }
        } else {
          this.$swal({
            icon: 'error',
            title: 'Cadastro inválido',
            text: 'Você deve adicionar pelo menos uma pergunta',
            confirmButtonColor: '#007BFF',
          });
        }
      },

      onReset(event) {
        event.preventDefault()
        this.resetForm();
      },

      resetForm() {
        this.currentQuestion.title = '';
        this.currentQuestion.explanation = null
        this.currentQuestion.choices = [];
        // Trick to reset/clear native browser form validation state
        this.show = false
        this.$nextTick(() => {
          this.show = true
        })
      },

      changeQuestion(idx) {
        if (idx === this.form.questions.length) {
          this.currentQuestion = {
            title: '',
            explanation: '',
            choices: [],
          };
          this.currentQuestionIndex = idx;

          this.$swal.mixin(
            {
              toast: true,
              position: 'bottom-right',
              showConfirmButton: false,
              timer: 3000,
            }
          ).fire({
            icon: 'success',
            title: 'Pergunta salva com sucesso',
          });
        } else {
          this.currentQuestionIndex = idx;
          this.currentQuestion = this.form.questions[idx];
        }
      },

      addChoiceField() {
        this.currentQuestion.choices.push({
          is_right: false,
          description: '',
        });
      },

      addNewQuestion() {
        let fail = false;

        if (this.form.category) {
          this.showCategory = false;
        }

        if (this.currentQuestion.choices.length <= 1) {
          this.$swal({
            text: 'Você deve cadastrar ao menos duas alternativas',
            icon: 'error',
            title: 'Cadastro inválido',
            confirmButtonColor: '#007BFF',
          });
          fail = true;
        }

        if (this.currentQuestion.choices.some(item => item.description.length === 0)) {
          this.$swal({
            text: 'Alternativas não podem ficar em branco',
            icon: 'error',
            title: 'Cadastro inválido',
            confirmButtonColor: '#007BFF',
          });
          fail = true;
        }

        const amountOfRightChoices = this.currentQuestion.choices.reduce((accumulator, currentValue) => {
          return accumulator + (currentValue.is_right ? 1 : 0);
          }, 0
        );

        if (amountOfRightChoices === 0) {
          this.$swal({
            text: 'Ao menos uma alternativa deve estar correta',
            icon: 'error',
            title: 'Cadastro inválido',
            confirmButtonColor: '#007BFF',
          });
          fail = true;
        }

        if (amountOfRightChoices > 1) {
          this.$swal({
            text: 'Apenas uma alternativa deve estar correta',
            icon: 'error',
            title: 'Cadastro inválido',
            confirmButtonColor: '#007BFF',
          });
          fail = true;
        }

        if (!fail) {
          let aux = Object.assign({}, this.currentQuestion);
          this.form.questions.push(aux);
          this.currentQuestionIndex = this.form.questions.length;

          this.$swal.mixin(
            {
              toast: true,
              position: 'bottom-right',
              showConfirmButton: false,
              timer: 3000,
            }
          ).fire({
            icon: 'success',
            title: 'Pergunta cadastrada com sucesso',
          });
          this.resetForm();
        }
      },

      removeQuestion() {
        let removed = this.form.questions.splice(this.currentQuestionIndex, 1);
        this.currentQuestionIndex = this.form.questions.length;

        if (removed.length > 0) {
          this.$swal.mixin(
            {
              toast: true,
              position: 'bottom-right',
              showConfirmButton: false,
              timer: 3000,
            }
          ).fire({
            icon: 'info',
            title: 'Pergunta removida',
          });
        } else {
          this.$swal({
            icon: 'error',
            title: 'Remoção inválida',
            text: 'Não há questão selecionada para remoção',
            confirmButtonColor: '#007BFF',
          });
        }
      },

      removeChoice(index) {
        this.currentQuestion.choices.splice(index, 1);
      },

      mountExam(exam_id) {
        axios.get(`api/exams/${exam_id}`, {
          headers: {
            Authorization: 'Bearer ' + token
          }
        }).then( response => {
          this.form.title = response.data.data.title;
          this.form.category = response.data.data.category.id;
          let questions = response.data.data.questions.map(question => {
            let choices = question.choices.map(choice => {
              choice.is_right = choice.is_right == '1' ? true : false;
              return choice;
            });
            question.choices = choices;
            return question;
          });
          this.form.questions = questions;
        })
      },

      getCategories() {
        axios.get('api/categories-from-teacher', {
          headers: {
            Authorization: 'Bearer ' + token
          }
        }).then( response => {
          let categories = response.data.data.map(item => {
            return { text: item.name, value: item.id};
          });

          this.options = [...this.options, ...categories];
        });
      }
    }
  }
</script>

<style scope>
  .button-box {
    display: flex;
    justify-content: center;
  }

  .question-body {
    background-color: #C4E1FD;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 10px;
  }

  .pagination-hand {
    display: flex;
    justify-content: center;
  }
</style>