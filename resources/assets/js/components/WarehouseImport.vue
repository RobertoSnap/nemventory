<template>
    <div class="col-md-12">
    <b-form role="form" method="post" @submit.prevent="onSubmit"
          @keydown="form.errors.clear($event.target.name)" @keydown.enter.prevent="">
        <!-- Address  -->
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Input NEM address"
                   v-model="addressInput">
            <span class="help text-red" v-if="form.errors.has('address')" v-text="form.errors.get('address')"></span>
        </div>


        <div class="callout callout-info" v-if="addressData">
            <dl class="dl-horizontal">

                <dt>Multisig</dt>
                <dd v-if="type === 'multisig'">Account is multisig, please choose cosigner to use below.</dd>
                <dd v-if="type === 'cosigner'">Account is cosigner of multisig, please choose multisig account below.
                </dd>
                <dd v-if="type === 'simple'">
                    Account is NOT multisig. Will create limited account for security reasons.
                </dd>

                <dt>Balance</dt>
                <dd v-text="addressBalance"></dd>

            </dl>
        </div>

        <!--Choose multisig-->
        <div class="form-group" v-if="type === 'cosigner'">
            <label>Choose multisig</label>
            <select class="form-control"
                    v-model="form.multisig">
                <option v-for="cosignerMultisig in cosignerMultisigs" :value="cosignerMultisig">
                    {{ prettyAddress(cosignerMultisig.address) }}  Balance: {{ cosignerMultisig.balance / 1000000 }} XEM
                </option>
            </select>
            <span class="help-block text-red" v-if="form.errors.has('multisig')"
                  v-text="form.errors.get('multisig')"></span>
        </div>

        <!--Choose cosigner-->
        <div class="form-group" v-if="type === 'multisig'">
            <label>Choose cosigner</label>
            <select class="form-control"
                    v-model="form.cosigner">
                <option v-for="cosigner in cosigners" :value="cosigner"> {{ prettyAddress(cosigner.address)
                    }}  Balance: {{ cosigner.balance / 1000000 }} XEM
                </option>
            </select>
            <span class="help-block text-red" v-if="form.errors.has('cosigner')"
                  v-text="form.errors.get('cosigner')"></span>
        </div>

        <!--MULTISIG - Cosigners-->
        <div class="form-group" v-if="type === 'multisig' || type === 'cosigner'">
            <label>Cosigner Private key</label>
            <input type="password" class="form-control" placeholder="Cosigner private key..."
                   v-model="form.cosignerPrivateKey" autocomplete="new-password">
            <small>Only input private key for Cosigner. Your multisig should have two or more singatures required.
            </small>
            <span class="help-block text-red" v-if="form.errors.has('cosignerPrivateKey')"
                  v-text="form.errors.get('cosignerPrivateKey')"></span>
        </div>

        <!--ALL - Name  -->
        <div class="form-group" v-if="addressData">
            <label>Warehouse name</label>
            <input type="text" class="form-control" placeholder="Set a name"
                   v-model="form.name">
            <span class="help text-red" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></span>
        </div>


        <!--PO actions-->

            <button type="submit" class="btn btn-primary pull-right">Import</button>
            <span class="help text-red" v-if="form.errors.has('form')"
                  v-text="form.errors.get('form')"></span>


    </b-form >
    </div>
</template>
<script>
    import Form from '../packages/form';
    export default {
        data: function () {
            return {
                form: new Form({
                    name: '',
                    address: null,
                    multisig: null,
                    cosigner: null,
                    cosignerPrivateKey: null,
                    type: null,
                }),
                addressData: undefined,
                addressInput: undefined,
                selectedConsigner: undefined,
                selectedMultisig: undefined,
            }
        },
        watch: {
            addressInput: function (value) {
                this.getAccountInfo(this.plainAddress(value));
            }
        },
        computed: {
            isMultisig: function () {
                return !!(this.addressData !== undefined && ( this.addressData.meta.cosignatories.length || this.addressData.meta.cosignatoryOf.length));
            },
            type: function () {
                if (this.addressData !== undefined) {
                    if (this.addressData.meta.cosignatories.length) {
                        this.form.multisig = this.addressData.account;
                        this.form.type = "multisig";
                        return "multisig"
                    }
                    else if (this.addressData.meta.cosignatoryOf.length) {
                        this.form.cosigner = this.addressData.account;
                        this.form.type = "cosigner";
                        return 'cosigner'
                    }
                    else {
                        this.form.address = this.addressInput;
                        this.form.type = "simple";
                        return "simple"
                    }
                }
            },
            addressBalance: function () {
                if (this.addressData !== undefined) {
                    return this.addressData.account.balance / 1000000;
                }
            },
            cosigners: function () {
                if (this.addressData !== undefined) {
                    return this.addressData.meta.cosignatories;
                }
            },
            cosignerMultisigs: function () {
                if (this.addressData !== undefined) {
                    return this.addressData.meta.cosignatoryOf;
                }
            },
        },
        methods: {
            onSubmit() {
                this.form.post('/api/user/warehouse/import')
                    .then(response => {
                        console.log("Finished with onSubmit");
                        this.addressInput = '';
                        this.addressData = undefined;
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            prettyAddress(address){
                return address.match(/.{1,6}/g).join('-');
            },
            plainAddress(address){
                return address.replace(/-/g, '').trim()
            },
            getAccountInfo(address){
                if (address.length > 39) {
                    axios.get('/api/nem/account/info?address=' + address)
                        .then(response => {
                            console.log("Got account info");
                            console.log(response.data);
                            this.addressData = response.data;
                        })
                        .catch(error => {
                            console.log(error);
                            this.addressData = undefined;
                        })
                }
            }

        },
        mounted: function () {

        },
    }

</script>
