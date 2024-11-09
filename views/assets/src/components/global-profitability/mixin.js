export default {
  data () {
    return {

    }
  },
  methods: {
    getInvoices() {
      const self = this;
      const request_data = {
        type: 'GET',
        url: self.base_url + 'pm/v2/all-invoices',
        success: function (res) {
          if (res.data !== undefined) {
            self.$store.commit("setInvoices", [...res.data]);
          }
        },
        error: function (res) {
          console.error('Failed to fetch invoices:', res);
        }
      };
      self.httpRequest(request_data);
    }
  }
}
