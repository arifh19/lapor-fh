<template>
 <!-- Navbar -->
 <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="#" class="nav-link">Home</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="#" class="nav-link">Contact</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
   <notification-list />
   <li class="nav-item">
      <a href="#" v-if="isLoggedIn" @click="logout">Logout</a> 
   </li>
    
  </ul>
</nav>
      <!-- /.navbar -->

</template>

<script>

import Echo from "laravel-echo"
import Pusher from "pusher-js"
import jwt_decode from 'jwt-decode'

export default {
  methods: {
    logout() {
     this.$store.dispatch('logout').then(() => {
        this.$router.push("/login")
      });
    },

    listen(){
      window.Echo = new Echo({
          broadcaster: 'pusher',
          key: 'a1cffa6dd570e9f215d8',
          cluster: 'mt1',
          encrypted: true,
          auth: {
              headers: {
                  Authorization: 'Bearer ' + localStorage.getItem("token")
              },
          },
        
      });
      

      if(localStorage.getItem("token")) {
          let decoded = jwt_decode(localStorage.getItem("token"));
          window.Echo.private(`App.User.`+decoded.sub)
          .notification((notification) => {
              console.log(notification);
              this.$store.dispatch('addNotif',notification).then(() => {});
          });
          
      }
    }   

  },

  created(){
    this.listen();
  },

  computed: {
    isLoggedIn() {
      return this.$store.getters.isLoggedIn;
    }
  }
        
}
</script>