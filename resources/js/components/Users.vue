<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdminOrAuthor()">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title float-left mb-3">Users Table</h3>

                        <div class="box-tools float-right mb-3" v-if="$gate.isAdmin()">
                            <button class="btn btn-success" @click="newModal">Add new user <i class="fas fa-user-plus"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody><tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Registered at</th>
                                <th v-if="$gate.isAdmin()">Modify</th>
                            </tr>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{ user.id }}</td>
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.type | upCase }}</td>
                                <td>{{ user.created_at | dateFilter }}</td>
                                <td v-if="$gate.isAdmin()">
                                    <button class="btn btn-primary ml-1" @click="editForm(user)">
                                        <i class="fa fa-edit white"></i>
                                    </button>
                                    <button class="btn btn-danger" @click="deleteUser(user.id)">
                                        <i class="fa fa-trash white"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody></table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- Pagination-->
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item" v-bind:class="[{ disabled: !pagination.prev_page_url }]">
                        <a class="page-link" href="#" @click="loadUsers(pagination.prev_page_url)">
                            Previous
                        </a>
                    </li>

                    <li class="page-item disabled"><a class="page-link text-dark" href="#">Page {{ pagination.current_page }} of {{ pagination.last_page }}</a></li>

                    <li class="page-item" v-bind:class="[{ disabled: !pagination.next_page_url }]">
                        <a class="page-link" href="#" @click="loadUsers(pagination.next_page_url)">
                            Next
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.Pagination-->
        </div>
        <div class="mt-5" v-if="!$gate.isAdminOrAuthor()">
            <not-found></not-found>
        </div>
        <AddNewUserModal v-bind:form="form" v-on:refreshData="loadUsers" ref="newModal"></AddNewUserModal>
    </div>
</template>
<script>
    import AddNewUserModal from "./layouts/AddNewUserModal";
    export default {
        name: "Profile",

        components: {
            AddNewUserModal
        },

        data(){
            return{
                users: {},
                pagination:{},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',
                })
            }
        },

        methods: {
            loadUsers(page_url) {
                page_url = page_url || 'api/user';
                if(this.$gate.isAdminOrAuthor()){
                    axios.get(page_url).then(({ data }) => {
                        this.users = data.data;
                        let pagination = {
                            current_page: data.current_page,
                            last_page: data.last_page,
                            next_page_url: data.next_page_url,
                            prev_page_url: data.prev_page_url
                        };
                        this.pagination = pagination;
                    });
                }
            },

            deleteUser(id){
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.form.delete('api/user/'+id)
                            .then(res => {
                                swal.fire(
                                    'Deleted!',
                                    'User has been deleted.',
                                    'success'
                                )
                                this.loadUsers();
                            })
                            .catch(err => {
                                swal.fire(
                                    'Failed!',
                                    'Something went wrong',
                                    'error'
                                )
                            })
                    }
                })
            },

            newModal() {
                this.form.reset();
                this.$refs.newModal.$data.titleModal = 'Add new user';
                this.$refs.newModal.$data.showPasswordBlock = true;
                this.$refs.newModal.$data.modalButtonName = 'Create';
                this.$refs.newModal.$data.editMode = false;
                $('#AddNewUserModal').modal('show');
            },

            editForm(user) {
                this.$refs.newModal.editModal(user);
            }
        },

        created() {
            this.$Progress.start();
            newVueInstance.$on('searching', (search) => {
                let query = search;
                console.log('query : ' + query);
                axios.get('api/findUser?q='+query)
                    .then(({data}) => {
                        this.users = data.data;
                        let pagination = {
                            current_page: data.current_page,
                            last_page: data.last_page,
                            next_page_url: data.next_page_url,
                            prev_page_url: data.prev_page_url
                        };
                        this.pagination = pagination;
                    })
                    .catch(err => console.log(err))
            })
            this.loadUsers();
            this.$Progress.finish();
        }
    }
</script>

<style scoped>

</style>
