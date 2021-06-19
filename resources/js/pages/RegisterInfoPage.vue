<template>
<div>
  <nav-bar></nav-bar>
  <div class="container">
    <b-form @submit="onSubmit" @reset="onReset" v-if="show">
      <b-form-group
        id="input-group-1"
        label="Categoria:"
        label-for="input-1"
      >
        <b-form-select v-model="form.category" :options="options"></b-form-select>
      </b-form-group>

      <b-form-group id="input-group-2" label="Enunciado da Questão:" label-for="input-2">
        <b-form-input
          id="input-2"
          v-model="form.question"
          placeholder="Digite o enunciado"
          required
        ></b-form-input>
      </b-form-group>

      <b-form-group id="input-group-3" label="Explicação da Questão:" label-for="input-3">
        <b-form-input
          id="input-3"
          v-model="form.explanation"
          placeholder="Explicação da questão"
          required
        ></b-form-input>
      </b-form-group>

      <b-form-group id="input-group-4" label="Alternativas:" label-for="input-4">
        <div v-for="(choice, index) in form.choices" :key="index">
          <b-form-group label="Opção:">
            <b-form-input v-model="choice.text"></b-form-input>
            <b-form-checkbox v-model="choice.is_right">Marque se a opção for a correta</b-form-checkbox>
            <b-button @click="removeChoice(index)">X</b-button>
          </b-form-group>
        </div>
        <b-button @click="addChoiceField()">Adicionar Alternativa</b-button>
      </b-form-group>

      <b-button type="submit" variant="primary">Submit</b-button>
      <b-button type="reset" variant="danger">Reset</b-button>
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
          category: '',
          question: '',
          explanation: '',
          choices: [],
        },
        show: true,
        options: [],
      }
    },

    components: {
      NavBar,
    },

    created() {
      this.getCategories();
    },

    methods: {
      onSubmit(event) {
        event.preventDefault()
        alert(JSON.stringify(this.form))
        if (form.choices.length > 0) {

        }
      },
      onReset(event) {
        event.preventDefault()
        // Reset our form values
        this.form.category = ''
        this.form.question = ''
        this.form.explanation = null
        this.form.choices = [];
        // Trick to reset/clear native browser form validation state
        this.show = false
        this.$nextTick(() => {
          this.show = true
        })
      },

      addChoiceField() {
        this.form.choices.push({
          is_right: false,
          text: '',
        });
      },

      removeChoice(index) {
        this.form.choices.splice(index, 1);
      },

      getCategories() {
        axios.get('api/categories', {
          headers: {
            Authorization: 'Bearer ' + token
          }
        }).then( response => {
          this.options = response.data.data.map(item => {
            return { text: item.name, value: item.id};
          });
        });
      }
    }
  }
</script>

<style>

</style>