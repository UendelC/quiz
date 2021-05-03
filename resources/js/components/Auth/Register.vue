<template>
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">Register</div>

				<div class="card-body">
					<div v-if="response.message" :class="`rounded-sm bg-${response.color}-100 p-4 mb-4`">
						<h3 :class="`text-sm leading-5 font-medium text-${response.color}-800`">
							{{ response.message }}
						</h3>
					</div>
					<ValidationObserver ref="registerForm" tag="form" @submit.stop.prevent="register()">
						<div class="form">
							<div class="form-group row">
								<label for="name" class="col-md-4 col-form-label text-md-right"> Nome </label>

								<div class="col-md-6">
									<ValidationProvider v-slot="{errors}" rules="required" name="name">
										<input type="name" class="form-control" name="name" required v-model="name">
											<span v-if="!!errors[0]" class="invalid-feedback" role="alert">
												<strong>{{ errors[0] }}</strong>
											</span>
									</ValidationProvider>
								</div>
							</div>
						</div>

						<div class="form">
							<div class="form-group row">
								<label for="email" class="col-md-4 col-form-label text-md-right"> E-mail </label>

								<div class="col-md-6">
									<ValidationProvider v-slot="{errors}" rules="email|required" name="Email">
										<input type="email" class="form-control" name="email" required autocomplete="email" v-model="email">
											<span v-if="!!errors[0]" class="invalid-feedback" role="alert">
												<strong>{{ errors[0] }}</strong>
											</span>
									</ValidationProvider>
								</div>
							</div>

							<div class="form-group row">
								<label for="password" class="col-md-4 col-form-label text-md-right"> Senha </label>

								<div class="col-md-6">
									<ValidationProvider v-slot="{errors}" rules="required" name="password">
										<input type="password" class="form-control" name="password" required autocomplete="new-password" v-model="password">
											<span v-if="!!errors[0]" class="invalid-feedback" role="alert">
												<strong>{{ errors [0]}}</strong>
											</span>
									</ValidationProvider>
								</div>
							</div>

							<div class="form-group row">
								<label for="type" class="col-md-4 col-form-label text-md-right">Tipo de usuário</label>
								<div class="col-md-6">
									<ValidationProvider rules="required" name="type">
										<select name="type" v-model="type" class="form-control">
											<option value="participant">Estudante</option>
											<option value="teacher">Professor</option>
										</select>
									</ValidationProvider>
								</div>
							</div>
							<div class="form-group row mb-0">
								<div class="col-md-6 offset-md-4">
									<button type="submit" class="btn btn-primary">
										Registrar
									</button>
									<button class="btn btn-primary" @click="loginHandler()">
										Logar
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
</template>

<script>
	import { ValidationObserver, ValidationProvider } from 'vee-validate';
	import { required, email } from 'vee-validate/dist/rules';
	import { extend } from 'vee-validate';

	extend('required', required);
	extend('email', email);

export default {
	name: 'register',

	components: {
		ValidationObserver,
		ValidationProvider,
	},

	data() {
		return {
			name: '',
			email: '',
			password: '',
			type: '',
			response: {
				color: '',
				message: '',
			},
		}
	},

	methods: {
		async register() {
			console.log('teste');
			const validator = await this.$refs.registerForm.validate();
			if (!validator) {return;}

			this.resetResponse();

			const payload = {
				email: this.email,
				password: this.password,
				type: this.type,
				name: this.name,
			};

			axios.post('api/register', payload).then(() => {
				this.response.color = 'green';
				this.response.message = 'Seu cadastro foi feito com sucesso';
				this.resetForm();
			}).catch(error => {
				this.response.color = 'red';
				this.response.message = 'Credenciais inválidas.';
			});

		},

		resetResponse() {
			this.response.color = '';
			this.response.message = '';
		},

		resetForm() {
			this.$refs.registerForm.reset();
			this.name = '';
			this.email = '';
			this.password = '';
			this.type = '';
		},

		loginHandler() {
			this.$router.push({name: 'login'});
		}
	}
}
</script>

<style>

</style>