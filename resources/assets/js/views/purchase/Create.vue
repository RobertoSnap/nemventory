<template>
    <div class="col-md-12 purchase-order">
        <b-form @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

            <!-- Header-->
            <div class="col-md-12">

                <!--Vendor-->
                <b-form-group label="Vendor" description="Choose or create new vendor by inputting address">
                    <v-select
                            v-model="form.vendor"
                            :options="vendors"
                            label="name"
                            :getOptionLabel="getWarehouseLabel"
                    >
                        <span slot="no-options">
                                  No vendor found.
                              <b-btn  variant="link" v-b-modal.new-vendor>Add new?</b-btn>
                        </span>
                    </v-select>
                    <b-badge variant="warning" v-if="form.errors.has('vendor')"
                             v-text="form.errors.get('vendor')"></b-badge>
                    <b-badge variant="warning" v-if="form.errors.has('vendor.address')"
                             v-text="form.errors.get('vendor.address')"></b-badge>
                </b-form-group>


                <!--Warehouse-->
                <b-form-group label="Warehouse" description="Choose warehouse to receive items.">
                    <v-select
                            v-model="form.warehouse"
                            :options="warehouses"
                            label="name"
                            :getOptionLabel="getWarehouseLabel"
                    >
                        <span slot="no-options">
                                  No warehouse found.
                              <router-link :to="'/warehouse/create'">Create new?</router-link>
                        </span>
                    </v-select>
                    <b-badge variant="warning" v-if="form.errors.has('warehouse')"
                             v-text="form.errors.get('warehouse')"></b-badge>
                </b-form-group>

                <!-- Comment -->
                <b-form-group label="Comment">
                    <textarea class="form-control" placeholder="(optional)" maxlength="32"
                              v-model="form.comment"></textarea>
                    <b-badge variant="warning" v-if="form.errors.has('comment')"
                             v-text="form.errors.get('comment')"></b-badge>
                </b-form-group>

            </div>


            <!-- Lines -->
            <div class="col-md-12">


                <table class="table">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Comment</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!--Each line-->
                    <tr v-for="(line, index) in form.lines">
                        <td>
                            <div class="form-group">
                                <v-select v-model="line.item" :options="items" label="label"
                                          :getOptionLabel="getItemLabel" :clearSearchOnSelect="true">
                                            <span slot="no-options">
                                              No items found
                                            </span>
                                </v-select>
                            </div>
                            <b-badge variant="warning" v-if="form.errors.has('lines.'+index+'.item')"
                                     v-text="form.errors.get('lines.'+index+'.item')"></b-badge>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" class="form-control" placeholder="" max="9000000000" min="0"
                                       v-model="line.quantity">
                                <b-badge variant="warning" v-if="form.errors.has('lines.'+index+'.quantity')"
                                         v-text="form.errors.get('lines.'+index+'.quantity')"></b-badge>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="(Optional)"
                                       v-model="line.comment">
                                <b-badge variant="warning" v-if="form.errors.has('lines.'+index+'.comment')"
                                         v-text="form.errors.get('lines.'+index+'.comment')"></b-badge>
                            </div>
                        </td>
                        <td>
                            <b-button type="button" variant="outline-success" v-on:click="addLine()"><i
                                    class="icon-plus"></i> Add
                            </b-button>
                            <b-button type="button" variant="outline-danger" v-on:click="removeLine(index)"><i
                                    class="icon-minus"></i> Remove
                            </b-button>
                        </td>
                    </tr>
                    </tbody>
                </table>


            </div>


            <!-- Actions-->
            <div class="col-md-12">
                <b-button type="submit" variant="primary" class="btn btn-primary pull-right">Create purchase order
                </b-button>
            </div>


        </b-form>
        <b-modal id="new-vendor"
                 ref="new-vendor"
                 title="Create vendor"
                 @ok="handleOk"
                 :ok-only="true"
                 :ok-title="'Close'"
        >

          <VendorCreate></VendorCreate>


        </b-modal>
    </div>


</template>
<script>
    import Form from '../../packages/form';
    import VendorCreate from '../../components/VendorCreate.vue';
    import vSelect from "vue-select"
    export default {
        components: {vSelect, VendorCreate},
        data: function () {
            return {
                form: new Form({
                    warehouse: undefined,
                    vendor: undefined,
                    comment: null,
                    lines: [],
                }),

                warehouses: [],
                vendors: [],
                items: [],
            }
        },
        computed: {},
        methods: {
            handleOk () {
                this.getData();
            },
            submitable(){
                let submitable = true;
                for (let value in this.form) {
                    if (this.form[value] === undefined) {
                        console.log("Submit stopped because values in form are undefined.");
                        submitable = false;
                        break;
                    }
                }
                return submitable;
            },
            onSubmit() {
                if (this.submitable()) {
                    this.form.post('/api/user/purchase-order')
                        .then(response => {
                            console.log("Finished with onSubmit");
                            this.form.lines = [];
                            this.addLine();
                        })
                        .catch(error => {
                            console.log("error");
                        });
                }

            },
            getWarehouseLabel(option)
            {
                switch (typeof option) {
                    case "string":
                        return option;
                        break;
                    case "object":
                        if (option.name && option.address) {
                            return option.name + " - " + option.address;
                        }
                        return option.name;
                        break;
                    default:
                        return "Error label";
                }
            },
            getItemLabel(option) {

                return option.mosaic.id.name + " - " + option.mosaic.description.substring(0,15) +"...";
            },
            newLine() {
                return {
                    item: undefined,
                    quantity: undefined,
                    comment: null,
                }
            },
            addLine() {
                this.form.lines.push(this.newLine());
            },
            removeLine(index) {
                if (index > 0) {
                    this.form.lines.splice(index)
                }
            },
            getData() {
                axios.get('/api/user/warehouse')
                    .then(response => {
                        this.warehouses = response.data;
                    });

                axios.get('/api/user/vendor')
                    .then(response => {
                        this.vendors = response.data;
                    });

                axios.get('/api/item')
                    .then(response => {
                        this.items = response.data;
                    });
            }
        },
        mounted: function () {
            this.addLine();
            this.getData();

        },
    }

</script>
<style>
    .ellipsis {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>