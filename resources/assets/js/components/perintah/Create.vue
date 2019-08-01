<template>
    <div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Pengajuan Perintah</h1>
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

                        <form enctype="multipart/form-data" role="form" @submit.prevent="store({keterangan, unit_id})">
                            <div class="card-body">  
                               <!-- <item-option v-for="(lokasi,index) in lokasis" v-bind:key="index" v-bind:index="index"
                               v-on:delete-row="deleteItem(index)" v-on:selected="selectedItem"></item-option> -->                             
                              <div class="form-group">
                                <label for="keterangan">Keterangan :</label>
                                <input class="form-control" v-model="keterangan" id="keterangan" placeholder="Masukan Keterangan" required="">
                              </div>
                              <div class="form-group">
                                <span><label for="unit_id">Ditujukan kepada : </label></span>
                                <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#modal-unit"><i class="fa fa-plus"></i></button><br>
                                <select class="form-control" id="unit_id" v-model="unit_id" required="" >
                                    <option value="" disabled>Pilih Unit</option>
                                    <option v-for="unit in units" v-bind:value="unit.id">
                                    {{ unit.nama }}
                                  </option>
                                </select>
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
            <div class="modal fade" id="modal-unit">
              <div class="modal-dialog" style="align: center">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Tambah Unit</h4>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                        <label for="namaunit">Unit :</label>
                        <input class="form-control" v-model="namaunit" id="namaunit" placeholder="Masukan Nama Unit" required="">
                    </div>
                    
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-success pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success pull-right" v-on:click="storeUnit()" data-dismiss="modal">Simpan</button>

                  </div>
                </div>
              <!-- /.modal-content -->
              </div>
              <!-- /.modal-dialog -->
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
              namaunit: '',
              units : [],
              keterangan : "",
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
          this.fetchUnit();
		    },
        methods:{
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
            store(){
                this.pending = true;
                var formData = new FormData();
                formData.append("keterangan", this.keterangan);   
                formData.append("unit_id", this.unit_id);    
                axios.post('/api/perintahs',formData,this.config)
                .then(({data}) => {
                    if(data.error == false){
                         this.$store.dispatch("success", data.message).then(() => {                            
                             this.$router.push("/perintahs")
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
            },           
        },
        computed: {
          checkIsInitial() {
            return this.isInitial;
          },
        }
    }
</script>
