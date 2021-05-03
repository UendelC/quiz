import Vue from 'vue';
import router from './router.js';
import App from './components/App';
import bootstrap from './bootstrap-components';
import store from './store';

require('./bootstrap');

const app = new Vue({
    el: '#app',
    components: {
        App,
    },
    store,
    router,
});
