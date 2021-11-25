import Vue from 'vue';
import router from './router.js';
import App from './components/App';
import bootstrap from './bootstrap-components';
import store from './store';
import Cuida from '@sysvale/cuida';
import VueSweetalert2 from 'vue-sweetalert2';

import 'sweetalert2/dist/sweetalert2.min.css';

require('./bootstrap');

Vue.use(VueSweetalert2);
Vue.use(Cuida);

const app = new Vue({
    el: '#app',
    components: {
        App,
    },
    store,
    router,
});
