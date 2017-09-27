<template>
    <div>
        <b-table :items="items" :fields="fields" v-if="warehouses">

            <template slot="vendor" scope="row" >
                <span>{{ warehouses[row.item.vendor_warehouse_id].name }}</span> <br> <small class="text-muted">{{ prettyAddress( warehouses[row.item.vendor_warehouse_id].address ) }}</small></b-badge>
            </template>
            <template slot="customer" scope="row" >
               <span>{{ warehouses[row.item.customer_warehouse_id].name }}</span> <br> <small class="text-muted">{{ prettyAddress( warehouses[row.item.customer_warehouse_id].address ) }}</small>
            </template>
            <template slot="status" scope="row">

                <b-badge variant="primary" class="" >  </b-badge>
            </template>
            <template slot="actions" scope="row">
                <!-- We use click.stop here to prevent a 'row-clicked' event from also happening -->
                <router-link v-if="row.item.status === 'created'" :to="'/purchase/'+ row.item.id " class="btn btn-primary" >Edit</router-link>
                <button v-if="row.item.status === 'created'" v-on:click="receiveOrder(row.item.id)" class="btn btn-warning" >Receive</button>
            </template>


        </b-table>
        <h3 v-if="! items">No purchase orders, start by creating one?</h3>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                items: [],
                fields: {
                    id:     { label: 'Order id', sortable: true },
                    vendor:     { label: 'Vendor', sortable: true },
                    customer:   { label: 'Warehouse', sortable: true },
                    status: { label: 'Status',  sortable: true,  formatter: 'status' , 'class': 'text-center'},
                    actions:  { label: 'Actions' }
                },
                sortBy: null,
                sortDesc: false,
                filter: null,
                warehouses: [],
            }
        },
        methods: {

            prettyAddress(address){
                return address.match(/.{1,6}/g).join('-');
            },
            plainAddress(address){
                return address.replace(/-/g, '').trim()
            },
            status (value) {
                return value.charAt(0).toUpperCase() + value.slice(1);
            },
            receiveOrder(id){
                axios.post('/api/user/purchase-order/'+id+'/receive')
                    .then(response => {
                        console.log(response.data);
                        this.getData();
                    });
            },
            getData(){
                axios.get('/api/warehouse?byId=true')
                    .then(response => {
                        this.warehouses = response.data;
                    });

                axios.get('/api/user/purchase-order')
                    .then(response => {
                        this.items = response.data;
                    });
            }

        },
        mounted() {
            this.getData();
        }
    }
</script>

<style lang="scss">


</style>