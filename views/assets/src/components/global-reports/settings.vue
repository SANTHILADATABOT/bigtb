<script>
import reportMixin from '@components/global-reports/mixin';

export default {
  mixins: [reportMixin],
  data() {
    return {
      selectedReport: {},
      loading: true,
      columns: [],
      datatypes: [
        { text: 'Text', value: 'string' },
        { text: '$', value: 'cash' },
        { text: '%', value: 'percentage' },
        { text: 'Date', value: 'date' },
      ],
    }
  },
  computed: {
    reports() {
      return this.$store.state.reports;
    },
    records() {
      return this.$store.state.records;
    },
    rows() {
      return this.$store.state.reportRows;
    },
  },
  methods: {
    getCols() {
      const wholeReport = this.reports.find(r => r.value === this.selectedReport);
      if (wholeReport.colSettings.length > 0) {
        this.columns = wholeReport.colSettings;
      } else {
        this.getRecords(this.selectedReport);
      }
    },
    convertString(str) {
      console.log("string: ", str);
      return this.convertToNormalCase(str);
    }
  },
  watch: {
    reports() {
      if (this.reports.length > 0) {
        this.loading = false;
      }
    },
    selectedReport() {
      this.getRecords(this.selectedReport)
    },
    records() {
      if (this.records.length > 0) {
        this.getReport(this.selectedReport, this.records[0].value)
      }
    },
    rows() {
      if (this.rows.length > 0) {
        const columns = [];
        for (const i in Object.keys(this.rows[0])) {
          columns.push({
            name: Object.keys(this.rows[0])[i],
            datatype: 'string',
            order: i,
            report_id: this.selectedReport.id
          });
        }
        this.columns = columns;
      }
    }
  },
  created() {
    this.getAvailableReports()
  },
}
</script>

<template>
  <div>
    <output v-if="loading" class="spinner-border">
      <span class="visually-hidden"> </span>
    </output>
    <select v-if="!loading" id="report" class="form-select-override" @change="getCols" v-model="selectedReport">
      <option v-for="report in reports" :key="report.value" :value="report.value">{{ report.text }}</option>
    </select>
    <div class="row mt-3" v-if="columns.length > 0">
      <div class="col-2 mb-3" v-for="col in columns">
        <div class="card border-secondary rounded">
          <div class="card-header bg-primary text-white" style="width: 200px;">{{convertString(col.name)}}</div>
          <div class="card-body">
            <select class="form-select">
              <option v-for="dt in datatypes" :key="`${col.name}${dt.value}`" :value="dt.value">{{dt.text}}</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="less">

</style>
