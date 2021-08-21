<template>
  <div>
    <nav-bar></nav-bar>
    <div class="container">
      <b-row>
        <b-col>
          <label class="typo__label">Participantes</label>
          <multiselect
            v-model="selectedParticipants"
            placeholder="Selecione Participantes"
            label="name"
            track-by="code"
            :options="participants"
            :multiple="true"
            :taggable="false"
            select-label=""
            selected-label=""
            deselect-label=""
            noResult="Nenhum elemento encontrado. Busque novamente"
            :loading="false"
            :disabled="false"
            @input="renderReport()"
          >
            <span slot="noResult">Oops! Nenhum element encontrado. Busque novamente.</span>
            <span slot="noOptions">Lista vazia</span>
          </multiselect>
        </b-col>
        <b-col>
          <label class="typo__label">Categorias</label>
          <multiselect
            v-model="selectedCategories"
            placeholder="Selecione Categorias"
            label="name"
            track-by="code"
            :options="categories"
            :multiple="true"
            :taggable="false"
            select-label=""
            selected-label=""
            deselect-label=""
            noResult="Nenhum elemento encontrado. Busque novamente"
            :loading="false"
            :disabled="false"
            @input="renderReport()"
          >
            <span slot="noResult">Oops! Nenhum element encontrado. Busque novamente.</span>
            <span slot="noOptions">Lista vazia</span>
          </multiselect>
        </b-col>
        <b-col>
          <label class="typo__label">Avaliações</label>
          <multiselect
            v-model="selectedExams"
            placeholder="Selecione Avaliações"
            label="name"
            track-by="code"
            :options="exams"
            :multiple="true"
            :taggable="false"
            select-label=""
            selected-label=""
            deselect-label=""
            noResult="Nenhum elemento encontrado. Busque novamente"
            :loading="false"
            :disabled="false"
            @input="renderReport()"
          >
            <span slot="noResult">Oops! Nenhum element encontrado. Busque novamente.</span>
            <span slot="noOptions">Lista vazia</span>
          </multiselect>
        </b-col>
        <b-col>
          <label class="typo__label">Período</label>
          <flat-pickr
            v-model="date"
            :config="config"
            class="form-control"
            placeholder="Selecione o período"
            name="date"
          >

          </flat-pickr>
        </b-col>
      </b-row>
    </div>
    <!-- <iframe class="col-md-12" src="https://datastudio.google.com/embed/reporting/8fa71982-e217-42a7-b1cd-a78e7ed8ba3e/page/UxgAC" frameborder="0" style="border:0" allowfullscreen></iframe> -->
  </div>
</template>

<script>
import NavBar from '../components/NavBar';
import Multiselect from 'vue-multiselect';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import { Portuguese } from "flatpickr/dist/l10n/pt.js";
import Cookie from '../service/cookie';

const token = Cookie.getToken();

export default {
  components: {
    NavBar,
    Multiselect,
    flatPickr,
  },

  data() {
    return {
      selectedParticipants: [],
      selectedCategories: [],
      selectedExams: [],
      participants: [
        {
          "name": "Javascript",
          "code": "js"
        }
      ],
      categories: [],
      exams: [],
      date: '',
      config: {
        dateFormat: 'Y-m-d',
        mode: 'range',
        altInput: true,
        locale: Portuguese,
      },
    }
  },

  created() {
    this.getCategories();
    this.getParticipants();
    this.getExams();
  },

  methods: {
    getCategories() {
        axios.get('api/categories-from-teacher', {
          headers: {
            Authorization: 'Bearer ' + token
          },
        }).then( response => {
          let categories = response.data.data.map(item => {
            return { name: item.name, code: item.id};
          });

          this.categories = [...categories];
        });
    },

    getParticipants() {
      axios.get('api/participants-from-teacher', {
          headers: {
            Authorization: 'Bearer ' + token
          },
        }).then( response => {
          let participants = response.data.data.map(item => {
            return { name: item.name, code: item.id};
          });

          this.participants = [...participants];
        });
    },

    getExams() {
      axios.get('api/exams-from-teacher', {
          headers: {
            Authorization: 'Bearer ' + token
          },
        }).then( response => {
          let exams = response.data.map(item => {
            return { name: item.title, code: item.id};
          });

          this.exams = [...exams];
        });
    },

    renderReport() {
      let data = {};

      if (this.selectedParticipants.length > 0) {
        data.participants = this.selectedParticipants.map(item => item.code);
      }

      if (this.selectedCategories.length > 0) {
        data.categories = this.selectedCategories.map(item => item.code);
      }

      if (this.selectedExams.length > 0) {
        data.exams = this.selectedExams.map(item => item.code);
      }

      if (this.date.length > 0) {
        data.date = this.date[0];
      }

      axios.post('api/report', data, {
        headers: {
          Authorization: 'Bearer ' + token
        },
      }).then( response => {
        this.report = response.report;
      });
    },
  },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css">
</style>
