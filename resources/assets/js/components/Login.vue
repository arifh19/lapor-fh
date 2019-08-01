<template>
  <div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="images/img-01.png" alt="IMG">
				</div>

				<form class="login100-form validate-form" @submit.prevent="login({ email, password })">
					<span class="login100-form-title">
						LAPOR Login
					</span>

					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" type="email" name="email" placeholder="Email" v-model="email" id="inputEmail" required autofocus>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" type="password" name="pass" placeholder="Password" v-model="password" id="inputPassword">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn" type="submit">
							  <i v-if="isPending"  class="fa fa-login fa-refresh fa-spin"></i>Sign in

						</button>
					</div>
          <div v-if="isError" class="alert alert-danger">
          <strong>Danger!</strong> {{messages}}
          </div>
					<div class="text-center p-t-12">
						<span class="txt1">
							Forgot
						</span>
						<a class="txt2" href="#">
							Username / Password?
						</a>
					</div>

				</form>
			</div>
		</div>
	</div>
</template>

<script>

export default {
   data() {
    return {
      email: "",
      password: ""
    }
  },

  methods: {
    login() {
      this.$store.dispatch("login", {
        email: this.email,
        password: this.password
      }).then(() => {
        this.$router.push("/")
      });
    },
    
  },

  computed: {
    isPending(){
      return this.$store.getters.isPending;
    },

    isError(){
      return this.$store.getters.isError;
    },

    messages(){
      return this.$store.getters.messages;
    },
  }
  
}
</script>