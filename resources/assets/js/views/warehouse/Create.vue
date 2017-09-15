<template>
    <div>
        <b-card title="Create simple warehouse">
            <WarehouseCreate></WarehouseCreate>
        </b-card>
        <b-card title="Import multisig warehouse">
            <WarehouseImport></WarehouseImport>
        </b-card>

    </div>
</template>

<script>
    import WarehouseCreate from '../../components/WarehouseCreate.vue';
    import WarehouseImport from '../../components/WarehouseImport.vue';
    export default {
        components: {
            WarehouseCreate: WarehouseCreate,
            WarehouseImport: WarehouseImport,
        },
        data: function () {
            return {
                test: "Test123",
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

<style>

</style>