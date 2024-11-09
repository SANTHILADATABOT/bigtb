<script>
import "@assets/css/variables.css";
import MaterialsMixin from "@components/global-materials/mixin.js";

export default {
  name: "vendor.vue",
  mixins: [MaterialsMixin],
  props: {
    vendor: {
      type: Object,
      required: true,
      validator: function (vendor) {
        return vendor.hasOwnProperty('name') &&
                vendor.hasOwnProperty('id') &&
                vendor.hasOwnProperty('phone') &&
                vendor.hasOwnProperty('email') &&
                vendor.hasOwnProperty('address');
      }
    }
  },
  methods: {
    deleteVendor() {
      this.deleteMaterialVendor(this.vendor.id);
      this.isCardVisible = false;
      this.$bvModal.hide(`vendor-info-${this.vendor.id}`);
    }
  },
  data() {
    return {
      isCardVisible: false,
    }
  }
}
</script>

<template>
  <b-card border-variant="primary" :header="`Vendor: ${vendor.name}`" header-bg-variant="primary" header-text-variant="white" style="max-width: none !important; padding-left: 1.25rem; padding-right: 1.25rem">
    <b-button size="lg" variant="secondary" class="mb-2" @click="$bvModal.show(`vendor-info-${vendor.id}`)"><i class="fa fa-info-circle text-white"></i> Vendor Info</b-button>
    <b-modal :id="`vendor-info-${vendor.id}`" centered :title="`${vendor.name} Details`" hide-footer>
      <b class="mv-name">Vendor Info for {{ vendor.name }}</b>
      <p class="mv-detail">{{ vendor.description }}</p>
      <div>
        <b class="mv-text">Phone: </b>
        <p class="mv-text">{{ vendor.phone }}</p>
      </div>
      <div>
        <b class="mv-text">Email: </b>
        <p class="mv-text">{{ vendor.email }}</p>
        </div>
      <div>
        <b class="mv-text">Address: </b>
        <p class="mv-text">{{ vendor.address }}</p>
      </div>
      <b-button @click="deleteVendor" variant="danger">Delete</b-button>
    </b-modal>
    <h5>Orders</h5>
    <slot></slot>
  </b-card>
</template>

<style scoped lang="less">
  .mv-name {
    display: block;
    color: black;
    font-weight: bold;
    font-size: 1.3rem;
  }
  .mv-detail {
    display: block;
    margin: 10px 0;
  }
  .mv-line {
    display: block;
    margin-bottom: 1em;
  }
  .mv-text {
    display: inline-block;
  }
</style>
