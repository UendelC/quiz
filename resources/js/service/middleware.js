import Cookie from './cookie';
import axios from 'axios';
import store from '../store';

export default {
	async redirectIfNotAuthenticated(to, from, next) {
		const token = Cookie.getToken();

		if (!token) {
			next({name: 'login'});
		}

		await axios.get('api/me/user', {
			headers: {
				Authorization: 'Bearer ' + token
			}
		 })
		.then((response) => {
			if (!store?.state?.user?.id) {
				const data = {
						user: response.data.data,
						token,
				};
				store.commit('user/STORE_USER', data);
			}
		})
		.catch(() => {
			Cookie.deleteToken();
			store.commit('user/CLEAR_USER');
			next({name: 'login'});
		});


		next();
	},

	redirectIfAuthenticated(to, from, next) {
		const token = Cookie.getToken();
		let n;

		if (token) {
			n = {name: 'about'};
		}

		next(n);
	}
}