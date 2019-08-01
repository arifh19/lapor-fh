<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Kelola Pengguna</h1>
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
								<button type="button" v-on:click="fetchUnit()" class="btn btn-primary" data-toggle="modal" data-target="#modal-user"><span class="fa fa-plus"></span>
									Tambah Data
								</button>

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
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="user in paginatedUsers" :key="user.id">
											<td>{{user.name}}</td>
											<td>{{user.email}}</td>
											<td>{{user.unit}}</td>
											<td>{{user.role}}</td>
											<td>{{user.status}}</td>
											<td>{{user.updated_at}}</td>
											<td>
												<button v-on:click="getUser(user.id)" class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-edit">
													<i class="fa fa-edit"></i>
												</button>
												<button v-on:click="setStatus(user.id,index)" type="button" class="btn btn-outline-danger">
													{{user.status=='Aktif' ? 'Non Aktifkan':'Aktifkan'}}
												</button>
								
												<button v-on:click="deleteUser(user.id)" type="button" class="btn btn-outline-danger">
													<i v-if="pending" class="fa fa-refresh fa-spin"></i>
													<i v-if="!pending" class="fa fa-trash"></i>
												</button>
												<div class="btn-group">
												<button type="button" class="btn btn-outline-danger">Role</button>
												<button type="button" class="btn btn-outline-danger dropdown-toggle" data-toggle="dropdown">
													<span class="caret"></span>
													<span class="sr-only">Toggle Dropdown</span>
												</button>
												<ul class="dropdown-menu" role="menu">
													<li><button v-on:click="setRole(user.id,'admin')" type="button">Admin</button></li>
													<li><button v-on:click="setRole(user.id,'teknis')" type="button">Teknis</button></li>
													<li><button v-on:click="setRole(user.id,'pengguna')" type="button">Pengguna</button></li>
												</ul>
												</div>
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
			<div class="modal fade" id="modal-user">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Register</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="name">Nama Pengguna :</label>
								<input class="form-control" v-model="name" id="name" placeholder="Masukan Nama" required="">
							</div>
							<div class="form-group">
								<label for="email">Email :</label>
								<input class="form-control" v-model="email" id="email" placeholder="Masukan Email" required="">
							</div>
							<div class="form-group">
                                <span><label for="unit_id">Unit : </label></span>
                                <select class="form-control" id="unit_id" v-model="unit_id" required="" >
                                    <option value="" disabled>Pilih Unit</option>
                                    <option v-for="unit in units" v-bind:value="unit.id">
                                    {{ unit.nama }}
                                  </option>
                                </select>
                            </div>
							<div class="form-group">
								<label for="password">Password :</label>
								<input type="password" class="form-control" v-model="password" id="password" placeholder="Masukan Password" required="">
							</div>
							<div class="form-group">
								<label for="password2">Retype Password :</label>
								<input type="password" class="form-control" v-model="password2" id="password2" placeholder="Masukan Password" required="">
							</div>
						</div>
						<div class="modal-footer">
                    		<button type="button" class="btn btn-success pull-left" data-dismiss="modal">Close</button>
                    		<button type="button" class="btn btn-success pull-right" v-on:click="check()" data-dismiss="modal">Register</button>						</div>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<div class="modal fade" id="modal-edit">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Edit User</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="name">Nama Pengguna :</label>
								<input class="form-control" v-model="name" id="name2" placeholder="Masukan Nama" required="">
							</div>
							<div class="form-group">
								<label for="email">Email :</label>
								<input class="form-control" v-model="email" id="email2" placeholder="Masukan Email" required="">
							</div>
							<div class="form-group">
                                <span><label for="unit_id">Unit : </label></span>
                                <select class="form-control" id="unit_id2" v-model="unit_id" required="" >
                                    <option value="" disabled>Pilih Unit</option>
                                    <option v-for="unit in units" v-bind:value="unit.id">
                                    {{ unit.nama }}
                                  </option>
                                </select>
                            </div>
							<div class="form-group">
								<label for="password">Password :</label>
								<input type="password" class="form-control" v-model="password" id="password4" placeholder="Masukan Password">
							</div>
							<div class="form-group">
								<label for="password2">Retype Password :</label>
								<input type="password" class="form-control" v-model="password2" id="password5" placeholder="Masukan Password">
							</div>
						</div>
						<div class="modal-footer">
                    		<button type="button" class="btn btn-success pull-left" data-dismiss="modal">Close</button>
                    		<button type="button" class="btn btn-success pull-right" v-on:click="checkUpdate()" data-dismiss="modal">Update</button>						
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
				{label: 'Nama Pengguna', name: 'nama' },
				{label: 'Email', name: 'email'},
				{label: 'Unit', name: 'unit'},
				{label: 'Role', name: 'role'},
				{label: 'Status', name: 'status'},
				{label: 'Update at', name: 'updated_at'},
			];
			columns.forEach((column) => {
				sortOrders[column.name] = -1;
			});
			
			return {
				users: [],
				units: [],
				columns: columns,
				sortKey: 'nama',
				sortOrders: sortOrders,
				length: 10,
				search: '',
				id: '',
				name: '',
				email: '',
				unit_id: '',
				password: '',
				password2: '',
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
			this.fetch();
        	Fire.$on('reload', () => {
            	this.fetch();
        	})
		},

		methods: {
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
			deleteUser(id){
				swal.fire({
					title: 'Delete this user?',
					text: "You won't be able to revert this!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, delete it!'
					}).then((result) => {
						if (result.value) {
							this.pending = true;
							axios.delete(`/api/users/${id}`, this.config).then(()=>{
								this.fetch();
								Fire.$emit('reload')
									swal.fire(
									'Deleted!',
									'User Deleted.',
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
				axios.get('/api/users', this.config)
					.then(response => {
						this.users = response.data;
						this.pagination.total = this.users.length;
					})
					.catch(errors => {
						console.log(errors);
					});
			},
			getUser(id) {
              axios.get('/api/users/'+id,this.config)
              .then(({data}) => {
				this.fetchUnit();
                this.id = data.id;
                this.name = data.name;
				this.email = data.email;
				this.unit_id = data.unit_id;
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

			setRole(id, index) {
				if (confirm("Apakah Anda Yakin ?")) {
					axios.get('/api/users/role/' + id + '/' + index, this.config)
						.then(({ data }) => {
							if (data.error == false) {
								this.$store.dispatch("success", data.message).then(() => {
									this.fetch();
								});
							} else {
								alert(data.message);
							}
						})
						.catch((error) => {
							if (error.response.status == 401) {
								alert("Token Expired");
								this.$store.dispatch('logout').then(() => {
									this.$router.push("/login");
								});
							} else {
								alert("Terjadi Kesalahan pada server");
							}
							this.pending = false;
						});
				}
			},

			setStatus(id, index) {
				if (confirm("Apakah Anda Yakin ?")) {
					axios.get('/api/users/status/' + id, this.config)
						.then(({ data }) => {
							if (data.error == false) {
								this.$store.dispatch("success", data.message).then(() => {
									this.fetch();
								});
							} else {
								alert(data.message);
							}
						})
						.catch((error) => {
							if (error.response.status == 401) {
								alert("Token Expired");
								this.$store.dispatch('logout').then(() => {
									this.$router.push("/login");
								});
							} else {
								alert("Terjadi Kesalahan pada server");
							}
							this.pending = false;
						});
				}
			},
			check(){
				if(this.password2!=this.password){
					swal.fire("Failed!", "Password tidak sama", "warning");
					this.password = '';
					this.password2 = '';
				}else{
					this.store();
				}
			},
			checkUpdate(){
				if(this.password2!=this.password){
					swal.fire("Failed!", "Password tidak sama", "warning");
					this.password = '';
					this.password2 = '';
				}else{
					this.update();
				}
			},
			store(){
                this.pending = true;
				var formData = new FormData();
                formData.append("name",this.name);   
                formData.append("email",this.email);   
                formData.append("unit_id",this.unit_id);   
                formData.append("password",this.password);  
                axios.post('/api/register',formData,this.config)
                .then(({data}) => {
                    if(data.error == false){
                         this.$store.dispatch("success", data.message).then(() => {                            
                          //code
						  	this.fetch();
							swal.fire("Berhasil!", "User baru telah ditambahkan", "success");

                          });                                            
                    }else{
						this.fetch();
                        swal.fire("Berhasil!", "User baru telah ditambahkan", "success");
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
                        swal.fire("Gagal!", 'Email telah digunakan', "warning");
                    } 
                    this.pending = false;
                 });  
			},
			update(){
                this.pending = true;
				var formData = new FormData();
                formData.append("name",this.name);   
                formData.append("email",this.email);   
				formData.append("unit_id",this.unit_id);   
				if(this.password!=null){
					formData.append("password",this.password); 
				}
                axios.post('/api/users/'+this.id,formData,this.config)
                .then(({data}) => {
                    if(data.error == false){
                         this.$store.dispatch("success", data.message).then(() => {                            
                          //code
						  	this.fetch();
							swal.fire("Berhasil!", "User baru telah diperbaharui", "success");

                          });                                            
                    }else{
						this.fetch();
                        swal.fire("Berhasil!", "User baru telah diperbaharui", "success");
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
                        swal.fire("Gagal!", 'Email telah digunakan', "warning");
                    } 
                    this.pending = false;
                 });  
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
			filteredUsers() {
				let users = this.users;
				if (this.search) {
					users = users.filter((row) => {
						return Object.keys(row).some((key) => {
							return String(row[key]).toLowerCase().indexOf(this.search.toLowerCase()) > -1;
						})
					});
				}
				let sortKey = this.sortKey;
				let order = this.sortOrders[sortKey] || 1;
				if (sortKey) {
					users = users.slice().sort((a, b) => {
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
				return users;
			},
			paginatedUsers() {
				return this.paginate(this.filteredUsers, this.length, this.pagination.currentPage);
			}
		}	
    };
</script>
