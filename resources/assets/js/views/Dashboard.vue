<template>
  <div class="animated fadeIn">


    <!--row-->
    <div class="row" v-if="stats">
      <div class="col-sm-6 col-lg-6">

        <b-card class="" header="Warehouse items" >

          <ul class="horizontal-bars">

            <li v-for="warehouse in stats.warehouses">
              <div class="title" v-text="warehouse.name">
              </div>
              <div class="bars">
                <b-progress class="progress-xs" :max="stats.total_item_count" :value="warehouse.item_count" variant="info"></b-progress>
                <b-progress class="progress-xs" :max="stats.total_xem_balance" :value="warehouse.xem_balance" variant="success"></b-progress>
              </div>
            </li>

            <li class="legend">
              <span class="badge badge-pill badge-info"></span> <small>Items</small> &nbsp; <span class="badge badge-pill badge-success"></span> <small>XEM</small>
            </li>

          </ul>
        </b-card>



      </div><!--/.col-->

      <div class="col-sm-6 col-lg-6">
        <b-card class="" header="Warehouse funds" >

          <ul class="horizontal-bars type-2">

            <li v-for="warehouse in stats.warehouses">
              <div class="title" v-text="warehouse.name">
              </div>
              <span class="value"><span class="text-muted small">{{ warehouse.xem_balance }} XEM</span></span>
              <div class="bars">
                <b-progress class="progress-xs" :max="stats.total_xem_balance" :value="warehouse.xem_balance" variant="success"></b-progress>
              </div>
            </li>


          </ul>
        </b-card>
      </div>

      <div class="col-sm-12 col-lg-12">

        <b-card class="" header="Latest transfers" >

        </b-card>

      </div>
    </div>


  </div>
</template>

<script>
export default {
  name: 'dashboard',

  data: function () {
      return {
          stats: undefined,
      }
  },
  methods: {

  },
  mounted() {
      axios.get('/api/user/stats/001')
          .then(response => {
              this.stats = response.data;

          });
  }
}
</script>
<style lang="scss">
  .horizontal-bars li {

    .title{
      line-height: normal;
    }
  }
</style>
