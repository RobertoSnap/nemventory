<template>
    <b-alert :show="dismissCountDown"
             dismissible
             :variant="alert.type"
             @dismissed="dismissCountdown=0"
             @dismiss-count-down="countDownChanged">
        <h4 v-if="alert.heading" v-text="alert.heading"></h4>
        <p class="body">{{ alert.message }}</p>
        <b-progress :variant="alert.type"
                    :max="dismissSecs"
                    :value="dismissCountDown"
                    class="progress-xs">
        </b-progress>
    </b-alert>
</template>

<script>
    export default {
        props: [ 'alert' ],
        data: function () {
            return {
                dismissSecs: 6,
                dismissCountDown: 6,
                showDismissibleAlert: false
            }
        },
        methods: {
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown;
            },
            showAlert() {
                this.dismissCountDown = this.dismissSecs;
            }
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