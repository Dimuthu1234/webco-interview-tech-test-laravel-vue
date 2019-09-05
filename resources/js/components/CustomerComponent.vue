<template>
    <div class="table-responsive">
        <form @submit.prevent="addCustomerRecord" class="mb-3">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Name" v-model="customerRecord.name">
            </div>

            <div class="form-group">
                <input type="text" class="form-control" placeholder="NIC"
                       v-model="customerRecord.nic">
            </div>

            <div class="form-group">
                <input type="text" class="form-control" placeholder="Address"
                       v-model="customerRecord.address">
            </div>

            <div class="form-group">
                <input type="text" class="form-control" placeholder="Telephone"
                       v-model="customerRecord.telephone">
            </div>

            <button type="submit" class="btn btn-light btn-block">Save</button>

        </form>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item"><a class="page-link"
                                                                                                href="#"
                                                                                                @click="fetchCustomerRecords(pagination.prev_page_url)">Previous</a>
                </li>
                <li class="page-item disabled"><a class="page-link text-dark" href="#">Page {{ pagination.current_page
                    }} of {{ pagination.last_page }}</a></li>
                <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item"><a class="page-link"
                                                                                                href="#"
                                                                                                @click="fetchCustomerRecords(pagination.next_page_url)">Next</a>
                </li>
            </ul>
        </nav>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>NIC</th>
                <th>Address</th>
                <th>Telephone</th>
                <th width="1%"></th>
                <th width="1%"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="customer in customerRecords" v-bind:key="customer.id">
                <td>{{ customer.id }}</td>
                <td>{{ customer.name }}</td>
                <td>{{ customer.nic }}</td>
                <td>{{ customer.address }}</td>
                <td>{{ customer.telephone }}</td>
                <td><a href="#" @click="editCustomer(customer)">Edit</a></td>
                <td><a href="#" @click="deleteCustomer(customer.id)">Delete</a></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                search: '',
                customerRecords: [],
                customerRecord: {
                    id: '',
                    name: '',
                    nic: '',
                    address: '',
                    telephone: '',
                },
                customer_id: '',
                pagination: {},
                edit: false
            }
        },
        created() {
            this.fetchCustomerRecords();
        },

        methods: {
            fetchCustomerRecords(page_url) {
                let vm = this;
                page_url = page_url || 'api/customer';
                fetch(page_url)
                    .then(res => res.json())
                    .then(res => {
                        this.customerRecords = res.data;
                        vm.makePagination(res.meta, res.links);
                    })
                    .catch(err => console.log(err));
            },
            makePagination(meta, links) {
                let pagination = {
                    current_page: meta.current_page,
                    last_page: meta.last_page,
                    next_page_url: links.next,
                    prev_page_url: links.prev,
                };

                this.pagination = pagination;
            },
            deleteCustomer(id) {
                if (confirm('Are you sure?')) {
                    fetch(`api/customer/${id}`, {
                        method: 'delete'
                    })
                        .then(res => res.json())
                        .then(data => {
                            alert('Customer record removed Successfully!');
                            this.fetchCustomerRecords();
                        })
                        .catch(err => console.log(err))
                }
            },
            addCustomerRecord(){
                if (this.edit === false){
                    //add
                    fetch('api/customer', {
                        method: 'post',
                        body: JSON.stringify(this.customerRecord),
                        headers: {
                            'content-type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.customerRecord.name = '';
                            this.customerRecord.nic = '';
                            this.customerRecord.address = '';
                            this.customerRecord.telephone = '';
                            alert('Customer Record Successfully Added!')
                            this.fetchCustomerRecords();
                        })
                        .catch(err => console.log(err));
                } else{
                    //update

                    fetch(`api/customer/${this.customerRecord.id}`, {
                        method: 'put',
                        body: JSON.stringify(this.customerRecord),
                        headers: {
                            'content-type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.customerRecord.name = '';
                            this.customerRecord.nic = '';
                            this.customerRecord.address = '';
                            this.customerRecord.telephone = '';
                            alert('Customer Record Successfully Updated!')
                            this.fetchCustomerRecords();
                        })
                        .catch(err => console.log(err));
                }
            },
            editCustomer(customerRecord){
                this.edit = true;
                this.customerRecord.id = customerRecord.id;
                this.customerRecord.customer_id = customerRecord.id;
                this.customerRecord.name = customerRecord.name;
                this.customerRecord.nic = customerRecord.nic;
                this.customerRecord.address = customerRecord.address;
                this.customerRecord.telephone = customerRecord.telephone;
            }
        },

    }
</script>
