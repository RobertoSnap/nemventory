<template>
    <div class="col-md-12 sales-order" v-if="order && warehouses[order.customer_warehouse_id]">
        <b-form @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

            <!-- Header-->
            <div class=" row" v-if="">
                <div class="col-md-6">
                    <!--Vendor-->
                    <b-form-group label="Customer">
                        <b-form-input v-model="warehouses[order.customer_warehouse_id].name"
                                      type="text"
                                      disabled
                        ></b-form-input>
                    </b-form-group>

                    <b-form-group label="Warehouse">
                        <b-form-input v-model="warehouses[order.vendor_warehouse_id].name"
                                      type="text"
                                      disabled
                        ></b-form-input>
                    </b-form-group>

                    <b-form-group label="Status">
                        <b-form-input v-model="order.status"
                                      type="text"
                                      disabled
                        ></b-form-input>
                    </b-form-group>

                </div>

                <div class="col-md-6" v-if="order">

                    <b-form-group label="Comment">
                        <b-form-input v-model="order.comment"
                                      type="text"
                        ></b-form-input>
                    </b-form-group>

                </div>

            </div>


            <!-- Lines -->
            <div class="row">


                <table class="table">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Comment</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!--Each line-->
                    <tr v-for="(line, index) in order.lines">
                        <td>
                            <div class="form-group">
                                <b-form-input v-model="line.item_name"
                                              type="text"
                                              disabled
                                ></b-form-input>
                            </div>

                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" class="form-control" placeholder="" max="9000000000" min="0"
                                       v-model="line.quantity" disabled>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="(Optional)"
                                       v-model="line.comment" disabled>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>


            </div>


            <!-- Actions-->
            <div class="col-md-12">
                <b-button type="submit" variant="primary" class="btn btn-primary pull-right">Update sales order
                </b-button>
            </div>


        </b-form>
    </div>
</template>

<script>
    import Form from '../../packages/form';
    export default {
        props: ['id'],
        data: function () {
            return {
                form: new Form({
                    id: undefined,
                    comment: undefined,
                }),
                order: undefined,
                warehouses: [],

            }
        },
        computed: {
            'form.id': function () {
                return 24
            },

        },
        mounted() {
            this.getData();
            axios.get('/api/user/sales-order/' + this.id )
                .then(response => {
                    this.order = response.data;
                });
        },
        methods: {
            getData() {
                axios.get('/api/warehouse?byId=true')
                    .then(response => {
                        this.warehouses = response.data;
                    });
            },
            onSubmit() {
                this.form.id = this.id;
                this.form.comment = this.order.comment;
                this.form.put('/api/user/sales-order/' + this.id)
                    .then(response => {
                        console.log("Finished with onSubmit");
                    })
                    .catch(error => {
                        console.log("error");
                    });


            },
        }
    }
</script>

<style>

</style>