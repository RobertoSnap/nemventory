<template>
    <div>
        <b-card :title="itemData.mosaic.id.name"
                img-src="https://lorempixel.com/600/300/food/5/"
                img-alt="Image"
                img-top
                tag="article"
                style=""
                class="col-12"
                v-if="itemData"
        >
            <div class="card-text">

                <div class="row">
                    <p class="col">Description</p>
                    <p class="col align-self-end" v-text="itemData.mosaic.description"></p>
                </div>

                <div class="row">
                    <p class="col">Creator</p>
                    <p class="col align-self-end" v-text="itemData.mosaic.creator"></p>
                </div>

                <div class="row" v-for="property in itemData.mosaic.properties">
                    <p class="col" v-text="property.name"></p>
                    <p class="col align-self-end" v-text="property.value"></p>
                </div>

            </div>
        </b-card>
    </div>
</template>

<script>
    export default {
        props: ['id'],
        data: function () {
            return {
                itemData: null,


            }
        },
        methods: {
            getData() {
                axios.get('/api/item/' + this.id + '')
                    .then(response => {
                        this.itemData = response.data;
                    });
            },
        },
        mounted() {
            console.log('Item details mounted.')
            this.getData();
        }
    }
</script>

<style>

</style>