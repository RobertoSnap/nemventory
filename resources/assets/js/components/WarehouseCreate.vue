<template>

    <b-form @submit.prevent="onSubmit"
            @keydown="form.errors.clear($event.target.name)" @keydown.enter.prevent="">
        <div class="col-md-12">

        <b-form-group label-for="newWarehouseName">
            <b-form-input id="newWarehouseName"
                          type="text" v-model="form.name" required
                          placeholder="Enter name of new warehouse"
            ></b-form-input>
            <span class="help text-red" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></span>
        </b-form-group>


        <b-button class="pull-right" type="submit" variant="primary">Create</b-button>
        <span class="help text-red" v-if="form.errors.has('form')" v-text="form.errors.get('form')"></span>
        </div>
    </b-form>





</template>
<script>
    import Form from '../packages/form';
    export default {
        data: function () {
            return {
                form: new Form({
                    name: '',
                }),
            }
        },
        computed: {

        },
        methods: {
            onSubmit() {
                this.form.post('/api/user/warehouse')
                    .then(response => {
                        console.log("Finished with onSubmit");
                        this.defaultData();
                    })
                    .catch(error => {
                        console.log("error");
                    })
            },

        },
        mounted: function () {

        },
    }

</script>
