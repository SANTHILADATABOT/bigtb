export default {
  data () {
    return {

    }
  },
  methods: {

    getMaterialOrders() {
      const self = this;
      const request_data = {
        type: 'GET',
        url: self.base_url + 'pm/v2/materials/orders',
        success: function (res) {
          if (res.data !== undefined) {
            self.$store.commit("setMaterialOrders", [...res.data]);
          }
        },
        error: function (res) {
          console.error('Failed to fetch material orders:', res);
        }
      };
      self.httpRequest(request_data);
    },

    getMaterialVendors() {
      const self = this;

      // Fetch all orders
      const ordersPromise = new Promise((resolve, reject) => {
        self.httpRequest({
          type: 'GET',
          url: self.base_url + 'pm/v2/materials/orders',
          success: function (res) {
            if (res.data !== undefined) {
              resolve(res.data);
            }
          },
          error: function (res) {
            console.error('Failed to fetch orders while fetching vendors:', res);
            reject(res);
          }
        });
      });

      // Fetch all vendors
      const vendorsPromise = new Promise((resolve, reject) => {
        self.httpRequest({
          type: 'GET',
          url: self.base_url + 'pm/v2/materials/vendors',
          success: function (res) {
            if (res.data !== undefined) {
              resolve(res.data);
            }
          },
          error: function (res) {
            console.error('Failed to fetch material vendors:', res);
            reject(res);
          }
        });
      });

      Promise.all([ordersPromise, vendorsPromise]).then(([orders, vendors]) => {
        // Assign orders to their respective vendors
        vendors.forEach(vendor => {
          vendor.orders = orders.filter(order => parseInt(order.vendor_id) === parseInt(vendor.id));
        });
        // Commit the vendors with their orders to the store
        self.$store.commit("setMaterialVendors", [...vendors]);
      }).catch(error => {
        console.error('Failed to fetch orders or vendors:', error);
      });
    },

    addMaterialOrder(dataObj) {
      // dataObj = {vendor_id: "vendor_id", material_id: "material_id", quantity: "quantity", cost: "cost", date: "date", ordered_by: "ordered_by"  }
      const self = this;
      const request_data = {
        type: 'POST',
        url: self.base_url + 'pm/v2/materials/orders',
        data: dataObj,
        success: function (res) {
          console.log("successfully added order: ", res.data);
          self.getMaterialVendors();
        },
        error: function (res) {
          console.error('Failed to add new order:', res);
        }
      };
      self.httpRequest(request_data);
    },

    deleteMaterialOrder(order_id) {
      const self = this;
      const request_data = {
        type: 'DELETE',
        url: self.base_url + 'pm/v2/materials/orders/' + order_id,
        success: function (res) {
          if (res.data !== undefined) {
            console.log("successfully deleted order: ", res.data);
            self.getMaterialVendors();
          }
        },
        error: function (res) {
          console.error('Failed to delete order:', res);
        }
      };
      self.httpRequest(request_data);

    },

    addMaterialVendor(dataObj) {
      // dataObj = {name: "vendor name", description: "description", email: "email", phone: "phone", address: "address"}
      const self = this;
      const request_data = {
        type: 'POST',
        url: self.base_url + 'pm/v2/materials/vendors',
        data: dataObj,
        success: function (res) {
          if (res.data !== undefined) {
            self.getMaterialVendors();
          }
        },
        error: function (res) {
          console.error('Failed to add new vendor:', res);
        }
      };
      self.httpRequest(request_data);
    },

    deleteMaterialVendor(vendor_id) {
      const self = this;
      const request_data = {
        type: 'DELETE',
        url: self.base_url + 'pm/v2/materials/vendors/' + vendor_id,
        success: function (res) {
          console.log("successfully deleted vendor: ", res.data);
          self.getMaterialVendors();
        },
        error: function (res) {
          console.error('Failed to delete vendor:', res);
        }
      };
      self.httpRequest(request_data);

    },

    getWPUsers () {
      const self = this;

      self.httpRequest({
        url: self.base_url + 'pm/v2/users/',
        success (res) {
          if (res.data !== undefined) {
            self.$store.commit('setUsers', res.data);
          }
        }
      })
    },

    getCurrentUser() {
      const self = this;
      self.httpRequest({
        url: self.base_url + 'pm/v2/current-user',
        success (res) {
          if (res.data !== undefined) {
            self.$store.commit('setCurrentUser', res.data);
          }
        }
      })
    }
  }
}
