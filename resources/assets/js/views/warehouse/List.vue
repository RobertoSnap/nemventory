<template>
    <div>
        <b-table :items="warehouses" :fields="fields" >

            <template slot="address" scope="row"><b-badge variant="primary">{{ prettyAddress(row.value) }}</b-badge></template>
            <template slot="actions" scope="row">
                <!-- We use click.stop here to prevent a 'row-clicked' event from also happening -->
                <router-link :to="'/warehouse/'+ plainAddress ( row.item.address ) " class="btn btn-primary" >Details</router-link>
            </template>

        </b-table>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                warehouses: [],
                fields: {
                    id:     { label: 'Id', sortable: true },
                    name:   { label: 'Name', sortable: true, 'class': 'text-center'  },
                    address: { label: 'Address' },
                    actions:  { label: 'Actions' }
                },
                sortBy: null,
                sortDesc: false,
                filter: null,
            }
        },
        methods: {
            prettyAddress(address){
                return address.match(/.{1,6}/g).join('-');
            },
            plainAddress(address){
                return address.replace(/-/g, '').trim()
            },

        },
        mounted() {
            axios.get('/api/user/warehouse')
                .then(response => {
                    console.log(response);
                    this.warehouses = response.data;
                });
        }
    }
</script>

<style lang="scss">


</style>