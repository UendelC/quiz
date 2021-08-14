<template>
  <b-card
    no-body
  >
    <template #header>
      <h4 class='mb-0'>{{ exam.title }}</h4>
    </template>

    <b-card-body>
      <div v-for="question in exam.questions" :key="question.id">
        <h4>{{ question.title }}</h4>
        <ul>
          <li v-for="choice in question.choices" :key="choice.id">
            {{ choice.description }}
          </li>
        </ul>
      </div>
    </b-card-body>
  </b-card>
</template>

<script>
export default {
  props: {
    exam: {
      type: Object,
      required: true,
      default: () => {},
    }
  },
}
</script>

<style scoped>

</style>