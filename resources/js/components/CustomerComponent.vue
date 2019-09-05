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
            <label>Telephone numbers</label>
            <div class="form-group" v-for="(input,k) in inputs" :key="k">
                <input type="text" class="form-control" placeholder="Telephone" v-model="input.name">
                <span>
        <i class="fa fa-minus pull-right" style="color: red;border: 1px solid;padding: 4px;border-radius: 50%;"
           @click="remove(k)" v-show="k || ( !k && inputs.length > 1)"></i>
        <i class="fa fa-plus pull-right" style="color: green;border: 1px solid;padding: 4px;border-radius: 50%;"
           @click="add(k)" v-show="k == inputs.length-1"></i>
    </span>
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
                <td>
                    <ul v-for="telephoneDetails in customer.telephones">
                        <li>{{ telephoneDetails.telephone }}</li>
                    </ul>
                </td>
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
                inputs: [
                    {
                        name: ''
                    }
                ],
                customerRecord: {
                    id: '',
                    name: '',
                    nic: '',
                    address: '',
                    telephones: [],
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
            add(index) {
                this.inputs.push({name: ''});
            },
            remove(index) {
                this.inputs.splice(index, 1);
            },
            fetchCustomerRecords(page_url) {
                let vm = this;
                page_url = page_url || 'api/customer';
                fetch(page_url)
                    .then(res => res.json())
                    .then(res => {
                        this.customerRecords = res.data;
                        console.log(res.data[0]['telephones'][0]['telephone']);
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
            addCustomerRecord() {
                if (this.edit === false) {
                    //add
                    fetch('api/customer', {
                        method: 'post',
                        body: JSON.stringify([this.customerRecord, this.inputs]),
                        headers: {
                            'content-type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.customerRecord.name = '';
                            this.customerRecord.nic = '';
                            this.customerRecord.address = '';
                            this.inputs = [{name: ''}];
                            alert('Customer Record Successfully Added!')
                            this.fetchCustomerRecords();
                        })
                        .catch(err => console.log(err));
                } else {
                    //update

                    fetch(`api/customer/${this.customerRecord.id}`, {
                        method: 'put',
                        body: JSON.stringify([this.customerRecord, this.inputs]),
                        headers: {
                            'content-type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.customerRecord.name = '';
                            this.customerRecord.nic = '';
                            this.customerRecord.address = '';
                            this.inputs = [{name: ''}];
                            alert('Customer Record Successfully Updated!')
                            this.fetchCustomerRecords();
                        })
                        .catch(err => console.log(err));
                }
            },
            editCustomer(customerRecord) {
                this.edit = true;
                this.customerRecord.id = customerRecord.id;
                this.customerRecord.customer_id = customerRecord.id;
                this.customerRecord.name = customerRecord.name;
                this.customerRecord.nic = customerRecord.nic;
                this.customerRecord.address = customerRecord.address;
                this.inputs = customerRecord.telephones.length === 0 ?
                    [{name: ''}] :
                    customerRecord.telephones.length === 1 ?
                        [{name: customerRecord.telephones[0]['telephone']}] :
                        customerRecord.telephones.length === 2 ?
                            [{name: customerRecord.telephones[0]['telephone']}, {name: customerRecord.telephones[1]['telephone']}] :
                            customerRecord.telephones.length === 3 ?
                                [{name: customerRecord.telephones[0]['telephone']}, {name: customerRecord.telephones[1]['telephone']}, {name: customerRecord.telephones[2]['telephone']}] :
                                customerRecord.telephones.length === 4 ?
                                    [{name: customerRecord.telephones[0]['telephone']}, {name: customerRecord.telephones[1]['telephone']}, {name: customerRecord.telephones[2]['telephone']}, {name: customerRecord.telephones[3]['telephone']}] :
                                    customerRecord.telephones.length === 5 ?
                                        [{name: customerRecord.telephones[0]['telephone']}, {name: customerRecord.telephones[1]['telephone']}, {name: customerRecord.telephones[2]['telephone']}, {name: customerRecord.telephones[3]['telephone']}, {name: customerRecord.telephones[4]['telephone']}] :
                                        [{name: ''}];
            }
        },

    }
</script>
