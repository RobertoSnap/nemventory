<template>
    <div>
        <div class="my-1 row">
            <div class="col-sm-6">
                <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" />
            </div>
            <div class="col-sm-6">
                <b-form-fieldset horizontal label="Filter" :label-cols="3">
                    <b-form-input v-model="filter" placeholder="Type to Search" />
                </b-form-fieldset>
            </div>
        </div>



        <b-table :items="items" :fields="fields" :filter="filter"   :current-page="currentPage" :per-page="perPage">

            <template slot="name" scope="row">
                <!-- We use click.stop here to prevent a 'row-clicked' event from also happening -->
                <p> {{ row.item.mosaic.id.name }}</p>
            </template>

            <template slot="description" scope="row">
                <!-- We use click.stop here to prevent a 'row-clicked' event from also happening -->
                <p> {{ row.item.mosaic.description }}</p>
            </template>

            <template slot="actions" scope="row">
                <!-- We use click.stop here to prevent a 'row-clicked' event from also happening -->
                <router-link :to="'/item/'+  row.item.mosaic.id.namespaceId + '.' + row.item.mosaic.id.name" class="btn btn-primary" >Details</router-link>
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
                    name:               { label: 'Name', sortable: true },
                    description:        { label: 'Description',},
                    actions:            { label: 'Actions' }
                },
                currentPage: 1,
                perPage: 10,
                pageOptions: [{text:5,value:5},{text:10,value:10},{text:15,value:15}],
                sortBy: null,
                sortDesc: false,
                filter: null,
            }
        },
        computed: {
            totalRows: function () {
                return this.items.length;
            }
        },
        mounted() {
            axios.get('/api/item')
                .then(response => {
                    this.items = response.data;
                });
        }
    }
</script>

<style>

</style>