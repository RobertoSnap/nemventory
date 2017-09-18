<template>
    <b-form role="form" method="post" action="/item" @submit.prevent="onSubmit"
            @keydown="form.errors.clear($event.target.name)">


        <!-- Name -->
        <b-form-group label="Name" description="Must start with letter and only lower-case.">
            <b-form-input v-model="form.name"
                          type="text"
                          placeholder="Item name"
                          description="Must start with letter and only lower-case."
                          maxlength="32"
            ></b-form-input>
            <span class="help text-red" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></span>
        </b-form-group>

        <!-- Description -->
        <b-form-group label="Description">
            <textarea class="form-control" rows="3" placeholder="Enter description ..." maxlength="512"
                      v-model="form.description"></textarea>
            <span class="help text-red" v-if="form.errors.has('description')"
                  v-text="form.errors.get('description')"></span>
        </b-form-group>


        <!--Initial stock-->
        <b-form-group label="Initial stock">
            <b-form-input v-model="form.initialStock"
                          type="number"
                          placeholder="Set inital stock value"
            ></b-form-input>
            <span class="help text-red" v-if="form.errors.has('initialStock')"
                  v-text="form.errors.get('initialStock')"></span>
        </b-form-group>

        <b-form-group>
            <label>Divisibility</label>
            <b-form-select v-model="form.divisibility" :options="divisibilitys" class="mb-3" text-field="name">
            </b-form-select>
            <span class="help text-red" v-if="form.errors.has('divisibility')"
                  v-text="form.errors.get('divisibility')"></span>
        </b-form-group>

        <!-- Warehouse -->
        <b-form-group label="Warehouse" description="Select warehouse to receive inital supply">
            <select class="form-control"
                    v-model="form.warehouse">
                <option v-for="wallet in wallets" :value="wallet.address">{{ wallet.name }} - {{ wallet.address }}
                </option>
            </select>
            <span class="form-text text-muted" v-if="form.warehouse">Balance: </span><span class=""
                                                                                           v-if="form.warehouse"
                                                                                           v-text="warehouseBalance"></span>
            <span class="help-block text-red" v-if="form.errors.has('warehouse')"
                  v-text="form.errors.get('warehouse')"></span>
        </b-form-group>


        <!--Fee-->
        <b-form-group label="XEM Fee" description="Total fee for creating an item on NEMventory." label-for="xemFee">
            <b-form-input id="xemFee" type="number" v-model="form.fee" disabled></b-form-input>
            <span class="help text-red" v-if="form.errors.has('fee')" v-text="form.errors.get('fee')"></span>
        </b-form-group>

        <b-button class="col-12" type="submit" variant="primary">Request item</b-button>
        <span class="help text-red" v-if="form.errors.has('form')" v-text="form.errors.get('form')"></span>
    </b-form>
</template>
<script>
    import Form from '../../packages/form';
    export default {
        data: function () {
            return {
                form: new Form({
                    name: '',
                    description: '',
                    initialStock: '',
                    divisibility: 0,
                    warehouse: '',
                    fee: ''
                }),
                wallets: [],
                itemRequests: [],
                divisibilitys: [
                    {
                        value: 0,
                        name: "1"
                    },
                    {
                        value: 1,
                        name: "0,1"
                    },
                    {
                        value: 2,
                        name: "0,01"
                    },
                    {
                        value: 3,
                        name: "0,001"
                    },
                    {
                        value: 4,
                        name: "0,0001"
                    },
                    {
                        value: 5,
                        name: "0,00001"
                    },
                    {
                        value: 6,
                        name: "0,000001"
                    },
                ],
                warehouseBalance: null,
            }
        },
        watch: {
            'form.warehouse': function (address) {
                if (address !== undefined) {
                    axios.get('/api/nem/account/balance?address=' + address)
                        .then(res => {
                            this.warehouseBalance = res.data;
                        })
                }
            }
        },
        methods: {
            onSubmit() {
                this.form.post('/api/user/itemrequest')
                    .then(response => {
                        console.log("Finished with onSubmit");
                        this.defaultData();
                    })
                    .catch(error => {
                        console.log("error");
                    })
            },
            defaultData() {
    //                this.form.name = "test" + Math.floor((Math.random() * 10000) + 1);
    //                this.form.description = "Testing now";
    //                this.form.initialStock = 500;
    //                this.form.divisibility = 0;


                //Get fee
                axios.get('/api/fee/itemrequest')
                    .then(res => this.form.fee = res.data)
                    .catch(error => {
                        console.log("Error in gettting FEE")
                    });

                //Get wallets
                axios.get('/api/user/warehouse')
                    .then(res => this.wallets = res.data)
                    .catch(error => {
                        console.log("Error in gettting user wallets")
                    })

            }
        },
        mounted: function () {
            this.defaultData();


        },
    }

</script>
