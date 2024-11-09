<script>
import Orders from "@components/global-materials/orders.vue";
import AddNew from "@components/global-materials/add-new.vue";
import MaterialsMixin from "@components/global-materials/mixin.js";

export default {
  components: { Orders, AddNew },
  mixins: [MaterialsMixin],
  computed: {
    project() {
      return this.$store.state.project;
    }
  },
  watch: {
    project() {
      console.log("Project changed (global-materials.vue): ", this.project);
    }
  },
  created() {
    this.getMaterialVendors();
    this.getWPUsers();
    this.getProjects();
  }
}
</script>

<template>
  <b-container fluid>
    <b-row>
      <b-col cols="8" class="justify-content-start align-items-center">
        <h1>Material Orders</h1>
        <p>Keep track of your material ordering by vendor, or make new orders.</p>
      </b-col>
      <b-col cols="4" class="d-flex justify-content-end align-items-center">
        <add-new></add-new>
      </b-col>
    </b-row>
    <b-row class="mt-4">
      <b-col cols="12">
        <orders></orders>
      </b-col>
    </b-row>
  </b-container>
</template>
