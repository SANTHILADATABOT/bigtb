<script>
import ProfitMixin from "@components/global-profitability/mixin";
import MaterialsMixin from "@components/global-materials/mixin";
import Invoices from "@components/global-profitability/invoices";
import MaterialCosts from "@components/global-profitability/material-costs.vue";
import BottomLine from "@components/global-profitability/bottom-line.vue";
export default {
  name: "profitability",
  components: { MaterialCosts, Invoices, BottomLine },
  mixins: [ProfitMixin, MaterialsMixin],
  computed: {
    invoices() {
      return this.$store.state.invoices;
    },
    vendorsWithCosts() {
      return this.$store.state.materialVendors;
    }
  },
  created() {
    this.getInvoices();
    this.getMaterialVendors()
  },
}
</script>

<template>
  <div>
    <h2>Overall Profitability</h2>
    <invoices :invoices="invoices"></invoices>
    <material-costs :vendors="vendorsWithCosts"></material-costs>
    <bottom-line :invoices="invoices" :vendors="vendorsWithCosts"></bottom-line>
  </div>
</template>

<style scoped lang="less">
.p-table {
  width: 62%;
  border-collapse: collapse;
  th, td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
  }
  th {
    background-color: #f2f2f2;
  }
}
</style>
