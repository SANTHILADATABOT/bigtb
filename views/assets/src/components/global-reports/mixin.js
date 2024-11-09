export default {
  data () {
    return {

    }
  },
  methods: {
    getAvailableReports() {
        const self = this;
        const request_data = {
          type: 'GET',
          url: self.base_url + 'pm/v2/db-reports',
          success: function (res) {
            if (res.data !== undefined) {
              let reports = res.data.map(rep => {
                return {
                  id: rep.id,
                  value: rep.report_name,
                  text: rep.title,
                  colSettings: rep.col_settings,
                }
              });
              self.$store.commit("setReports", reports);
            }
          },
          error: function (res) {
            console.error('Failed to fetch available reports:', res);
          }
        };
        self.httpRequest(request_data);
      },
    getRecords(reportName) {
      const self = this;
      const request_data = {
        type: 'GET',
        url: self.base_url + `pm/v2/db-reports/records?report_id=${reportName}`,
        success: function (res) {
          if (res.data !== undefined) {
            let records = res.data.map(rec => {
                return {
                    value: rec.record,
                    text: rec.record
                }
            });
            self.$store.commit("setRecords", records);
          }
        },
        error: function (res) {
          console.error('Failed to fetch available records:', res);
        }
      };
      self.httpRequest(request_data);
    },
      getEmployees() {
          const self = this;
          const request_data = {
              type: 'GET',
              url: self.base_url + `pm/v2/db-reports/employees`,
              success: function (res) {
                  if (res.data !== undefined) {
                      self.$store.commit("setEmployees", res.data);
                  }
              },
              error: function (res) {
                  console.error('Failed to fetch employees:', res);
              }
          };
          self.httpRequest(request_data);
      },
    getReport(reportName, recordNumber) {
      const self = this;
      const request_data = {
        type: 'GET',
        url: self.base_url + `pm/v2/db-reports/${reportName}?record=${recordNumber}`,
        success: function (res) {
          if (res.data !== undefined) {
              console.log("Report rows:", res.data);
            self.$store.commit("setReportRows", res.data);
          }
        },
        error: function (res) {
          console.error('Failed to fetch report:', res);
        }
      };
      self.httpRequest(request_data);
    },
    clearRows() {
      this.$store.commit("setReportRows", []);
    },
    convertToNormalCase(str) {
        return str.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
  }
}
