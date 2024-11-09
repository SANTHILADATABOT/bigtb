<script>
import NewVendor from "@components/global-materials/new-vendor.vue";
import NewOrder from "@components/global-materials/new-order.vue";
import MaterialsMixin from "@components/global-materials/mixin.js";

export default {
  name: "add-new",
  mixins: [MaterialsMixin],
  components: {NewVendor, NewOrder},
}
</script>

<template>
  <div id="add-new" class="d-flex justify-content-end">
    <new-vendor></new-vendor>
    <new-order></new-order>
  </div>
</template>

<style scoped lang="less">

</style>
