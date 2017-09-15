<template>
    <b-alert :variant="alert.type"
             dismissible
             :show="showDismissibleAlert"
             @dismissed="showDismissibleAlert=false">
        <div class="row">
            <div class="col-10">
                <h4 v-if="alert.heading" v-text="alert.heading"></h4>
                <span>{{ alert.message }} </span>
            </div>
            <div class="col-2">
                <template slot="dismiss" scope="row" >
                </template>
            </div>
        </div>



    </b-alert>
</template>

<script>
    export default {
        props: [ 'alert' ],
        data: function () {
            return {
                showDismissibleAlert: true
            }
        },
        methods: {

        },
        mounted () {
        },
    }
</script>

<style>
    h4{
        font-size: 16px;
    }
    p {
        font-size: 14px;
    }
</style>