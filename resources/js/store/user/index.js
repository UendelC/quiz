export default {
    namespaced: true,

    state: () => ({
        user: {},
    }),

    mutations: {
        STORE_USER(state, user) {
            state.user = user;
        },

        CLEAR_USER(state) {
            state.user = {};
        },
    },

    actions: {},
}