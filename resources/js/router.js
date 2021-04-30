import Vue from 'vue';
import VueRouter from 'vue-router';
import ExampleComponent from './components/ExampleComponent';
import LoginMenu from './components/Auth/LoginMenu';
import Register from './components/Auth/Register';
import Guard from './service/middleware';

Vue.use(VueRouter);

export default new VueRouter({
	routes: [
		{
			path: '/',
			name: 'index',
			component: ExampleComponent,
		},
		{
			path: '/login',
			beforeEnter: Guard.redirectIfAuthenticated,
			name: 'login',
			component: LoginMenu,
		},
		{
			path: '/register',
			name: 'register',
			component: Register,
		},

	],
	mode: 'history',
});