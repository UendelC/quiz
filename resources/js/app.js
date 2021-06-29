import Vue from 'vue';
import router from './router.js';
import App from './components/App';
import bootstrap from './bootstrap-components';
import store from './store';
import Cuida from '@sysvale/cuida';

require('./bootstrap');

Vue.use(Cuida);

const app = new Vue({
    el: '#app',
    components: {
        App,
    },
    store,
    router,
});
