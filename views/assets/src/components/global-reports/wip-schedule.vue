<script>
import ReportTable from "@components/global-reports/report-table.vue";
import FilterBar from "@components/global-reports/filter-bar.vue";
import reportMixin from '@components/global-reports/mixin';

export default {
  components: {ReportTable, FilterBar},
  mixins: [reportMixin],
  data() {
    const now = new Date();
    return {
      dateFieldOpts: {
        shortcuts: [{
          text: 'This month',
          onClick(picker) {
            picker.$emit('pick', [new Date(), new Date()]);
          }
        }, {
          text: 'This year',
          onClick(picker) {
            const end = new Date();
            const start = new Date(new Date().getFullYear(), 0);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: 'Last year',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setFullYear(start.getFullYear() - 1);
            picker.$emit('pick', [start, end]);
          }
        }]
      },
      loading: false,
      filteredRows: [],
      filters: {
        global: null,
        record_number: [0, 100000],
        salesperson: [],
        estimator: [],
        status: [],
        dates: [now]
      },
      statuses: [
        {id: 1, name: '1'},
        {id: 2, name: '2'},
        {id: 3, name: '3'},
        {id: 4, name: '4'},
      ]
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
    employees() {
      return this.$store.state.employees;
    },
    maxRecordNumber() {
      return this.rows.reduce((max, row) => Math.max(max, row.record_number), 0);
    },
  },
  methods: {
    submitGetWIPReport() {
      this.rows = [];
      this.loading = true;
      this.getReport('wip-schedule', 0);
    },
    exportCSV() {
      this.$refs.dt.exportCSV();
    },
    applyFilters() {
      this.filteredRows = this.rows.filter(row => {
        // Global filter
        if (this.filters.global && this.filters.global !== '' && !Object.values(row).some(val => {
          if (val) {
            return val.toString().toLowerCase().includes(this.filters.global.toLowerCase())
          }
        })) {
          return false;
        }
        // Record number filter
        if (this.filters.record_number.length === 2) {
          const [min, max] = this.filters.record_number;
          if (row.record_number < min || row.record_number > max) {
            return false;
          }
        }
        console.log("personnel: ", this.filters.salesperson, this.filters.estimator);
        console.log("row: ", row.salesperson, row.estimator);
        // Salesperson filter
        if (this.filters.salesperson.length > 0 && !this.filters.salesperson.includes(row.salesperson)) {
          return false;
        }
        // Estimator filter
        if (this.filters.estimator.length > 0 && !this.filters.estimator.includes(row.estimator)) {
          return false;
        }
        // Status filter
        console.log("status: ", this.filters.status, row.status);
        if (this.filters.status.length > 0 && !this.filters.status.includes(parseInt(row.status))) {
          return false;
        }
        console.log("made it past status filter");
        // Dates filter
        console.log("filter dates: ", this.filters.dates);
        console.log("row date: ", row.created_at);
        if (this.filters.dates.length === 2 && row.created_at) {
          const [startDate, endDate] = this.filters.dates;
          const rowDate = new Date(row.created_at);
          if (rowDate < startDate || rowDate > endDate) {
            return false;
          }
        }

        console.log("made it past the date filter");

        return true;
      });
    },
  },
  watch: {
    rows() {
      if (this.loading && this.rows.length > 0) {
        this.loading = false;
      }
    },
    filters: {
      handler() {
        this.applyFilters();
      },
      deep: true
    },
    maxRecordNumber() {
      console.log("Max record number: ", this.maxRecordNumber);
      this.filters.record_number = [0, this.maxRecordNumber];
    }
  },
  mounted() {
    this.getEmployees()
    this.submitGetWIPReport();
    this.clearRows();
  },
}
</script>

<template>
  <div>
    <div v-if="loading" class="d-flex justify-content-center align-items-center mt-5">
      <output v-if="loading" class="spinner-border">
        <span class="visually-hidden"> </span>
      </output>
    </div>
    <div v-if="rows.length > 0 && !loading" >
      <div class="d-flex align-items-center row">
        <div class="col-8">
          <div class="row py-2">
            <div class="col">
              <el-input class="global-filter"
                        placeholder="Search Any Column"
                        v-model="filters.global"
                        clearable>
              </el-input>
            </div>
          </div>
          <div class="row py-2">
            <div class="col">
              <el-select v-model="filters.salesperson" multiple placeholder="Select Salesperson" class="filter">
                <el-option
                    v-for="e in employees"
                    :key="e.id"
                    :label="e.name"
                    :value="e.name">
                </el-option>
              </el-select>
              <el-select v-model="filters.estimator" multiple placeholder="Select Estimator" class="filter">
                <el-option
                    v-for="e in employees"
                    :key="e.id"
                    :label="e.name"
                    :value="e.name">
                </el-option>
              </el-select>
              <el-select v-model="filters.status" multiple placeholder="Select Status" class="filter">
                <el-option
                    v-for="s in statuses"
                    :key="s.id"
                    :label="s.name"
                    :value="s.id">
                </el-option>
              </el-select>
              <el-date-picker class="filter"
                              v-model="filters.dates"
                              type="monthrange"
                              align="right"
                              unlink-panels
                              range-separator="To"
                              start-placeholder="Start month"
                              end-placeholder="End month"
                              :picker-options="this.dateFieldOpts">
              </el-date-picker>
            </div>
          </div>
        </div>
        <div class="col-3 pr-5">
          <div class="row py-2">
            <div class="col">
              <label for="record-number-filter">Record # Min:</label>
            </div>
            <div class="col">
              <el-input-number id="record-number-filter"
                               v-model="filters.record_number[0]"
                               :min="0"
                               :max="maxRecordNumber"
                               :step="100"
                               controls-position="right"
                               class="filter"/>
            </div>
          </div>
          <div class="row py-2">
            <div class="col">
              <label for="record-number-filter-2">Record # Max: </label>
            </div>
            <div class="col">
              <el-input-number id="record-number-filter-2"
                               v-model="filters.record_number[1]"
                               :min="100"
                               :max="maxRecordNumber"
                               :step="100"
                               controls-position="right"
                               class="filter"/>
            </div>
          </div>
        </div>
      </div>
      <DataTable ref="dt" :value="filteredRows" :scrollable="true" scrollHeight="800px" scrollDirection="both" responsiveLayout="scroll">
        <template #header>
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="m-0">WIP Schedule</h5>
            <span class="d-flex align-items-center">
              <Button @click="exportCSV($event)">Export CSV</Button>
            </span>
          </div>
        </template>
        <template #loading>
          Loading data. Please wait.
        </template>
        <template #empty>
          No customers found.
        </template>
        <Column field="record_number" header="Record #" sortable/>
        <Column field="salesperson" header="Salesperson" sortable/>
        <Column field="estimator" header="Estimator" sortable/>
        <Column field="created_at" header="Date Started" sortable :styles="{'min-width':'200px'}"/>
        <Column field="total_contract" header="Total Contract" sortable/>
        <Column field="total_budget" header="Total Budget" sortable/>
        <Column field="estimated_gross_profit" header="Estimated Gross Profit" sortable/>
        <Column field="contract_revenue_earned" header="Contract Revenue Earned" sortable/>
        <Column field="direct_job_cost" header="Direct Job Cost" sortable/>
        <Column field="gross_profit" header="Gross Profit" sortable/>
        <Column field="total_billed_to_date" header="Total Billed to Date" sortable/>
        <Column field="percent_budget_used" header="Percent Budget Used" sortable/>
        <Column field="costs_and_earnings_in_excess_of_billings" header="Costs and Earnings in Excess of Billings" sortable/>
        <Column field="billings_in_excess_of_costs_and_earnings" header="Billings in Excess of Costs and Earnings" sortable/>
        <Column field="prior_year_costs" header="Prior Year Costs" sortable/>
        <Column field="current_year_earned_revenue" header="Current Year Earned Revenue" sortable/>
        <Column field="current_year_costs" header="Current Year Costs" sortable/>
        <Column field="current_year_gross_profit" header="Current Year Gross Profit" sortable/>
      </DataTable>
<!--      <report-table :rows="rows" :filter="filter" :label="selectedReport" :totals="selectedReport === 'budget'"></report-table>-->
    </div>
  </div>
</template>

<style scoped>
.global-filter {
  width: 20rem !important;
  margin-left: .5rem;
}
.filter {
  margin-left: .5rem;
}
</style>
