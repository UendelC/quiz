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
            v-model="dates"
            :config="config"
            class="form-control"
            placeholder="Selecione o período"
            name="date"
          >

          </flat-pickr>
        </b-col>
      </b-row>
      <b-row class='pt-4'>
        <b-col>
          <cds-totalizer
            variant="green"
            iconSide="right"
          >
            <template slot="icon">
              <b-icon></b-icon>
            </template>
            <template slot="subtitle">
              Nota média
            </template>
            <template slot="value">
              {{ report.mean_score }}
            </template>
          </cds-totalizer>
        </b-col>
        <b-col>
          <cds-totalizer
            variant="green"
            iconSide="right"
          >
            <template slot="icon">
              <b-icon icon="plus"></b-icon>
            </template>
            <template slot="subtitle">
              Nota máxima
            </template>
            <template slot="value">
              {{ maxScore }}
            </template>
          </cds-totalizer>
        </b-col>
        <b-col>
          <cds-totalizer
            variant="green"
            iconSide="right"
          >
            <template slot="icon">
              <b-icon icon="minus"></b-icon>
            </template>
            <template slot="subtitle">
              Nota mínima
            </template>
            <template slot="value">
              {{ minScore }}
            </template>
          </cds-totalizer>
        </b-col>
        <b-col>
          <cds-totalizer
            variant="green"
            iconSide="right"
          >
            <template slot="icon">
              <b-icon icon="person-fill"></b-icon>
            </template>
            <template slot="subtitle">
              Número de Avaliações
            </template>
            <template slot="value">
              {{ report.scores.length }}
            </template>
          </cds-totalizer>
        </b-col>
        <b-col>
          <cds-totalizer
            variant="green"
            iconSide="right"
          >
            <template slot="icon">
              <b-icon icon="graph-up"></b-icon>
            </template>
            <template slot="subtitle">
              Desvio Padrão
            </template>
            <template slot="value">
              {{ report.standard_deviation }}
            </template>
          </cds-totalizer>
        </b-col>
      </b-row>
    </div>
    <canvas id="report-chart"></canvas>
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
import Chart from 'chart.js';

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
      dates: [],
      config: {
        dateFormat: 'Y-m-d',
        mode: 'range',
        altInput: true,
        locale: Portuguese,
      },
      report: {
        mean_score: 0,
        scores: [],
        standard_deviation: 0,
      },
    }
  },

  created() {
    this.getCategories();
    this.getParticipants();
    this.getExams();
    this.renderReport();
  },

  computed: {
    maxScore() {
      let notes = this.report.scores.map(score => score[0]);
      return Math.max(...notes);
    },

    minScore() {
      let notes = this.report.scores.map(score => score[0]);
      return Math.min(...notes);
    }
  },

  watch: {
    dates: function(newVal, oldVal) {
      this.renderReport();
    },
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

    renderChart() {
      let notes = this.report.scores.map(score => score[0]);
      let dates = this.report.scores.map(score => score[1]);
      const ctx = document.getElementById('report-chart').getContext('2d');
      const chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: dates,
          datasets: [{
            label: 'Notas',
            data: notes,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1,
          }],
        },
        // options: this.options
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

      if (this.dates.length > 0) {
        let datesFormatted = this.dates.replace(' até ', ',');
        datesFormatted = datesFormatted.split(',');
        data.start_date = datesFormatted[0];
        data.end_date = datesFormatted[1];
      }

      axios.post('api/report', data, {
        headers: {
          Authorization: 'Bearer ' + token
        },
      }).then( response => {
        this.report = response.data;
        this.renderChart();
      });
    },
  },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css">
</style>
