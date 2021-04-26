<template>
  <div class="login-wrap">
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
</div>
</template>

<script>
  import Cookie from 'js-cookie';
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
      };
    },

    methods: { 
      async login() {
        const validator = await this.$refs.loginForm.validate();

        if (!validator) { return; }
        console.log('login');
        const payload = {
          email: this.email,
          password: this.password,
        };

        axios.post('api/login', payload).then((response) => {
          const token = response.data.data.token;
          Cookie.set('_user_token', token, { expires: 2});

          this.$store.commit('user/STORE_USER', response.data.data.user);
        });
      }
    }

  }
</script>

<style lang="scss">
  @import "http://compass-style.org/reference/compass/css3/";

* { box-sizing: border-box; margin: 0; padding:0; }

html {
  background: #95a5a6;
  font-family: 'Helvetica Neue', Arial, Sans-Serif;
  
  .login-wrap {
    position: relative;
    margin: 0 auto;
    background: #ecf0f1;
    width: 350px;
    border-radius: 5px;
    box-shadow: 3px 3px 10px #333;
    padding: 15px;
    
    h2 {
      text-align: center;
      font-weight: 200;
      font-size: 2em;
      margin-top: 10px;
      color: #34495e;
    }
    
    .form {
      padding-top: 20px;
      
      input[type="text"],
      input[type="password"],
      button {
        width: 80%;
        margin-left: 10%;
        margin-bottom: 25px;
        height: 40px;
        border-radius: 5px;
        outline: 0;
        -moz-outline-style: none;
      }
      
      input[type="text"],
      input[type="password"] {
        border: 1px solid #bbb;
        padding: 0 0 0 10px;
        font-size: 14px;
        &:focus {
          border: 1px solid #3498db;
        }
      }
      
      a {
        text-align: center;
        font-size: 10px;
        color: #3498db;
        
        p{
          padding-bottom: 10px;
        }
        
      }
      
      button {
        background: #e74c3c;
        border:none;
        color: white;
        font-size: 18px;
        font-weight: 200;
        cursor: pointer;
        transition: box-shadow .4s ease;
        
        &:hover {
          box-shadow: 1px 1px 5px #555;  
        }
          
        &:active {
            box-shadow: 1px 1px 7px #222;  
        }
        
      }
      
    }
    
    &:after{
    content:'';
    position:absolute;
    top: 0;
    left: 0;
    right: 0;    
    background:-webkit-linear-gradient(left,               
        #27ae60 0%, #27ae60 20%, 
        #8e44ad 20%, #8e44ad 40%,
        #3498db 40%, #3498db 60%,
        #e74c3c 60%, #e74c3c 80%,
        #f1c40f 80%, #f1c40f 100%
        );
       background:-moz-linear-gradient(left,               
        #27ae60 0%, #27ae60 20%, 
        #8e44ad 20%, #8e44ad 40%,
        #3498db 40%, #3498db 60%,
        #e74c3c 60%, #e74c3c 80%,
        #f1c40f 80%, #f1c40f 100%
        );
      height: 5px;
      border-radius: 5px 5px 0 0;
  }
    
  }
  
}
</style>