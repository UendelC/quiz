import Vue from 'vue';
import VueRouter from 'vue-router';
import ExampleComponent from './components/ExampleComponent';
import LayoutAuth from './components/Auth/LayoutAuth';

Vue.use(VueRouter);

export default new VueRouter({
    routes: [
        {
            path: '/login',
            component: LayoutAuth,
        },
        {
            path: '/',
            component: ExampleComponent,
        },

    ],
    mode: 'history',
});