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
        <b-form-input
          id="input-1"
          v-model="form.category"
          type="select"
          placeholder="Categoria"
          required
        ></b-form-input>
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
            <b-form-checkbox v-model="choice.is_right"></b-form-checkbox>
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
        letterIndex: 0,
      }
    },

    components: {
      NavBar,
    },

    methods: {
      onSubmit(event) {
        event.preventDefault()
        alert(JSON.stringify(this.form))
      },
      onReset(event) {
        event.preventDefault()
        // Reset our form values
        this.form.category = ''
        this.form.question = ''
        this.form.explanation = null
        // Trick to reset/clear native browser form validation state
        this.show = false
        this.$nextTick(() => {
          this.show = true
        })
      },

      addChoiceField() {
        this.form.choices.push({
          name: '',
          is_right: false,
          text: '',
        });

        this.letterIndex++;
      },

      removeChoice(index) {
        this.form.choices.splice(index, 1);
      }
    }
  }
</script>

<style>

</style>