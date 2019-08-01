<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Daftar Perintah</h1>
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
								<div class="col-2" v-if="role=='admin'">
									<router-link  :to="{ name: 'perintahs-create'}" class="btn btn-primary" > <span class="fa fa-plus"></span>
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
									<thead v-if="role == 'admin'">
										<tr>
											<th v-for="column in columns" :key="column.name" @click="sortBy(column.name)"
												:class="sortKey === column.name ? (sortOrders[column.name] > 0 ? 'sorting_asc' : 'sorting_desc') : 'sorting'"
												style="width: 20%; cursor:pointer;">
												{{column.label}}
											</th>
											<th>Unit</th>
											<th>Action</th>
										</tr>
									</thead>
									<thead v-if="role == 'teknis'">
										<tr>
											<th v-for="column in columns" :key="column.name" @click="sortBy(column.name)"
												:class="sortKey === column.name ? (sortOrders[column.name] > 0 ? 'sorting_asc' : 'sorting_desc') : 'sorting'"
												style="width: 20%; cursor:pointer;">
												{{column.label}}
											</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody v-if="role == 'admin'">
										<tr v-for="perintah in paginatedUsers" :key="perintah.id">
											<td>{{perintah.updated_at}}</td>
											<td>{{perintah.pengirim}}</td>
											<td>{{perintah.keterangan}}</td>
											<td>{{perintah.status}}</td>
											<td>{{perintah.diperbaharui_oleh}}</td>
											<td>{{perintah.unit_id}}</td>
											<td>
												<button type="button" v-on:click="show(perintah.id)" class="btn btn-outline-info" data-toggle="modal" data-target="#modal-dokum"><i class="fa fa-search"></i></button>
												<button v-on:click="deleteUser(perintah.id)" type="button" class="btn btn-outline-danger">
													<i v-if="pending" class="fa fa-refresh fa-spin"></i>
													<i v-if="!pending" class="fa fa-trash"></i>
												</button>
												<button v-if="perintah.status == 'Menunggu konfirmasi'&& role=='teknis'" type="button" v-on:click="show(perintah.id)" class="btn btn-outline-success" data-toggle="modal" data-target="#modal-tindak">Tindak</button>
												<button v-if="perintah.status != 'Menunggu konfirmasi' && role=='teknis'" type="button" v-on:click="show(perintah.id)" class="btn btn-outline-success" data-toggle="modal" data-target="#modal-laporan">Laporan</button>
											</td>
										</tr>
									</tbody>
									<tbody v-if="role == 'teknis'">
										<tr v-for="perintah in paginatedUsers" :key="perintah.id">
											<td>{{perintah.updated_at}}</td>
											<td>{{perintah.pengirim}}</td>
											<td>{{perintah.keterangan}}</td>
											<td>{{perintah.status}}</td>
											<td>{{perintah.diperbaharui_oleh}}</td>
											<td>
												<button type="button" v-on:click="show(perintah.id)" class="btn btn-outline-info" data-toggle="modal" data-target="#modal-dokum"><i class="fa fa-search"></i></button>
												<!-- <button v-on:click="deleteUser(sarp.id)" type="button" class="btn btn-outline-danger">
													<i v-if="pending" class="fa fa-refresh fa-spin"></i>
													<i v-if="!pending" class="fa fa-trash"></i>
												</button> -->
												<button v-if="perintah.status == 'Menunggu konfirmasi' && role=='teknis'" type="button" v-on:click="show(perintah.id)" class="btn btn-outline-success" data-toggle="modal" data-target="#modal-tindak">Tindak</button>
												<button v-if="perintah.status != 'Menunggu konfirmasi' && role=='teknis'" type="button" v-on:click="show(perintah.id)" class="btn btn-outline-success" data-toggle="modal" data-target="#modal-laporan">Laporan</button>
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
			<div class="modal fade" id="modal-tindak">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Tindaklanjut Perintah</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="status">Status/Keterangan :</label>
								<input class="form-control" v-model="status" id="status" placeholder="Masukan Keterangan/Status Perintah" required="">
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
							<h4 class="modal-title">Laporan Perintah</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="laporan">Uraian Laporan :</label>
								<textarea class="form-control" v-model="laporan" id="laporan" placeholder="Masukan Laporan Perintah" required=""></textarea>
							</div>
							<div class="form-group">
								<label for="status">Status/Keterangan :</label>
								<input class="form-control" v-model="status" id="status" placeholder="Masukan Keterangan/Status Perintah" required="">
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
				{label: 'Tanggal', name: 'updated_at' },
				{label: 'Pengirim', name: 'pengirim'},
				{label: 'Keterangan', name: 'Keterangan'},
				{label: 'Status', name: 'status'},
				{label: 'Updated by', name: 'diperbaharui_oleh'},
			];
			columns.forEach((column) => {
				sortOrders[column.name] = -1;
			});
			
			return {
				perintahs: [],
				columns: columns,
				sortKey: 'updated_at',
				sortOrders: sortOrders,
				length: 10,
				search: '',
				foto: '',
				role: '',
				id: '',
				laporan: '',
				status: '',
				foto: '',
				isInitial:true,
				pending: false,
				attachment:'',
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
			
			deleteUser(id){
				swal.fire({
					title: 'Delete this Perintah?',
					text: "You won't be able to revert this!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, delete it!'
					}).then((result) => {
						if (result.value) {
							this.pending = true;
							axios.delete(`/api/perintahs/${id}`, this.config).then(()=>{
								this.fetch();
								Fire.$emit('reload')
									swal.fire(
									'Deleted!',
									'Perintah Deleted.',
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
				axios.get('/api/perintahs', this.config)
					.then(response => {
						console.log('The data: ', response.data)
						this.perintahs = response.data;
						this.pagination.total = this.perintahs.length;
					})
					.catch(errors => {
						console.log(errors);
					});
			},
			show(id) {
              axios.get('/api/perintahs/'+id,this.config)
              .then(({data}) => {
                this.id=data.id;
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
			tindak(id){
                this.pending = true;
                var formData = new FormData();
                formData.append("status",this.status);   
                axios.post('/api/perintah/tindak/'+id,formData,this.config)
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
				formData.append("dokumentasi",this.attachment);    
                axios.post('/api/perintah/laporan/'+id,formData,this.config)
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
				let perintahs = this.perintahs;
				if (this.search) {
					perintahs = perintahs.filter((row) => {
						return Object.keys(row).some((key) => {
							return String(row[key]).toLowerCase().indexOf(this.search.toLowerCase()) > -1;
						})
					});
				}
				let sortKey = this.sortKey;
				let order = this.sortOrders[sortKey] || 1;
				if (sortKey) {
					perintahs = perintahs.slice().sort((a, b) => {
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
				return perintahs;
			},
			paginatedUsers() {
				return this.paginate(this.filteredUsers, this.length, this.pagination.currentPage);
			}
		}	
    };
</script>
