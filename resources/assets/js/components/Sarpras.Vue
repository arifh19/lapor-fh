<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Pendataan Sarana Prasarana</h1>
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
									<router-link  :to="{ name: 'sarpras-create'}" class="btn btn-primary" > <span class="fa fa-plus"></span>
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
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="sarp in paginatedUsers" :key="sarp.id">
											<td>{{sarp.ruangan}}</td>
											<td>{{sarp.kode}}</td>
											<td>{{sarp.nama}}</td>
											<td>{{sarp.spesifikasi}}</td>
											<td>{{sarp.kondisi}}</td>
											<td>{{sarp.status}}</td>
											<td>{{sarp.updated_at}}</td>
											<td>
												<router-link  :to="{ name: 'sarpras-edit', params: {id:sarp.id}}"  class="btn btn-outline-success">
													<i class="fa fa-edit"></i>
												</router-link>
												<button type="button" v-on:click="show(sarp.id)" class="btn btn-outline-info" data-toggle="modal" data-target="#modal-dokum"><i class="fa fa-search"></i></button>
												<button v-on:click="deleteUser(sarp.id)" type="button" class="btn btn-outline-danger">
													<i v-if="pending" class="fa fa-refresh fa-spin"></i>
													<i v-if="!pending" class="fa fa-trash"></i>
												</button>
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
			<div class="modal fade" id="modal-dokum">
				<div class="modal-dialog" style="width: 50%">
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
		</section>
		<!-- /.content -->
	</div>
</template>
<script>

    export default {
		props: {
          id: {
              type: String,
              required: true,
          },
        },
		props: ['id_notif'],

		data() {
			let sortOrders = {};
			let columns = [
				{label: 'Ruangan', name: 'ruangan' },
				{label: 'Kode Inventaris', name: 'kode'},
				{label: 'Nama Sarpras', name: 'nama'},
				{label: 'Spesifikasi', name: 'spesifikasi'},
				{label: 'Kondisi', name: 'kondisi'},
				{label: 'Status', name: 'status'},
				{label: 'Update at', name: 'updated_at'},
			];
			columns.forEach((column) => {
				sortOrders[column.name] = -1;
			});
			
			return {
				sarpras: [],
				columns: columns,
				sortKey: 'ruangan',
				sortOrders: sortOrders,
				length: 10,
				search: '',
				foto: '',
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
			deleteUser(id){
				swal.fire({
					title: 'Delete this Sarpras?',
					text: "You won't be able to revert this!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, delete it!'
					}).then((result) => {
						if (result.value) {
							this.pending = true;
							axios.delete(`/api/sarpras/${id}`, this.config).then(()=>{
								this.fetch();
								Fire.$emit('reload')
									swal.fire(
									'Deleted!',
									'Sarpras Deleted.',
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
				axios.get('/api/sarpras', this.config)
					.then(response => {
						console.log('The data: ', response.data)
						this.sarpras = response.data;
						this.pagination.total = this.sarpras.length;
					})
					.catch(errors => {
						console.log(errors);
					});
			},
			show(id) {
              axios.get('/api/sarpras/'+id,this.config)
              .then(({data}) => {
                this.foto=data.foto;
                this.id=data.id;
                this.keterangan=data.keterangan;
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
				let sarpras = this.sarpras;
				if (this.search) {
					sarpras = sarpras.filter((row) => {
						return Object.keys(row).some((key) => {
							return String(row[key]).toLowerCase().indexOf(this.search.toLowerCase()) > -1;
						})
					});
				}
				let sortKey = this.sortKey;
				let order = this.sortOrders[sortKey] || 1;
				if (sortKey) {
					sarpras = sarpras.slice().sort((a, b) => {
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
				return sarpras;
			},
			paginatedUsers() {
				return this.paginate(this.filteredUsers, this.length, this.pagination.currentPage);
			}
		}	
    };
</script>
