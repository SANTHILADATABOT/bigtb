<script>
import pmMenu from "@components/common/menu.vue";
import pmHeader from "@components/common/header.vue";
import orders from "@components/project/project-materials/project-orders.vue";
import addNew from "@components/global-materials/add-new.vue";
import MaterialsMixin from "@components/global-materials/mixin.js";

export default {
  components: { pmMenu, pmHeader, orders, addNew },
  computed: {
    project() { return this.$store.state.project }
  },
  mixins: [MaterialsMixin],
  created() {
    this.getMaterialOrders();
    this.getMaterialVendors();
    this.getWPUsers();
    this.getProjects();
  }
}
</script>

<template>
  <div class="pm-wrap pm pm-front-end">
    <pm-header></pm-header>
    <pm-menu></pm-menu>
    <div class="pm-materials-container">
      <div class="pm-content">
        <orders class="orders" :project="project"></orders>
        <add-new class="add-new"></add-new>
      </div>
      <p>These orders are specific to this project. for all orders go
        <router-link to="/materials">here.</router-link>
      </p>
    </div>
  </div>
</template>

<style lang="less" scoped>
  .pm-materials-container {
    display: flex;
    flex-direction: column;
    margin-top: 0;
    padding: 20px;
    border: 1px solid #E5E4E4;
    background: #FAFAFA;
  }

  .pm-content {
    display: flex;
    flex-direction: row;
  }

  .orders {
    flex: 0.7;
  }

  .add-new {
    flex: 0.3;
  }

  .materials-input {
    width: 100%;
    padding: 10px;
    box-sizing: border-box;
  }
</style>
