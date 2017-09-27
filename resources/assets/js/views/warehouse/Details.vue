<template>
    <div>

        <div class="my-1 row">
            <div class="col-md-6">
                <b-form-fieldset horizontal label="Filter" :label-cols="3">
                    <b-form-input v-model="filter" placeholder="Type to Search" />
                </b-form-fieldset>
            </div>
        </div>



        <b-table :items="items" :fields="fields" :filter="filter" >

            <template slot="actions" scope="row">
                <!-- We use click.stop here to prevent a 'row-clicked' event from also happening -->
                <router-link :to="'/item/'+  row.item.namespaceId + '.' + row.item.name  " class="btn btn-primary" >Details</router-link>
                <router-link :to="'/sales/create/'+ row.item.name" class="btn btn-warning" >Sell</router-link>
            </template>

        </b-table>
    </div>
</template>

<script>
    export default {
        props: ['id'],
        data: function () {
            return {
                items: [],
                fields: {
                    name:     { label: 'Name', sortable: true },
                    quantity:   { label: 'Quantity', sortable: true, 'class': 'text-center'  },
                    actions:  { label: 'Actions' }
                },
                sortBy: null,
                sortDesc: false,
                filter: null,
            }
        },
        methods: {
            getData() {
                axios.get('/api/warehouse/'+this.id+'/item')
                    .then(response => {
                        this.items = response.data;
                    });
            },
            prettyAddress(address){
                return address.match(/.{1,6}/g).join('-');
            },
            plainAddress(address){
                return address.replace(/-/g, '').trim()
            },

        },
        mounted() {
            console.log('Warehouse DETAILS.')
            this.getData();
        },
        watch: {
            '$route' (to, from) {
                // react to changes between warehouse
            }
        }
    }
</script>

<style lang="scss">


</style>