<template>
  <div class="wrapper">
	<div class="container">
		<h1>Welcome</h1>
		
		<form class="form">
			<input type="text" placeholder="Username" v-model="email">
			<input type="password" placeholder="Password" v-model="password">
			<button type="submit" @click.stop.prevent="login()">Login</button>
		</form>
	</div>
	
	<ul class="bg-bubbles">
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
	</ul>
</div>
</template>

<script>
  import Cookie from 'js-cookie';
  
  export default {
    name: 'Login',

    components: { 
      // LoginMenu,
    },

    data() {
      return {
        email: '',
        password: '',
      };
    },

    methods: { 
      login() {
        const payload = {
          email: this.email,
          password: this.password,
        };

        axios.post('api/login', payload).then((response) => {
          const token = response.data.data.token;
          Cookie.set('_user_token', token, { expires: 2});
          console.log(response);
        });
      }
    }

  }
</script>

<style>

</style>