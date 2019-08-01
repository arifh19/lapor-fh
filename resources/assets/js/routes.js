import VueRouter from 'vue-router';
import Dashboard from './components/Dashboard.vue';
import Home from './components/Home.vue';
import Login from './components/Login.vue';
import userStore from './UserStore';
import Sarpras from './components/Sarpras.vue';
import SarprasCreate from './components/sarpras/Create.vue';
import SarprasEdit from './components/sarpras/Edit.vue';
import Komplain from './components/Komplain.vue';
import KomplainCreate from './components/komplain/Create.vue';
import Perintah from './components/Perintah.vue';
import PerintahCreate from './components/perintah/Create.vue';
import User from './components/User.vue';


let routes = [  
    {
        path: '/',
        component: Home,
        children: [
          {
            path: '/',
            component: Dashboard,
            name: 'dashboard', 
            meta: { requiresAuth: true} ,
          },
          {
            path: 'users',
            component: User,
            name: 'users', 
            meta: { requiresAuth: true, AdminAuth: true} ,
          },
          {
            path: 'komplains',
            component: Komplain,
            name: 'komplains', 
            meta: { requiresAuth: true} ,
          },
          {
            path: 'komplains/create',
            component: KomplainCreate,
            name: 'komplains-create', 
            meta: { requiresAuth: true} ,
          },
          {
            path: 'sarpras',
            component: Sarpras,
            name: 'sarpras', 
            meta: { requiresAuth: true, AdminTeknisAuth: true} ,
          },
          {
            path: 'sarpras/create',
            component: SarprasCreate,
            name: 'sarpras-create', 
            meta: { requiresAuth: true, AdminTeknisAuth: true} ,
          },
          {
            path: 'sarpras/:id',
            component: SarprasEdit,
            name: 'sarpras-edit', 
            meta: { requiresAuth: true, AdminTeknisAuth: true} ,
            props: true,
          },
          {
            path: 'perintahs',
            component: Perintah,
            name: 'perintahs', 
            meta: { requiresAuth: true, AdminTeknisAuth: true} ,
          },
          {
            path: 'perintahs/create',
            component: PerintahCreate,
            name: 'perintahs-create', 
            meta: { requiresAuth: true, AdminTeknisAuth: true} ,
          },
        ],
    },
    {
        path: '/login',
        component: Login,
        meta: { requiresAuth: false} ,
    },


];

const router = new VueRouter({
    routes
});
var authUser = "";

function fetch() {
  var config = {
    headers: {
      'Content-Type': 'application/json' ,
      'Authorization' : 'Bearer '+ localStorage.getItem("token"),
    },
   };
  axios.get('/api/users/current',config)
  .then(({data}) => {
    authUser = data.role; //memasukkan data role ke authUser
  })
  .catch((error) => {	     
   if(error.response.status == 401){
      alert("Token Expired");
      next({
        path: '/login',   //kembali ke halaman login
      });
    }else{			           	
      alert("Terjadi Kesalahan pada server");
    } 
  });  
}
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {  //jika butuh login
    
    if (!userStore.getters.isLoggedIn) { //ga punya token
      next({
        path: '/login',  //kembali ke tempat login
      });
    } 
    else if (to.meta.AdminAuth) { 
      fetch();
      if(authUser === "admin"){
        next(); //lanjut ke route berikutnya
      }
      else { //jika rolenya tidak sesuai
        next({
          path: '/',   //dashboard
        });      
      }
    }
    else if (to.meta.AdminTeknisAuth) { 
      fetch();
      if(authUser === "admin"||authUser === "teknis"){
        next(); //lanjut ke route berikutnya
      }
      else { //jika rolenya tidak sesuai
        next({
          path: '/',   //dashboard
        });      
      }
    }
    else if (to.meta.teknisAuth) { 
      fetch();
      if(authUser === "teknis"){
        next(); //lanjut ke route berikutnya
      }
      else { //jika rolenya tidak sesuai
        next({
          path: '/',   //dashboard
        });      
      }
    }
    else{ //bukan admin / teknis / pengguna
      next();  
    }
  } 
  else {   //ga butuh login
    if (userStore.getters.isLoggedIn) {   //punya token
       next({
        path: '/',   //dashboard
      });
    }
    else{ //ga punya token
     next();   //lanjutkan
    }
  }
})

export default router;