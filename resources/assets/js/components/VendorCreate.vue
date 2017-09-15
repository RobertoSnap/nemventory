<template>
    <div>

        <div class="col-md-12">


            <div class="col-md-12">
                <b-card title="Import from address">
                    <form @submit.stop.prevent="submitImport">
                        <div class="form-group">
                            <b-form-input type="text"
                                          placeholder="NEM address"
                                          v-model="formImport.address"
                            ></b-form-input>
                        </div>
                        <dl class="dl-horizontal" v-if="formImport.address && ! addressData">
                            <dd>Not valid address</dd>
                        </dl>
                        <dl class="dl-horizontal" v-if="addressData">
                            <dt>Allready created:</dt>
                            <dd v-text=" (this.addressData.name) ? 'Yes' : 'No'"></dd>
                            <dt v-if="addressData.name">Warehouse name:</dt>
                            <dd v-if="addressData.name">{{ addressData.name}}</dd>
                        </dl>


                        <div class="form-group">
                            <b-form-input type="text"
                                          placeholder="Set vendor Name"
                                          v-model="formImport.name"
                                          v-if="( addressData && ! addressData.name  )"
                            ></b-form-input>
                        </div>

                        <b-button type="submit"
                                  :disabled=" !( addressData && ( addressData.name || formImport.name ) )">
                            Import as vendor
                        </b-button>
                    </form>

                </b-card>
            </div>


            <div class="col-md-12">
                <b-card title="New">
                    <form @submit.stop.prevent="submitCreate">
                        <div class="form-group">
                            <b-form-input type="text"
                                          placeholder="Vendor Name"
                                          v-model="formCreate.name"></b-form-input>
                        </div>
                        <b-button type="submit" :disabled=" ! formCreate.name ">Create</b-button>
                    </form>
                </b-card>
            </div>


        </div>
    </div>
</template>

<script>
    import Form from '../packages/form';
    export default {
        data: function () {
            return {
                formImport: new Form({
                    address: undefined,
                    name: undefined,
                }),
                formCreate: new Form({
                    name: undefined,
                }),
                addressData: undefined,
            }
        },
        watch: {
            'formImport.address': function (value) {
                this.getAddressData(value);
            }
        },
        methods: {
            submitImport() {
                this.formImport.post('/api/user/vendor/import')
                    .then(response => {
                        console.log(response);

                    })
                    .catch(error => {
                        console.log("Error Import: ");
                        console.log(error);
                    });


            },
            submitCreate() {
                this.formCreate.post('/api/user/vendor/create')
                    .then(response => {
                        console.log(response);


                    })
                    .catch(error => {
                        console.log("Error create: ");

                    });
                this.$root.$emit('hide::modal');

            },
            getAddressData: function (value) {
                console.log("ran with " + value);

                if (this.formImport.address !== undefined && this.formImport.address.length > 39) {
                    axios.get('/api/warehouse/info?address=' + this.formImport.address)
                        .then(response => {
                            console.log(response);
                            this.addressData = response.data;
                        })
                        .catch(error => {
                            console.log(error);
                        })
                }
            }
        },
        mounted() {

        }
    }
</script>

<style>

</style>