<template>
    <div >
        <!-- Modal Component -->
        <b-modal id="onBoarding"
                 ref="onBoarding"
                 title="Get started"
                 @ok="handleOk"
                 :ok-only="true"
        >

            <p>Your account has been created <i class="icon-star"></i></p>
            <div v-if="warehouse">


                <p>To get you started we have taken the oppertunity to create a brand new warehouse in your name. Its called <span style="font-weight: bold" v-text="warehouse.name"> </span> with address <span style="font-weight: bold" v-text="prettyAddress(warehouse.address)"> </span></p>

                <p>Since we are in <b>BETA</b>, we are trying to transfer som test XEM too your new warehouse. We will notify you as soons as it arrives.</p>

                <p>By mistake we have transferred some items to this warehouse. Can you please sell them back to us or send them to someone in need?</p>
            </div>



        </b-modal>


    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                slide: 0,
                sliding: null,
                warehouse: undefined,
            }
        },
        mounted() {
            this.getData();

            //Test
            //localStorage.setItem("onBoardingDone", "false");

            if (typeof(Storage) !== "undefined") {
                console.log("Useing local storage");

                if( localStorage.getItem("onBoardingDone") === "true"){
                    console.log("OnBoarding done");


                } else {
                    console.log("Start Onboarding");
                    this.$root.$emit('show::modal','onBoarding');


                    
                }

            } else {
                console.log("NOT local storage");
            }
        },
        methods: {
            handleOk () {
                localStorage.setItem("onBoardingDone", "true");
            },
            onSlideStart(slide) {
                this.sliding = true;
            },
            onSlideEnd(slide) {
                this.sliding = false;
            },
            getData() {
                axios.get('/api/user/warehouse')
                    .then(response => {
                        this.warehouse = response.data[0];
                    });
            },
            prettyAddress(address){
                return address.match(/.{1,6}/g).join('-');
            },
            plainAddress(address){
                return address.replace(/-/g, '').trim()
            },

        }
    }
</script>

<style>

</style>