<template>
    <div id="alert" class="">
        <div v-for="alert in alerts">
            <AlertDismiss v-if="alert.dismiss === true" :alert="alert" > </AlertDismiss>
            <AlertRegular v-if="alert.dismiss !== true" :alert="alert" > </AlertRegular>
        </div>
    </div>

</template>

<script>
    import Pusher from '../packages/pusher'
    import AlertDismiss from './AlertDismiss.vue'
    import AlertRegular from './AlertRegular.vue'
    export default {
        components: { AlertDismiss,AlertRegular },
        data: function() {
            return {
                alerts: [],
                warehouses: undefined,
                userId: undefined,
            }
        },
        methods: {
            channels() {
                axios.get('/api/user/warehouse')
                    .then(response => {
                        this.warehouses = response.data;
                        this.setWarehouseChannels();
                    });
                axios.get('/api/user')
                    .then(response => {
                        this.userId = response.data;
                        this.setUserChannels();
                    });
            },
            setWarehouseChannels(){
                //Warehouse channels
                for (let key in this.warehouses){
                    window.Echo.channel('warehouse.'+this.warehouses[key].id)
                        .listen("WarehouseMovement", (e) => {
                            console.log(e);
                            this.alerts.push(e)
                        });
                }
            },
            setUserChannels(){
                //User channel
                window.Echo.private('user.'+this.userId)
                    .listen("UserMovement", (e) => {
                        console.log(e);
                        this.alerts.push(e)
                    });
            }
        },
        mounted () {
            this.channels();
        },
    }
</script>

<style>
    #alert{
        z-index: 999;
        position: fixed;
        right:5px;
        top: 5px;
    }
</style>