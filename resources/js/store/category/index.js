import axios from 'axios'

const state = {
  categories: [],
}

const getters = {
  allCategories: (state) => state.categories
}

const actions = {
  getCategories({commit}) {
    axios.get('api/categories', {
			headers: {
				Authorization: 'Bearer ' + token
			}
		 }).then(response => {
      commit('SET_CATEGORIES', response.data);
    });
  }
}

const mutations = {
  SET_CATEGORIES(state, categories) {
    state.categories = categories;
  }
}

export default {
    state,
    getters,
    actions,
    mutations
}