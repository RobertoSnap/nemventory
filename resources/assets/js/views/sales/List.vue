<template>
    <div>
        <b-table :items="items" :fields="fields"  v-if="warehouses">

            <template slot="customer" scope="row">
                <span>{{ warehouses[row.item.customer_warehouse_id].name }}</span> <br> <small class="text-muted">{{ prettyAddress( warehouses[row.item.customer_warehouse_id].address ) }}</small>
            </template>
            <template slot="warehouse" scope="row" >
               <span>{{ warehouses[row.item.vendor_warehouse_id].name }}</span> <br> <small class="text-muted">{{ prettyAddress( warehouses[row.item.vendor_warehouse_id].address ) }}</small>
            </template>
            <template slot="actions" scope="row">
                <!-- We use click.stop here to prevent a 'row-clicked' event from also happening -->
                <router-link v-if="row.item.status === 'created'" :to="'/sales/'+ row.item.id" class="btn btn-primary" >Edit</router-link>
                <button v-if="row.item.status !== 'sent' " v-on:click="sendOrder(row.item.id)" class="btn btn-primary" >Send</button>
            </template>

        </b-table>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                items: [],
                fields: {
                    id:     { label: 'Order id', sortable: true },
                    customer:     { label: 'Customer', sortable: true },
                    warehouse:   { label: 'Warehouse', sortable: true },
                    status: { label: 'Status', formatter: 'status' , 'class': 'text-center'},
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
            sendOrder(id){
                axios.post('/api/user/sales-order/'+id+'/send')
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

                axios.get('/api/user/sales-order')
                    .then(response => {
                        this.items = response.data;
                    });
            }
        },
        mounted() {
            console.log('Sales list mounted.')
            this.getData();
        },
    }
</script>

<style lang="scss">


</style>