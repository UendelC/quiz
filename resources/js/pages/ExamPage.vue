<template>
  <div>
    <nav-bar></nav-bar>
    <div class="row justify-content-center pt-3">
      <div class="quiz-container" v-if="exam.questions.length > 0 && !dismissAlert">
          <b-form @submit="handleSubmit" v-if="!processExam">
            <div class="quiz-header">
              <h2>{{ title }}</h2>
              <b-form-checkbox-group
                v-model="selected"
                :options="options"
                :state="valid"
                stacked
                class="answer"
                name="checkbox-validation"
              >
                <b-form-invalid-feedback :state="valid">Selecione uma questão</b-form-invalid-feedback>
                <b-form-valid-feedback :state="valid">Resposta cadastrada com sucesso!</b-form-valid-feedback>
              </b-form-checkbox-group>
            </div>
            <b-button type="submit" class="button-exam" :disabled="processExam">Responder</b-button>
          </b-form>
          <div class="text-center" v-if="processExam">
            <b-spinner variant="primary" label="Text Centered"></b-spinner>
          </div>
      </div>
      <div v-else-if="!loading && !dismissAlert || noExams">
        <cds-empty-state
          empty-state-image="https://img.freepik.com/free-vector/night-robot-with-flashlight-space-signboard-404-error-cute-illustration-error-page-404-found_138353-33.jpg?size=626&ext=jpg"
          title="Não há provas cadastradas"
          text="Novas provas aparecerão aqui assim que habilitadas pelo professor"
          :show-action-button="false"
          img-description="Imagem de que não há vagas"
        />
      </div>
      <div class="container">
        <div class="justify-content-center pt-3">
          <b-alert :show="dismissAlert" variant="success" fade>
            <h4 class="alert-heading">Parabéns!</h4>
            <p>
              Você finalizou a avaliação, em breve sua nota estará disponível na aba de <a href="/student-grades">boletim</a>.
            </p>
            <hr>
            <p class="mb-0">
              Novas Provas estarão disponíveis em Realizar exame.
            </p>
          </b-alert>
        </div>
      </div>
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
    this.getExam();
  },

  data() {
    return {
      exam: {
        questions: {
          id: '',
          title: '',
          choices: {
            id: '',
            description: ''
          }
        },
        exam_id: '',
      },
      questionIndex: 0,
      selected: [],
      valid: null,
      answers: [],
      loading: '',
      dismissAlert: false,
      processExam: false,
      noExams: false,
    }
  },

  computed: {
    options() {
      return this.exam.questions[this.questionIndex].choices.map( item => { return {text: item.description, value: item.id} });
    },

    title() {
      return this.exam.questions.length > 0 ? this.exam.questions[this.questionIndex].title : '';
    }
  },

  methods: {
    getExam() {
      axios.get('api/exams', {
          headers: {
            Authorization: 'Bearer ' + token
          }
        })
        .then( response => {
          if (response.data.message) {
            this.noExams = true;
          } else {
            this.loading = false;
            this.exam = response.data.data;
          }
        });
    },

    handleCheck(itemId) {
      this.selected = itemId;
    },

    advanceQuestion() {
      this.answers.push(this.selected.pop());
      this.valid = null;
      if (this.questionIndex < this.exam.questions.length - 1) {
        this.questionIndex++;
        return false;
      }

      return true;
    },

    handleSubmit(event) {
      event.preventDefault();
      this.valid = this.selected.length === 1;
      let finalQuestion = false;
      if (this.valid) {
        finalQuestion = this.advanceQuestion();
      }
      if (finalQuestion) {
        this.processExam = true;
        axios.post(
          'api/takeexam',
          {
            exam_id: this.exam.exam_id,
            answers: this.answers,
          },
          {
            headers: {
              Authorization: 'Bearer ' + token
            },
          }
        )
        .then( response => {
          this.valid = true;
          this.dismissAlert = true;
          this.processExam = false;
        });
      }
    }
  },
}
</script>

<style scoped>
  * {
    box-sizing: border-box;
}

body-component {
    background-color: #0408;
    background-image: linear-gradient(315deg, #b8c6db, #f5f7fa, 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: "Poppins", "sans-serif";
    margin: 0;
    min-height: 100vh;
}

.quiz-container {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 0 10px 2px rgba(100, 100, 100, 0.1);
    overflow: hidden;
    width: 750px;
    max-width: 100%;
}

.quiz-header {
    padding: 2rem;
}

.answer {
  padding-top: 1.5rem;
}

h2 {
    margin: 0;
}

ul {
    list-style-type: none;
    padding: 0;
}

ul li {
    font-size: 1.2rem;
    margin: 1rem 0;
}

ul li label {
    cursor: pointer;
}

.button-exam {
    background-color: #005b96;
    border: none;
    color: white;
    display: block;
    cursor: pointer;
    font-family: inherit;
    font-size: 1.1rem;
    width: 100%;
    padding: 1.3rem;
}

.button-exam:hover {
    background-color: #02416b;
}
</style>