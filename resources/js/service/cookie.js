import Cookie from 'js-cookie';

const TOKEN_NAME = '_user_token';

export default {
	setToken(token) {
		Cookie.set(TOKEN_NAME, token, {expires: 2});
	},

	getToken() {
		if (typeof Cookie.get(TOKEN_NAME) == 'undefined') {
			console.log('cookie nulo');
			console.log(document.cookie);
		} else {
			console.log('cookie válido');
		}
		return Cookie.get(TOKEN_NAME);
	},

	deleteToken() {
		Cookie.remove(TOKEN_NAME);
	}
}