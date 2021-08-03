<template>
	<!-- <div class="login-wrap">
		<h2>Login</h2>
	
		<ValidationObserver tag="form" ref="loginForm" @submit.stop.prevent="login">
			<div class="form">

				<ValidationProvider rules="required|email" v-slot="{ errors }">
					<div v-if="!!errors[0]" class="text-red-500 text-sm mb-2">
						{{ errors[0] }}
					</div>
					<input type="text" placeholder="E-mail" name="un"  v-model="email"/>
				</ValidationProvider>

				<ValidationProvider rules="required" v-slot="{ errors }"> 
					<div v-if="!!errors[0]" class="text-red-500 text-sm mb-2">
						{{ errors[0] }}
					</div>
					<input type="password" placeholder="Password" name="ps" v-model="password"/>
				</ValidationProvider>
				<button type="submit"> Sign in </button>
				<a href="#"> <p> Don't have an account? Register </p></a>
			</div>

		</ValidationObserver>
</div> -->
<div class="vueport">
	<div class="container">
			<div class="row justify-content-center">
					<div class="col-md-8">
							<div class="card">
									<div class="card-header">Login</div>

									<div class="card-body">
										<div v-if="response.message" :class="`rounded-sm bg-${response.color}-100 p-4 mb-4`">
											<h3 :class="`text-sm leading-5 font-medium text-${response.color}-800`">
												{{ response.message }}
											</h3>
										</div>
											<ValidationObserver tag="form" ref="loginForm" @submit.stop.prevent="login">
												<div class="form">
														<div class="form-group row">
																<label for="email" class="col-md-4 col-form-label text-md-right">E-mail</label>

																<div class="col-md-6">
																	<ValidationProvider rules="required|email" v-slot="{ errors }">
																		<input id="email" type="email" class="form-control" name="email" required autocomplete="email" autofocus v-model="email">

																		<span v-if="!!errors[0]" class="invalid-feedback" role="alert">
																				<strong>{{ errors[0] + '1'}}</strong>
																		</span>
																	</ValidationProvider>
																</div>
														</div>

														<div class="form-group row">
																<label for="password" class="col-md-4 col-form-label text-md-right">Senha</label>

																<div class="col-md-6">
																	<ValidationProvider rules="required" v-slot="{ errors }">
																		<input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" v-model="password">

																			<span v-if="!!errors[0]" class="invalid-feedback" role="alert">
																				<strong>{{ errors[0] + '2'}}</strong>
																			</span>
																	</ValidationProvider>
																</div>
														</div>

														<div class="form-group row mb-0">
																<div class="col-md-8 offset-md-4">
																		<button type="submit" class="btn btn-primary">
																				Login
																		</button>
																		<button class="btn btn-primary" @click="registerHandler()">
																				Registrar-se
																		</button>
																</div>
														</div>
												</div>
											</ValidationObserver>
									</div>
							</div>
					</div>
			</div>
	</div>
</div>
</template>

<script>
	import Cookie from '../../service/cookie';
	import { ValidationObserver, ValidationProvider } from 'vee-validate';
	import { required, email } from 'vee-validate/dist/rules';
	import { extend } from 'vee-validate';

	extend('required', required);
	extend('email', email);

	export default {
		name: 'Login',

		components: {
			// LoginMenu,
			ValidationProvider,
			ValidationObserver,
		},

		data() {
			return {
				email: '',
				password: '',
				response: {
					color: '',
					message: '',
				},
			};
		},

		methods: {
			async login() {
				const validator = await this.$refs.loginForm.validate();

				if (!validator) { return; }

				const payload = {
					email: this.email,
					password: this.password,
				};

				this.resetResponse();

				await axios.post('api/login', payload).then((response) => {
					const token = response.data.data.token;
					Cookie.setToken(token);

					this.$store.commit('user/STORE_USER', response.data.data.user);
					this.$router.push({name: 'index'});
				}).catch(() => {
					this.response.color = 'red';
					this.response.message = 'Credenciais inválidas.';
				});
			},

			resetResponse() {
				this.response.color = '';
				this.response.message = '';
			},

			registerHandler() {
				this.$router.push({name: 'register'});
			}
		}

	}
</script>

<style scoped>
	.vueport {
		display: flex;
		align-items: center;
		height: 100vh;
	}

	.card {
    box-shadow: 0 0 10px 2px rgba(100, 100, 100, 0.1);
	}

</style>