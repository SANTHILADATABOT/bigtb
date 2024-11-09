<script>
import MaterialsMixin from "@components/global-materials/mixin.js";
import Order from "@components/global-materials/order.vue";
import Vendor from "@components/global-materials/vendor.vue";

export default {
  name: "orders",
  components: {Order, Vendor},
  mixins: [MaterialsMixin],
  computed: {
    orders() { return this.$store.state.materialOrders },
    vendors() { return this.$store.state.materialVendors },
    users() { return this.$store.state.users }
  },
  watch: {
    orders() {
      console.log("Orders updated: ", this.orders);
    }
  }
}
</script>

<template>
  <div id="material-orders">
    <b-row v-for="v in vendors" :key="v.id || index">
      <b-col cols="12">
        <vendor :vendor="v">
          <template v-slot:default>
            <b-row id="vendor-orders-row" v-if="v.orders && v.orders.length" v-bind:key="v.id">
              <order v-for="order in v.orders" :key="order.id" :order="order"></order>
            </b-row>
            <p v-else>No orders placed yet with this vendor.</p>
          </template>
        </vendor>
      </b-col>
    </b-row>
    <b-row v-if="vendors.length === 0">
      <b-col cols="12">
        <p>Record and view all of the orders made for materials here. No materials ordered to date.</p>
      </b-col>
    </b-row>
  </div>
</template>
