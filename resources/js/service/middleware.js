import Cookie from 'js-cookie';

export default {
	redirectIfAuthenticated(to, from, next) {
		const token = Cookie.get('_user_token');
		let n;

		if (token) {
			n = {name: 'index'};
		}

		next(n);
	}
}