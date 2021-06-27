<template>
  <div>
    <nav-bar></nav-bar>
    <div class="row justify-content-center">
      <div class="quiz-container">
          <b-form @submit="handleSubmit" v-if="exam.questions[questionIndex]">
            <div class="quiz-header">
              <h2>{{ exam.questions[questionIndex].title }}</h2>
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
            <b-button type="submit" class="button-exam">Submit</b-button>
          </b-form>
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
    }
  },

  computed: {
    options() {
      return this.exam.questions[this.questionIndex].choices.map( item => { return {text: item.description, value: item.id} });
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
          this.exam = response.data.data;
        });
    },

    handleCheck(itemId) {
      this.selected = itemId;
    },

    advanceQuestion() {
      if (this.questionIndex < this.exam.questions.length - 1) {
        this.questionIndex++;
        return false;
      }

      return true;
    },

    handleSubmit(event) {
      event.preventDefault();
      this.valid = this.selected.length === 1;
      const finalQuestion = this.advanceQuestion();
      if (finalQuestion) {
        axios.post('api/take-exam', {
          headers: {
            Authorization: 'Bearer ' + token
          }
        })
        .then( response => {
          this.exam = response.data.data;
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
    width: 600px;
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
    background-color: #8e44ad;
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
    background-color: #732d91;
}
</style>