<script>
import ReportsNav from '@components/global-reports/reports-nav.vue';
import ReportTable from "@components/global-reports/report-table.vue";
import FilterBar from "@components/global-reports/filter-bar.vue";
import reportMixin from '@components/global-reports/mixin';

export default {
  components: {ReportTable, ReportsNav, FilterBar},
  mixins: [reportMixin],
  data() {
    return {
      loading: false,
      filter: ''
    }
  },
  computed: {
    records() {
      return this.$store.state.records;
    },
    rows() {
      const rows = JSON.parse(JSON.stringify(this.$store.state.reportRows));
      rows.forEach(row => {
        delete row.__ob__;
      });
      return rows ?? [];
    },
    totalValue() {
      const total = this.rows.reduce((acc, row) => acc + (
          parseFloat(row['quantity-on-hand']) * parseFloat(row['average-cost'])
      ), 0);
      return total.toFixed(2);
    }
  },
  methods: {
    submitGetParts() {
      this.rows = [];
      this.loading = true;
      this.getReport('parts-on-hand', 0);
    }
  },
  watch: {
    rows() {
      if (this.loading && this.rows.length > 0) {
        this.loading = false;
      }
    }
  },
  mounted() {
    this.submitGetParts();
    this.clearRows();
  }
}
</script>

<template>
  <div>
    <div v-if="loading" class="d-flex justify-content-center align-items-center mt-5">
      <output v-if="loading" class="spinner-border">
        <span class="visually-hidden"></span>
      </output>
    </div>
    <div v-if="rows.length > 0 && !loading">
      <filter-bar v-model="filter"/>
      <p>Total Value: ${{ totalValue }}</p>
      <report-table :rows="rows" :filter="filter" :label="selectedReport"
                    :totals="selectedReport === 'budget'"></report-table>
    </div>
  </div>
</template>

<style scoped lang="less">

</style>
