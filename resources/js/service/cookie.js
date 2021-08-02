import Cookie from 'js-cookie';
import store from '../store';

const TOKEN_NAME = '_user_token';

export default {
	setToken(token) {
		Cookie.set(TOKEN_NAME, token, {expires: 2});
	},

	getToken() {
		if (store.state.user.user.token) {
			return store.state.user.user.token;
		}

		return Cookie.get(TOKEN_NAME);
	},

	deleteToken() {
		Cookie.remove(TOKEN_NAME);
	}
}