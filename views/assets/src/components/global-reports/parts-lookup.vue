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
      filters : {
        global: null
      },
      filteredRows: []
    }
  },
  computed: {
    rows() {
      const rows = JSON.parse(JSON.stringify(this.$store.state.reportRows));
      rows.forEach(row => {
        delete row.__ob__;
      });
      return rows ?? [];
    },
    totalValue() {
      const total = this.filteredRows.reduce((acc, row) => {
        return acc + (
            (row['quantity-on-hand'] ? parseFloat(row['quantity-on-hand']) : 0)
          * (row['average-cost'] ? parseFloat(row['average-cost']) : 0));
      }, 0);
      return total.toFixed(2);
    }
  },
  methods: {
    submitGetParts() {
      this.rows = [];
      this.loading = true;
      this.getReport('parts-on-hand-two', 0);
    },
    applyFilters() {
      this.filteredRows = this.rows.filter(row => {
        // Global filter - in this format, more filters can easily be added
        if (this.filters.global && this.filters.global !== '' && !Object.values(row).some(val => {
          if (val) {
            return val.toString().toLowerCase().includes(this.filters.global.toLowerCase())
          }
        })) {
          console.log("Filtered out");
          return false;
        }
        return true;
      });
    },
    exportCSV() {
      this.$refs.dt.exportCSV();
    },
  },
  watch: {
    rows() {
      if (this.loading && this.rows.length > 0) {
        this.filteredRows = this.rows;
        this.loading = false;
      }
    },
    filters: {
      handler() {
        this.applyFilters();
      },
      deep: true
    },
  },
  mounted() {
    this.clearRows();
    this.submitGetParts();
  },
  created() {
    this.filteredRows = this.rows;
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
      <filter-bar v-model="filters.global"/>
      <p>Total Value: ${{ totalValue }}</p>
      <DataTable ref="dt" :value="filteredRows" :scrollable="true" scrollHeight="800px" scrollDirection="both" responsiveLayout="scroll">
        <template #header>
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="m-0">Parts On Hand</h5>
            <span class="d-flex align-items-center">
              <Button @click="exportCSV($event)">Export CSV</Button>
            </span>
          </div>
        </template>
        <template #loading>
          Loading data. Please wait.
        </template>
        <template #empty>
          No parts found.
        </template>
        <Column field="part-number" header="Part #" sortable/>
        <Column field="description" header="Description" sortable/>
        <Column field="unit" header="Unit" sortable/>
        <Column field="quantity-on-hand" header="Quantity on Hand" sortable/>
        <Column field="average-cost" header="Average Cost" sortable/>
        <Column field="location" header="Location" sortable/>
        <Column field="manufacturer" header="Manufacturer" sortable/>
        <Column field="manufacturer-part-number" header="Manufacturer Part Number" sortable/>
        <Column field="markup" header="Markup" sortable/>
        <Column field="reorder-quantity" header="Reorder Quantity" sortable/>
        <Column field="minimum-order" header="Minimum Order" sortable/>
        <Column field="weight" header="Weight" sortable/>
        <Column field="oem-warranty-duration" header="OEM Warranty Duration" sortable/>
      </DataTable>
<!--      <report-table :rows="rows" :filter="filter" :label="selectedReport"-->
<!--                    :totals="selectedReport === 'budget'"></report-table>-->
    </div>
  </div>
</template>

<style scoped lang="less">

</style>
