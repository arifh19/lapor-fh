<template>
    <div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Tambah Komplain</h1>
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

                        <form enctype="multipart/form-data" role="form" @submit.prevent="store({pelapor,identitas,sarpras_id, keterangan, kondisi_id})">
                            <div class="card-body">    
                               <!-- <item-option v-for="(lokasi,index) in lokasis" v-bind:key="index" v-bind:index="index"
                               v-on:delete-row="deleteItem(index)" v-on:selected="selectedItem"></item-option> -->                             
                              <div class="form-group">
                                <label for="pelapor">Nama Pelapor :</label>
                                <input class="form-control" v-model="pelapor" id="pelapor" placeholder="Masukan Nama Sarpras" required="">
                              </div>
                               <div class="form-group">
                                <label for="kode">No. Identitas : </label>
                                <input class="form-control" v-model="identitas"  id="identitas" placeholder="Masukan No. Identitas" required="">
                              </div>
                              <div class="form-group">
                                <label for="gedung">Gedung : </label>
                                <select class="form-control" id="gedung" v-model="searchRuangan" @change="fetchRuangan" required="" >
                                    <option value="" disabled>Pilih Gedung</option>
                                    <option v-for="gedung in gedungs" v-bind:value="gedung.gedung">
                                    {{ gedung.gedung }}
                                  </option>
                                </select>
                              </div>    
                              <div class="form-group">
                                <label for="ruangan">Ruangan : </label>
                                <select class="form-control" id="ruangan" v-model="lokasi_id" @change="fetchSarpras" required="">
                                    <option value="" disabled>Pilih Ruangan</option>
                                    <option v-for="ruangan in ruangans" v-bind:value="ruangan.id">
                                    {{ ruangan.ruangan }}
                                  </option>
                                </select>
                              </div>
                              <div class="form-group">
                                <label for="sarpras_id">Nama Sarpras : </label>
                                <select class="form-control" id="sarpras_id" v-model="sarpras_id" required="">
                                    <option value="" disabled>Pilih Sarpras</option>
                                    <option v-for="sarpras in sarprass" v-bind:value="sarpras.id">
                                    {{ sarpras.nama }}
                                  </option>
                                </select>
                              </div>                           
                              <div class="form-group">
                                <label for="kondisi_id">Kondisi : </label>
                                <select class="form-control" id="kondisi_id" v-model="kondisi_id" required="" >
                                    <option value="" disabled>Pilih Kondisi</option>
                                    <option v-for="kon in kondisis" v-bind:value="kon.id">
                                    {{ kon.keterangan }}
                                  </option>
                                </select>
                              </div> 
                              <div class="form-group">
                                <label for="keterangan">Keterangan :</label>
                                <textarea class="form-control" v-model="keterangan" id="keterangan" placeholder="Masukan Keterangan" required=""></textarea>
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
              sarprass : [],
              gedungs : [],
              kondisis : [],
              searchRuangan : "",
              lokasi_id : "",
              pelapor : "",
              identitas : "",
              sarpras_id : "",
              keterangan : "",
              kondisi_id : "",
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
        this.fetchKondisi();

		},
        methods:{
            fetch() {
              let readNotif = "";
              if(this.id_notif !== undefined){
                readNotif = "&read="+this.id_notif;
              }
                axios.get('/api/gedung',this.config)
                    .then(({data}) => {
                      //console.log(data);
                      this.gedungs = data;
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

            fetchKondisi() {
              let readNotif = "";
              if(this.id_notif !== undefined){
                readNotif = "&read="+this.id_notif;
              }
                axios.get('/api/kondisi',this.config)
                    .then(({data}) => {
                      //console.log(data);
                      this.kondisis = data;
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
            fetchSarpras() {
              let readNotif = "";
              if(this.id_notif !== undefined){
                readNotif = "&read="+this.id_notif;
              }
                axios.get('/api/sarpras/search/'+this.lokasi_id,this.config)
                    .then(({data}) => {
                      //console.log(data);
                      this.sarprass = data.data;
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
            store(){
                this.pending = true;
                var formData = new FormData();
                formData.append("dokumentasi",this.attachment);   
                formData.append("pelapor", this.pelapor);           
                formData.append("identitas", this.identitas);   
                formData.append("sarpras_id", this.sarpras_id);   
                formData.append("keterangan", this.keterangan);  
                formData.append("kondisi_id", this.kondisi_id);  
                axios.post('/api/komplains',formData,this.config)
                .then(({data}) => {
                    if(data.error == false){
                         this.$store.dispatch("success", data.message).then(() => {                            
                             this.$router.push("/komplains")
                          });                                            
                    }else{
                        alert(data.message);
                    }
                    this.pending = false;
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
                    this.pending = false;
                 });  
            } ,
            storeGedung(){
                this.pending = true;
                var formData = new FormData();
                formData.append("gedung",this.gedung);   
                formData.append("ruangan", this.ruangan);           
                axios.post('/api/lokasi',formData,this.config)
                .then(({data}) => {
                    if(data.error == false){
                         this.$store.dispatch("success", data.message).then(() => {                            
                          //code
                          this.fetch();
                          });                                            
                    }else{
                        alert(data.message);
                    }
                    this.pending = false;
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
                    this.pending = false;
                 });  
            } ,
            storeUnit(){
                this.pending = true;
                var formData = new FormData();
                formData.append("nama",this.namaunit);   
                axios.post('/api/units',formData,this.config)
                .then(({data}) => {
                    if(data.error == false){
                         this.$store.dispatch("success", data.message).then(() => {                            
                          //code
                          this.fetchUnit();
                          });                                            
                    }else{
                        alert(data.message);
                    }
                    this.pending = false;
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
                    this.pending = false;
                 });  
            } ,
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
