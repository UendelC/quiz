import Vue from 'vue';
import VueRouter from 'vue-router';
import HomePage from './pages/HomePage';
import LoginMenu from './components/Auth/LoginMenu';
import Register from './components/Auth/Register';
import AboutPage from './pages/AboutPage';
import Guard from './service/middleware';
import RegisterInfoPage from './pages/RegisterInfoPage';
import Report from './pages/StatisticsPage';

Vue.use(VueRouter);

export default new VueRouter({
	routes: [
		{
			path: '/',
			name: 'index',
			beforeEnter: Guard.redirectIfNotAuthenticated,
			component: HomePage,
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
		{
			path: '/about',
			name: 'about',
			component: AboutPage,
		},
		{
			path: '/register-info',
			name: 'register-info',
			component: RegisterInfoPage,
		},
		{
			path: '/report',
			name: 'report',
			component: Report,
		}

	],
	mode: 'history',
});