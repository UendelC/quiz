import Cookie from './cookie';

export default {
	redirectIfNotAuthenticated(to, from, next) {
		const token = Cookie.getToken();

		if (!token) {
			next({name: 'login'});
		}

		next();
	},

	redirectIfAuthenticated(to, from, next) {
		const token = Cookie.getToken();
		let n;

		if (token) {
			n = {name: 'index'};
		}

		next(n);
	}
}