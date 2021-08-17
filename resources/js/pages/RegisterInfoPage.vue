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
              <b-form-input v-model="choice.description"></b-form-input>
              <b-form-checkbox v-model="choice.is_right">Marque se a opção for a correta</b-form-checkbox>
              <b-button @click="removeChoice(index)" variant='danger'>
                <b-icon icon='trash'></b-icon>
              </b-button>
            </b-form-group>
          </div>
          <b-button @click="addChoiceField()">Adicionar Alternativa</b-button>
        </b-form-group>

        <div class='button-box'>
          <b-button @click="addNewQuestion()">Cadastrar Questão</b-button>
          <b-button type="reset" variant="danger">Cancelar</b-button>
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
      <b-button type="submit" variant="primary">Finalizar Cadastro de Avaliação</b-button>
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
        currentQuestionIndex: 1,
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
        if (true) {
          console.log(newValue);
        }
        // this.currentQuestion = this.form.questions[newValue];
      },
    },

    methods: {
      handleOk() {
        this.options.push({
          text: this.newCategory,
          value: this.newCategory,
        });
      },

      onSubmit(event) {
        event.preventDefault();

        if (this.form.category) {
          this.showCategory = false;
        }

        let aux = Object.assign({}, this.currentQuestion);
        this.form.questions.push(aux);

        if (this.form.questions.length >= 1) {
          axios.post('api/exams', this.form, {
            headers: {
              Authorization: 'Bearer ' + token
            }
          }).then( response => {
            this.$swal('Pergunta cadastrada com sucesso');
            this.resetForm();
            this.form.questions = [];
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
        this.currentQuestion = this.form.questions[idx];
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
          this.$swal('Você deve cadastrar ao menos duas alternativas');
          fail = true;
        }

        if (this.currentQuestion.choices.some(item => item.description.length === 0)) {
          this.$swal('Todas as alternativas devem ter seus textos');
          fail = true;
        }

        const amountOfRightChoices = this.currentQuestion.choices.reduce((accumulator, currentValue) => {
          return accumulator + (currentValue.is_right ? 1 : 0);
          }, 0
        );

        if (amountOfRightChoices === 0) {
          this.$swal('Ao menos uma alternativa deve estar correta');
          fail = true;
        }

        if (amountOfRightChoices > 1) {
          this.$swal('Apenas uma alternativa deve ser correta');
          fail = true;
        }

        if (!fail) {
          let aux = Object.assign({}, this.currentQuestion);
          this.form.questions.push(aux);
          this.currentQuestionIndex++;
          this.questionsSize++;
          this.resetForm();
        }
      },

      removeChoice(index) {
        this.currentQuestion.choices.splice(index, 1);
      },

      mountExam(exam_id) {
        axios.get(`api/exams/${exam_id}`, {
          headers: {
            Authorization: 'Bearer' + token
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
          console.log(questions);
          this.form.questions = questions;
          this.currentQuestion = questions[0];
        })
      },

      getCategories() {
        axios.get('api/categories', {
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
  }

  .question-body {
    background-color: cadetblue;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 10px;
  }

  .pagination-hand {
    display: flex;
    justify-content: center;
  }
</style>