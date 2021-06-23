<template>
  <div>
    <nav-bar></nav-bar>
    <div class="quiz-container">
        <div class="quiz-header">
            <h2 id="question">Question text</h2>
            <ul>
                <li>
                    <input type="radio" id="a" name="answer" class="answer">
                    <label id="a_text" for="a">Question</label>
                </li>
                <li>
                    <input type="radio" id="b" name="answer" class="answer">
                    <label id="b_text" for="b">Question</label>
                </li>
                <li>
                    <input type="radio" id="c" name="answer" class="answer">
                    <label id="c_text" for="c">Question</label>
                </li>
                <li>
                    <input type="radio" id="d" name="answer" class="answer">
                    <label id="d_text" for="d">Question</label>
                </li>
            </ul>
        </div>
        <button id="submit">Submit</button>
    </div>
  </div>
</template>

<script>
import NavBar from '../components/NavBar';
export default {
  components: {
    NavBar
  },
  
  data() {
    exam: []
  },

  methods: {
    getExam() {
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
  },
}
</script>

<style scoped>
  * {
    box-sizing: border-box;
}

body {
    background-color: #b8c6db;
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
    padding: 4rem;
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

button {
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

button:hover {
    background-color: #732d91;
}
</style>