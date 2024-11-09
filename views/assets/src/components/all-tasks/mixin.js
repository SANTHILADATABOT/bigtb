export default {
  data() {
    return {
      // define your data properties here
    };
  },
  computed: {
    // define your computed properties here
  },
  methods: {
    getAllTasks() {
      var self = this;

      var request = {
        type: 'GET',
        url: self.base_url + 'pm/v2/tasks',
        data: { orderby: 'created_at', order: 'desc'  },
        success(res) {
          self.$store.commit('setAllTasks', res.data);
        },
        error(err) {
          console.error(err);
        }
      };

      self.httpRequest(request);
    }
  }
};
