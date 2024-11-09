<script>
import reportMixin from '@components/global-reports/mixin';
import ReportsNav from "@components/global-reports/reports-nav";
import ReportTable from "@components/global-reports/report-table";

export default {
  components: {ReportTable, ReportsNav},
  mixins: [reportMixin],
  data() {
    return {
      selectedReport: null,
      selectedRecord: "",
      loading: false,
    }
  },
  computed: {
    availableReports() {
      return this.$store.state.reports;
    },
    records() {
      return this.$store.state.records;
    },
    rows() {
      const rows = JSON.parse(JSON.stringify(this.$store.state.reportRows));
      rows.forEach(row => {
        delete row.__ob__;
      });
      return rows;
    }
  },
  methods: {
    submitGetRecords() {
      this.getRecords(this.selectedReport);
    },
    submitGetReport() {
      this.rows = [];
      this.loading = true;
      this.getReport(this.selectedReport, this.selectedRecord);
    },
    clearTable() {
      this.rows = [];
    }
  },
  watch: {
    rows() {
      if (this.loading && this.rows.length > 0) {
        this.loading = false;
      }
    }
  },
  created() {
    this.clearRows();
    this.getAvailableReports();
  }
}
</script>

<template>
  <div>
    <div class="mb-3">
      <select id="report" class="form-select-override" @change="clearTable" v-model="selectedReport">
        <option v-for="report in availableReports" :key="report.value" :value="report.value">{{ report.text }}</option>
      </select>
      <b-btn @click="submitGetRecords">Get Records</b-btn>
    </div>
    <div class="mb-3">
      <select id="record" class="form-select-override" @change="clearTable" v-model="selectedRecord">
        <option value="">None Selected</option>
        <option v-for="record in records" :key="record.value" :value="record.value">{{ record.text }}</option>
      </select>
      <b-btn @click="submitGetReport">Get Report</b-btn>
    </div>
    <output v-if="loading" class="spinner-border">
      <span class="visually-hidden"> </span>
    </output>
    <report-table v-if="rows.length > 0 && !loading" :rows="rows" :label="selectedReport" :totals="selectedReport === 'budget'"></report-table>
  </div>
</template>

<style scoped>
</style>
