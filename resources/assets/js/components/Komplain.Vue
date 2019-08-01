<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 v-if="role=='teknis' || role=='admin'" class="m-0 text-dark">Kelola Komplain</h1>
						<h1 v-if="role=='pengguna'" class="m-0 text-dark">Pengajuan Komplain</h1>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.container-fluid -->
		</div>
		<!-- /.content-header -->

		<!-- Main content -->
		<section class="content">
			<div class="container-fluid">
				<!-- Small boxes (Stat box) -->
				<div class="row">
					<div class="col-12">
						  <div v-if="isSuccess" class="alert alert-success alert-dismissible">
						  	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					          <strong>Berhasil!</strong> {{messages}}
					        </div>
						<div class="card">	
							<div class="card-header">	
								<div class="row">			  
								<div class="col-2">
									<router-link  :to="{ name: 'komplains-create'}" class="btn btn-primary" > <span class="fa fa-plus"></span>
        							Tambah Data
        					    </router-link>
								</div>
					      						
        					     </div>	
							</div>
							<div class="card-body users-style">
								<div class="table-style">
									<input class="input" type="text" v-model="search" placeholder="Search..."
										@input="resetPagination()" style="width: 125px;">
									<div class="control">
										<div class="select">
											<select v-model="length" @change="resetPagination()">
												<option value="10">10</option>
												<option value="20">20</option>
												<option value="30">30</option>
											</select>
										</div>
									</div>
								</div>
								<table class="table table-bordered table-responsive">
									<thead>
										<tr>
											<th v-for="column in columns" :key="column.name" @click="sortBy(column.name)"
												:class="sortKey === column.name ? (sortOrders[column.name] > 0 ? 'sorting_asc' : 'sorting_desc') : 'sorting'"
												style="width: 20%; cursor:pointer;">
												{{column.label}}
											</th>
											<th v-if="role=='admin'">Unit</th>
											<th>Action</th>
										</tr>
							
									</thead>
									<tbody v-if="role=='admin'">
										<tr v-for="komplain in paginatedUsers" :key="komplain.id">
											<td>{{komplain.updated_at}}</td>
											<td>{{komplain.pelapor}}</td>
											<td>{{komplain.identitas}}</td>
											<td>{{komplain.nama_sarpras}}</td>
											<td>{{komplain.gedung_sarpras}}</td>
											<td>{{komplain.lokasi_sarpras}}</td>
											<td>{{komplain.kondisi_sarpras}}</td>
											<td>{{komplain.estimasi}}</td>
											<td>{{komplain.status_aset}}</td>
											<td>{{komplain.unit_id}}</td>
											<td>
												<button type="button" v-on:click="show(komplain.id)" class="btn btn-outline-info" data-toggle="modal" data-target="#modal-dokum"><i class="fa fa-search"></i></button>
												<button v-on:click="deleteUser(komplain.id)" type="button" class="btn btn-outline-danger">
													<i v-if="pending" class="fa fa-refresh fa-spin"></i>
													<i v-if="!pending" class="fa fa-trash"></i>
												</button>
												<button v-if="komplain.unit_id == unit" type="button" v-on:click="show(komplain.id)" class="btn btn-outline-success" data-toggle="modal" data-target="#modal-disposisi">Disposisi</button>
											</td>
		

										</tr>
									</tbody>
									<tbody v-if="role=='teknis' || role=='pengguna'">
										<tr v-for="komplain in paginatedUsers" :key="komplain.id">
											<td>{{komplain.updated_at}}</td>
											<td>{{komplain.pelapor}}</td>
											<td>{{komplain.identitas}}</td>
											<td>{{komplain.nama_sarpras}}</td>
											<td>{{komplain.gedung_sarpras}}</td>
											<td>{{komplain.lokasi_sarpras}}</td>
											<td>{{komplain.kondisi_sarpras}}</td>
											<td>{{komplain.estimasi}}</td>
											<td>{{komplain.status_aset}}</td>
											<td>
												<button type="button" v-on:click="show(komplain.id)" class="btn btn-outline-info" data-toggle="modal" data-target="#modal-dokum"><i class="fa fa-search"></i></button>
												<button v-on:click="deleteUser(komplain.id)" type="button" class="btn btn-outline-danger">
													<i v-if="pending" class="fa fa-refresh fa-spin"></i>
													<i v-if="!pending" class="fa fa-trash"></i>
												</button>
												<button v-if="komplain.is_check == 0 && role=='teknis'" type="button" v-on:click="show(komplain.id)" class="btn btn-outline-success" data-toggle="modal" data-target="#modal-tindak">Tindak</button>
												<button v-if="komplain.is_check == 1 && role=='teknis'" type="button" v-on:click="show(komplain.id)" class="btn btn-outline-success" data-toggle="modal" data-target="#modal-laporan">Laporan</button>
											</td>
										</tr>
									</tbody>
									
								</table>
								<div>
									<nav class="pagination" v-if="!tableShow.showdata">
										<span class="page-stats">{{pagination.from}} - {{pagination.to}} of {{pagination.total}}</span>
										<a v-if="pagination.prevPageUrl" class="btn btn-sm btn-primary pagination-previous" @click="--pagination.currentPage">
											Prev
										</a>
										<a class="btn btn-sm btn-primary pagination-previous" v-else disabled>
										Prev
										</a>
										<a v-if="pagination.nextPageUrl" class="btn btn-sm pagination-next" @click="++pagination.currentPage">
											Next
										</a>
										<a class="btn btn-sm btn-primary pagination-next" v-else disabled>
											Next
										</a>
									</nav>
									<nav class="pagination" v-else>
										<span class="page-stats">
											{{pagination.from}} - {{pagination.to}} of {{filteredUsers.length}}
											<span v-if="`filteredUsers.length < pagination.total`"></span>
										</span>
										<a v-if="pagination.prevPage" class="btn btn-sm btn-primary pagination-previous" @click="--pagination.currentPage">
											Prev
										</a>
										<a class="btn btn-sm pagination-previous btn-primary" v-else disabled>
										Prev
										</a>
										<a v-if="pagination.nextPage" class="btn btn-sm btn-primary pagination-next" @click="++pagination.currentPage">
											Next
										</a>
										<a class="btn btn-sm pagination-next btn-primary"  v-else disabled>
											Next
										</a>
									</nav>
								</div>
							</div>
							<!-- /.card-body
							<div class="card-footer">								
								<pagination v-bind:pagination="paginator"  @paginate="fetch()" ></pagination>
							</div> -->
						</div>
					</div>
				</div>
				<!-- /.row -->
			</div><!-- /.container-fluid -->
			<div class="modal fade" id="modal-disposisi">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Disposisi Unit</h4>
						</div>
						<div class="modal-body">
							 <div class="form-group">
                                <span><label for="unit_id">Unit : </label></span>
                                <select class="form-control" id="unit_id" v-model="unit_id" required="" >
                                    <option value="" disabled>Pilih Unit</option>
                                    <option v-for="unit in units" v-bind:value="unit.id">
                                    {{ unit.nama }}
                                  </option>
                                </select>
                            </div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success pull-right" v-on:click="disposisi(id)" data-dismiss="modal">Simpan</button>
							<button type="button" class="btn btn-success pull-left" data-dismiss="modal">Close</button>
						</div>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<div class="modal fade" id="modal-dokum">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Foto</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="hp">Foto: </label></br>
								<img :src="foto" width="400" height="400" />
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success pull-right" data-dismiss="modal">Close</button>
						</div>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<div class="modal fade" id="modal-tindak">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Tindaklanjut Komplain</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
                                <label for="status">Status : </label>
                                <select class="form-control" id="status" v-model="status" required="" >
                                    <option value="" disabled>Pilih Kondisi</option>
                                    <option v-for="status in statuss" v-bind:value="status.keterangan">
                                    {{ status.keterangan }}
                                  </option>
                                </select>
                            </div>
							<div class="form-group">
								<label for="keterangan">Keterangan :</label>
								<input class="form-control" v-model="keterangan" id="keterangan" placeholder="Masukan Keterangan Tindakan" required="">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success pull-right" v-on:click="tindak(id)" data-dismiss="modal">Simpan</button>
							<button type="button" class="btn btn-success pull-left" data-dismiss="modal">Close</button>
						</div>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<div class="modal fade" id="modal-laporan">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Laporan Komplain</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="laporan">Uraian Laporan :</label>
								<textarea class="form-control" v-model="laporan" id="laporan" placeholder="Masukan Laporan Komplain" required=""></textarea>
							</div>
							<div class="form-group">
                                <label for="status">Status : </label>
                                <select class="form-control" id="status" v-model="status" required="" >
                                    <option value="" disabled>Pilih Kondisi</option>
                                    <option v-for="status in statuss" v-bind:value="status.keterangan">
                                    {{ status.keterangan }}
                                  </option>
                                </select>
                            </div> 
							 <div class="form-group">
                                <label for="kondisi">Kondisi : </label>
                                <select class="form-control" id="kondisi" v-model="kondisi" required="" >
                                    <option value="" disabled>Pilih Kondisi</option>
                                    <option v-for="kon in kondisis" v-bind:value="kon.keterangan">
                                    {{ kon.keterangan }}
                                  </option>
                                </select>
                              </div> 
							<div v-if="!dokum" class="form-group">
                                <label for="dokum">Upload Gambar : (optional) </label>
                                <div class="dropbox">
                                	<input type="file" id="dokum"  @change="onFileChange"
                                    accept="image/*" class="input-file">
                                    <p v-if="checkIsInitial">
                                    	Drag your file(s) here to begin<br> or click to browse
                                    </p>
                                </div>
                            </div>
                            <div v-else>
                                <img :src="dokum" width="200" height="200" />
                                <button type="button" @click="removeImage">Remove foto</button>
                            </div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success pull-right" v-on:click="lapor(id)" data-dismiss="modal">Simpan</button>
							<button type="button" class="btn btn-success pull-left" data-dismiss="modal">Close</button>
						</div>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
		</section>
		<!-- /.content -->
	</div>
</template>
<script>

    export default {

		props: ['id_notif'],

		data() {
			let sortOrders = {};
			let columns = [
				{label: 'Tanggal', name: 'updated_at'},
				{label: 'Pelapor', name: 'pelapor' },
				{label: 'No.Identitas', name: 'identitas'},
				{label: 'Nama Sarpras', name: 'nama_sarpras'},
				{label: 'Gedung', name: 'gedung_sarpras'},
				{label: 'Ruangan', name: 'lokasi_sarpras'},
				{label: 'Kondisi', name: 'kondisi_sarpras'},
				{label: 'Estimasi Waktu', name: 'estimasi'},
				{label: 'Status', name: 'status_aset'},
			];
			columns.forEach((column) => {
				sortOrders[column.name] = -1;
			});

			return {
				komplains: [],
				statuss: [],
				units: [],
				kondisis : [],
				columns: columns,
				sortKey: 'updated_at',
				sortOrders: sortOrders,
				length: 10,
				search: '',
				id: '',
				foto: '',
				unit: '',
				unit_id: '',
				dokum: '',
				status: '',
				kondisi: '',
				keterangan: '',
				laporan: '',
				isInitial:true,
				pending: false,
				attachment:'',
				role:"",
				pending: false,
				config:{
        			headers: {
        				'Content-Type': 'application/json' ,
        				'Authorization' : 'Bearer '+ localStorage.getItem("token"),
        			},
       			},
				tableShow: {
					showdata: true,
				},
				pagination: {
					currentPage: 1,
					total: '',
					nextPage: '',
					prevPage: '',
					from: '',
					to: ''
				},
			}
		},

		created() {
			this.getRole();
			this.fetch();
        	Fire.$on('reload', () => {
            	this.fetch();
        	})
		},

		methods: {
			getRole() {
		    	axios.get('/api/users/current',this.config)
			        .then(({data}) => {
			        	//console.log(data);
				        this.name = data.name;
				        this.unit = data.unit_id;
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
			fetchStatus() {
              let readNotif = "";
              if(this.id_notif !== undefined){
                readNotif = "&read="+this.id_notif;
              }
                axios.get('/api/status',this.config)
                    .then(({data}) => {
                      //console.log(data);
                      this.statuss = data;
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
			deleteUser(id){
				swal.fire({
					title: 'Delete this Komplain?',
					text: "You won't be able to revert this!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, delete it!'
					}).then((result) => {
						if (result.value) {
							this.pending = true;
							axios.delete(`/api/komplains/${id}`, this.config).then(()=>{
								this.fetch();
								Fire.$emit('reload')
									swal.fire(
									'Deleted!',
									'Komplain Deleted.',
									'success'
									)
								Fire.$emit('AfterCreate');
							}).catch(()=> {
								swal.fire("Failed!", "There was something wronge.", "warning");
							});
						}
						this.pending = false;
					})
			},
			
			fetch() {
				axios.get('/api/komplains', this.config)
					.then(response => {
						// console.log('The data: ', response.data)
						this.komplains = response.data;
						console.log(this.komplains);
						this.pagination.total = this.komplains.length;
					})
					.catch(errors => {
						console.log(errors);
					});
			},
			show(id) {
              axios.get('/api/komplains/'+id,this.config)
              .then(({data}) => {
                this.foto=data.dokumentasi;
				this.id=data.id;
				this.fetchKondisi();
				this.fetchStatus();
				this.fetchUnit();
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
			disposisi(id){
                this.pending = true;
                var formData = new FormData();
                formData.append("unit_id",this.unit_id);   
                axios.post('/api/komplain/disposisi/'+id,formData,this.config)
                .then(({data}) => {
                    if(data.error == false){
                         this.$store.dispatch("success", data.message).then(() => {                            
                          //code
						  this.fetch();
						  swal.fire("Berhasil!", "Disposisi berhasil terkirim", "success");
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
			tindak(id){
                this.pending = true;
                var formData = new FormData();
                formData.append("status",this.status);   
                formData.append("keterangan",this.keterangan);   
                axios.post('/api/komplain/tindak/'+id,formData,this.config)
                .then(({data}) => {
                    if(data.error == false){
                         this.$store.dispatch("success", data.message).then(() => {                            
                          //code
						  this.fetch();
						  swal.fire("Berhasil!", "Status berhasil diubah", "success");
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
			lapor(id){
                this.pending = true;
                var formData = new FormData();
                formData.append("status",this.status);   
				formData.append("laporan",this.laporan);  
				formData.append("kondisi",this.kondisi);  
				formData.append("dokumentasi",this.attachment);    
                axios.post('/api/komplain/laporan/'+id,formData,this.config)
                .then(({data}) => {
                    if(data.error == false){
                         this.$store.dispatch("success", data.message).then(() => {                            
                          //code
						  this.fetch();
						  swal.fire("Berhasil!", "Laporan berhasil dikirim", "success");
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
			onFileChange(e) {
              var files = e.target.files || e.dataTransfer.files;
              if (!files.length)
                return;
              this.attachment=files[0];
              this.createImage(files[0]);
            },

            createImage(file) {
              var dokum = new Image();
              var reader = new FileReader();
              var vm = this;              
              this.isInitial = false;
              reader.onload = (e) => {
                vm.dokum = e.target.result;
              };
              reader.readAsDataURL(file);
            },

            removeImage: function (e) {
              this.dokum = '';
              this.isInitial = true;
            },
			paginate(array, length, pageNumber) {
				this.pagination.from = array.length ? ((pageNumber - 1) * length) + 1 : ' ';
				this.pagination.to = pageNumber * length > array.length ? array.length : pageNumber * length;
				this.pagination.prevPage = pageNumber > 1 ? pageNumber : '';
				this.pagination.nextPage = array.length > this.pagination.to ? pageNumber + 1 : '';
				return array.slice((pageNumber - 1) * length, pageNumber * length);
			},
			resetPagination() {
				this.pagination.currentPage = 1;
				this.pagination.prevPage = '';
				this.pagination.nextPage = '';
			},
			sortBy(key) {
				this.resetPagination();
				this.sortKey = key;
				this.sortOrders[key] = this.sortOrders[key] * -1;
			},
			getIndex(array, key, value) {
				return array.findIndex(i => i[key] == value)
			},
		},

		computed: {
			isSuccess(){
				return this.$store.getters.isSuccess;
			},
			messages(){
				return this.$store.getters.messages;
			},
			checkIsInitial() {
            	return this.isInitial;
          	},
			filteredUsers() {
				let komplains = this.komplains;
				if (this.search) {
					komplains = komplains.filter((row) => {
						return Object.keys(row).some((key) => {
							return String(row[key]).toLowerCase().indexOf(this.search.toLowerCase()) > -1;
						})
					});
				}
				let sortKey = this.sortKey;
				let order = this.sortOrders[sortKey] || 1;
				if (sortKey) {
					komplains = komplains.slice().sort((a, b) => {
						let index = this.getIndex(this.columns, 'name', sortKey);
						a = String(a[sortKey]).toLowerCase();
						b = String(b[sortKey]).toLowerCase();
						if (this.columns[index].type && this.columns[index].type === 'date') {
							return (a === b ? 0 : new Date(a).getTime() > new Date(b).getTime() ? 1 : -1) * order;
						} else if (this.columns[index].type && this.columns[index].type === 'number') {
							return (+a === +b ? 0 : +a > +b ? 1 : -1) * order;
						} else {
							return (a === b ? 0 : a > b ? 1 : -1) * order;
						}
					});
				}
				return komplains;
			},
			paginatedUsers() {
				return this.paginate(this.filteredUsers, this.length, this.pagination.currentPage);
			}
		}	
    };
</script>