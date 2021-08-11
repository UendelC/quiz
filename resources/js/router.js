import Vue from 'vue';
import VueRouter from 'vue-router';
import HomePage from './pages/HomePage';
import LoginMenu from './components/Auth/LoginMenu';
import Register from './components/Auth/Register';
import AboutPage from './pages/AboutPage';
import Guard from './service/middleware';
import RegisterInfoPage from './pages/RegisterInfoPage';
import ExamManagement from './pages/ExamManagement';
import Report from './pages/StatisticsPage';
import ExamPage from './pages/ExamPage';
import StudentGrade from './pages/StudentGrade';

Vue.use(VueRouter);

export default new VueRouter({
	routes: [
		{
			path: '/',
			name: 'index',
			beforeEnter: Guard.redirectIfNotAuthenticated,
			component: AboutPage,
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
			beforeEnter: Guard.redirectIfNotAuthenticated,
			component: AboutPage,
		},
		{
			path: '/exam-management',
			name: 'exam-management',
			beforeEnter: Guard.redirectIfNotAuthenticated,
			component: ExamManagement,
		},
		{
			path: '/report',
			name: 'report',
			beforeEnter: Guard.redirectIfNotAuthenticated,
			component: Report,
		},
		{
			path: '/exam',
			name: 'exam',
			beforeEnter: Guard.redirectIfNotAuthenticated,
			component: ExamPage,
		},
		{
			path: '/student-grades',
			name: 'grades',
			beforeEnter: Guard.redirectIfNotAuthenticated,
			component: StudentGrade,
		}

	],
	mode: 'history',
});