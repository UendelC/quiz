import Vue from 'vue';
import VueRouter from 'vue-router';
import ExampleComponent from './components/ExampleComponent';
import LoginMenu from './components/Auth/LoginMenu';

Vue.use(VueRouter);

export default new VueRouter({
    routes: [
        {
            path: '/login',
            component: LoginMenu,
        },
        {
            path: '/',
            component: ExampleComponent,
        },

    ],
    mode: 'history',
});