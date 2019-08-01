<template>
      <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="admin-lte/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Admin</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="admin-lte/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{name}}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
              
              <li v-if="role=='admin'||role=='teknis'||role=='pengguna'" class="nav-item">
                <router-link  :to="{ name: 'dashboard'}"  class="nav-link">                  
                    <i class="nav-icon fa fa-bar-chart-o"  :class="{ 'text-info': isActive('dashboard') }"></i>
                    <p>Dashboard</p>
                </router-link>
              </li>
              <li v-if="role=='admin'" class="nav-item">
                <router-link  :to="{ name: 'users'}"  class="nav-link">                  
                    <i class="nav-icon fa fa-user"  :class="{ 'text-info': isActive('users') }"></i>
                    <p>Kelola Akun</p>
                </router-link>
              </li>
              <li v-if="role=='admin'||role=='teknis'" class="nav-item">
                <router-link  :to="{ name: 'sarpras'}"  class="nav-link">                  
                    <i class="nav-icon fa fa-dropbox"  :class="{ 'text-info': isActive('sarpras') }"></i>
                    <p>Kelola Sarana Prasarana</p>
                </router-link>
              </li>

              <li v-if="role=='admin'||role=='teknis'" class="nav-item">
                <router-link  :to="{ name: 'perintahs'}"  class="nav-link">                  
                    <i class="nav-icon fa fa-edit"  :class="{ 'text-info': isActive('perintahs') }"></i>
                    <p>Daftar Perintah</p>
                </router-link>
              </li>

              <li v-if="role=='admin'||role=='teknis'||role=='pengguna'" class="nav-item">
                <router-link  :to="{ name: 'komplains'}"  class="nav-link">                  
                    <i class="nav-icon fa fa-wrench"  :class="{ 'text-info': isActive('komplains') }"></i>
                    <p v-if="role=='admin'||role=='teknis'">Daftar Komplain</p>
                    <p v-if="role=='pengguna'">Pengajuan Komplain</p>

                </router-link>
              </li>
              <li v-if="role=='admin'||role=='teknis'||role=='pengguna'" class="nav-item"  @click="logout">
                <router-link  :to="{ name: 'logout'}"  class="nav-link">                  
                    <i class="nav-icon fa fa-sign-out"  :class="{ 'text-info': isActive('login') }"></i>
                    <p >Logout</p>

                </router-link>
                
              </li>
        </ul>
      </nav>
          <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>

<script>
    export default {
        data(){
          return {
            name:"",
            role:"",
            config: {
                headers: {
                  'Content-Type': 'application/json' ,
                  'Authorization' : 'Bearer '+ localStorage.getItem("token"),
                },
            },
          }
        },

        created(){
          this.fetch();
        },
        
        methods: {
          logout() {
            this.$store.dispatch('logout').then(() => {
                this.$router.push("/login")
            });
          },
          isActive(menuItem) {
            return this.$route.name === menuItem;
          },

          fetch() {
		    	axios.get('/api/users/current',this.config)
			        .then(({data}) => {
			        	//console.log(data);
				        this.name = data.name;
				        this.role = data.role;
				      })
			        .catch((error) => {	     
					   if(error.response.status == 401){
					   	alert("Token Expired");
			            this.$store.dispatch('logout').then(() => {
					    	this.$router.push("/login");
					 	});
			           }else{			           	
			          		alert("Terjadi Kesalahan pada server");
			           } 
		        });  
		      },
        },
        
    }
</script>
