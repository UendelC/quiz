import Vue from 'vue';
import router from './router.js';
import App from './components/App';
import vuetify from './vuetify';
import store from './store';

require('./bootstrap');

const app = new Vue({
    el: '#app',
    vuetify,
    components: {
        App,
    },
    store,
    router,
});
