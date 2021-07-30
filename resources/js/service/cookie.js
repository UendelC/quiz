import Cookie from 'js-cookie';

const TOKEN_NAME = '_user_token';

export default {
	setToken(token) {
		Cookie.set(TOKEN_NAME, token, {expires: 2});
	},

	getToken() {
		console.log('Cookie ' + Cookie.get(TOKEN_NAME);
		console.log('document ' + document.cookie);
		return Cookie.get(TOKEN_NAME);
	},

	deleteToken() {
		Cookie.remove(TOKEN_NAME);
	}
}