<template>
    <!-- Modal -->
    <div class="modal fade" id="AddNewUserModal" tabindex="-1" role="dialog" aria-labelledby="AddNewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddNewUserModalLabel">{{ titleModal }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode ? updateUser() : createUser()">
                    <div class="modal-body">
                        <div class="form-group">
                            <input v-model="form.name" type="text" name="name" placeholder="Name"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <input v-model="form.email" type="email" name="email" placeholder="Email"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>
                        <div class="form-group">
                            <textarea v-model="form.bio" type="text" name="bio" placeholder="Short Bio"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                            </textarea>
                            <has-error :form="form" field="bio"></has-error>
                        </div>
                        <div class="form-group">
                            <select name="type" v-model="form.type" id="type" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                                <option value="author">Author</option>
                            </select>
                            <has-error :form="form" field="type"></has-error>
                        </div>
                        <div class="form-group" v-bind:class="{ 'd-none': !showPasswordBlock }">
                            <input v-model="form.password" type="password" name="password" placeholder="Password"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                            <has-error :form="form" field="password"></has-error>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">{{ modalButtonName }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "AddNewUserModal",
        props: ['form'],
        data() {
            return{
                editMode: '',
                titleModal: '',
                showPasswordBlock: '',
                modalButtonName: ''
            }
        },
        methods: {
            createUser() {
                let vm = this;
                this.$Progress.start();
                this.form.post('api/user')
                    .then(res => {
                        vm.$emit('refreshData');
                        $('#AddNewUserModal').modal('hide');
                        toast.fire({
                            icon: 'success',
                            title: 'User created'
                        })
                        vm.$Progress.finish();
                    })
                    .catch(err =>{
                        toast.fire({
                            icon: 'error',
                            title: 'An error has occurred'
                        })
                        vm.$Progress.fail();
                    })
            },

            editModal(user) {
                this.form.reset();
                $('#AddNewUserModal').modal('show');
                this.titleModal = 'Edit User';
                this.form.fill(user);
                this.showPasswordBlock = false;
                this.modalButtonName = 'Update';
                this.editMode = true;
            },

            updateUser() {
                this.$Progress.start()
                this.form.put('api/user/'+this.form.id)
                    .then(ress => {
                        swal.fire(
                            'Updated!',
                            'User has been updated.',
                            'success'
                        )
                        this.$emit('refreshData');
                        $('#AddNewUserModal').modal('hide');
                        this.$Progress.finish();
                    })
                    .catch(err => {
                        console.log(err);
                        this.$Progress.fail()
                        swal.fire(
                            'Error!',
                            'An Error has been occurred.',
                            'error'
                        )
                    })
            }
        }
    }
</script>

<style scoped>

</style>
