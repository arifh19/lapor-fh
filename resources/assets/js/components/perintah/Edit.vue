<template>
    <div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Tambah Aset</h1>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">

                        <form enctype="multipart/form-data" role="form" @submit.prevent="store({nama_supir, alamat, hp ,foto})">
                            <div class="card-body">
                              <div class="form-group">
                                <label for="product">Gedung : </label>
                                <select class="form-control" id="lokasi_id" v-model="searchRuangan" @change="fetchRuangan" required="" >
                                    <option value="" disabled>Pilih Gedung</option>
                                    <option v-for="gedung in gedungs" v-bind:value="gedung.gedung">
                                    {{ gedung.gedung }}
                                  </option>
                                </select>
                              </div>    
                               <!-- <item-option v-for="(lokasi,index) in lokasis" v-bind:key="index" v-bind:index="index"
                               v-on:delete-row="deleteItem(index)" v-on:selected="selectedItem"></item-option> -->                             
                              <div class="form-group">
                                <label for="product">Ruangan : </label>
                                <select class="form-control" id="lokasi_id" v-model="lokasi_id" required="">
                                    <option value="" disabled>Pilih Ruangan</option>
                                    <option v-for="ruangan in ruangans" v-bind:value="ruangan.id">
                                    {{ ruangan.ruangan }}
                                  </option>
                                </select>
                              </div>    
                               <!-- <item-option v-for="(lokasi,index) in lokasis" v-bind:key="index" v-bind:index="index"
                               v-on:delete-row="deleteItem(index)" v-on:selected="selectedItem"></item-option> -->                             
                              <div class="form-group">
                                <label for="nama">Nama Sarpras :</label>
                                <input class="form-control" v-model="nama" id="nama" placeholder="Masukan Nama Sarpras" required="">
                              </div>
                               <div class="form-group">
                                <label for="kode">Kode : </label>
                                <input class="form-control" v-model="kode"  id="kode" placeholder="Masukan Kode Inventaris" required="">
                              </div>
                              <div class="form-group">
                                <label for="spesifikasi">Spesifikasi : </label>
                                <input class="form-control" v-model="spesifikasi"  id="spesifikasi" placeholder="Masukan Spesifikasi" required="">
                              </div>                             
                              <div class="form-group">
                                <label for="kondisi">Kondisi : </label>
                                <input class="form-control" v-model="kondisi"  id="kondisi" placeholder="Masukan Kondisi" required>
                              </div>
                              <div class="form-group">
                                <label for="unit_id">Unit yang bertanggungjawab : </label>
                                <select class="form-control" id="unit_id" v-model="unit_id" required="" >
                                    <option value="" disabled>Pilih Unit</option>
                                    <option v-for="unit in units" v-bind:value="unit.id">
                                    {{ unit.nama }}
                                  </option>
                                </select>
                              </div>   

                              <div v-if="!foto" class="form-group">
                                <label for="foto">Upload Gambar : (optional) </label>
                                  <div class="dropbox">
                                    <input type="file" id="foto"  @change="onFileChange"
                                      accept="image/*" class="input-file">
                                      <p v-if="checkIsInitial">
                                        Drag your file(s) here to begin<br> or click to browse
                                      </p>
                                  </div>
                                </div>
                              <div v-else>
                                <img :src="foto" width="200" height="200" />
                                <button type="button" @click="removeImage">Remove foto</button>
                              </div>
                            </div> 
                            
                            <div class="card-footer">
                              <button type="submit" class="btn btn-primary">
                                <i v-if="pending"  class="fa fa-refresh fa-spin"></i>Submit
                              </button>
                            </div>
                            
                        </form>
                        </div>
                  </div>
              </div>
            </div>
        </section>

</div>
</template>

<script>
    export default {
        data() {
            return {
              pending:false,
              isInitial:true,
              foto: '',
              attachment:'',
              ruangans : [],
              gedungs : [],
              units : [],
              searchRuangan : "",
              lokasi_id : "",
              nama : "",
              kode : "",
              spesifikasi : "",
              kondisi : "",
              unit_id : "",
              config:{
                    headers: {
                        'Content-Type': 'application/json' ,
                        'Authorization' : 'Bearer '+ localStorage.getItem("token"),
                    },
                },
            };
          },
        created() {
        this.fetch();
        this.fetchUnit();

		},
            methods:{
            fetch(){
              axios.get('/api/sarpras/'+this.id,{
                    headers: {
                        'Content-Type': 'application/json' ,
                        'Authorization' : 'Bearer '+ localStorage.getItem("token"),
                    },
                },)
              .then(({data}) => {
                this.ruangan=data.lokasi_id;
                this.searchRuangan=data.lokasi_id;
                this.nama=data.nama;
                this.kode=data.kode;
                this.spesifikasi = data.spesifikasi;
                this.kondisi = data.kondisi;
                this.unit_id = data.unit_id;
                this.foto = data.foto;
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
          fetchRuangan() {
              let readNotif = "";
              if(this.id_notif !== undefined){
                readNotif = "&read="+this.id_notif;
              }
                axios.get('/api/lokasi/search/'+this.searchRuangan,this.config)
                    .then(({data}) => {
                      //console.log(data);
                      this.ruangans = data.data;
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
            fetchUnit() {
              let readNotif = "";
              if(this.id_notif !== undefined){
                readNotif = "&read="+this.id_notif;
              }
                axios.get('/api/units',this.config)
                    .then(({data}) => {
                      //console.log(data);
                      this.units = data;
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
            update(){
                this.pending = true;     
                var formData = new FormData();
                if(this.attachment!=null){
                  formData.append("foto",this.attachment);  
                }  
                formData.append("nama_supir", this.nama_supir);           
                formData.append("alamat", this.alamat);   
                formData.append("hp", this.hp);   
                formData.append("status", this.status);   
                axios.post('/api/supirs/'+this.id,formData,this.config)
                .then(({data}) => {
                    if(data.error == false){
                         this.$store.dispatch("success", data.message).then(() => {                            
                             this.$router.push("/supirs")
                          });                                            
                    }else{
                        alert(data.message);
                    }
                    this.pending = false;
                })
                .catch((error) => {   
                     console.log(error);   
                     if(error.response.status == 401){
                        alert("Token Expired");
                        this.$update.dispatch('logout').then(() => {
                            this.$router.push("/login");
                        });
                    }else{                       
                        alert("Terjadi Kesalahan pada server");
                    } 
                    this.pending = false;
                 });
            },

            onFileChange(e) {
              var files = e.target.files || e.dataTransfer.files;
              if (!files.length)
                return;
              this.attachment=files[0];
              this.createImage(files[0]);
            },

            createImage(file) {
              var foto = new Image();
              var reader = new FileReader();
              var vm = this;              
              this.isInitial = false;
              reader.onload = (e) => {
                vm.foto = e.target.result;
              };
              reader.readAsDataURL(file);
            },

            removeImage: function (e) {
              this.foto = '';
              this.isInitial = true;
            }
        },

        computed: {
          checkIsInitial() {
            return this.isInitial;
          },
        }
    }
</script>
