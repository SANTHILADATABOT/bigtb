export default {
  data () {
    return {

    }
  },
  // methods: {
  //   getCalenderDatas() {
  //     try {
  //       var self = this;
  //           console.log('self.base_url=>',self.base_url);
  //           self.httpRequest({
  //             url: self.base_url + 'pm/v2/monthlycalender/index', // Replace with your API endpoint
  //             method: "GET",
  //             success: function (res) {
  //               console.log("Data fetched successfully:", JSON.parse(res));
  //               if (res !== undefined) {
  //                 return JSON.parse(res);
  //               }
  //             },
  //             error: function (err) {
  //               console.error("Error fetching data:", err);
  //             },
  //           });
  //     } catch (error) {
  //       console.error('Failed to fetch data:', error);
  //     }
  //   },
  // }
  methods: {
    getCalenderDatas() {
      return new Promise((resolve, reject) => {
        try {
          var self = this;
          console.log('self.base_url=>', self.base_url);
          self.httpRequest({
            url: self.base_url + 'pm/v2/monthlycalender/index', // Replace with your API endpoint
            method: "GET",
            success: function (res) {
              console.log("Data fetched successfully:", JSON.parse(res));
              if (res !== undefined) {
                resolve(JSON.parse(res));  // Resolve with the fetched data
              }
            },
            error: function (err) {
              console.error("Error fetching data:", err);
              reject(err);  // Reject the promise in case of an error
            },
          });
        } catch (error) {
          console.error('Failed to fetch data:', error);
          reject(error);  // Reject the promise in case of an exception
        }
      });
    },
    fetchEventDetails(eventId) {
      return new Promise((resolve, reject) => {
        axios
          .get(`${this.base_url}pm/v2/monthlycalender/show`, {
            params: { id: eventId } // Pass the event ID as a query parameter
          })
          .then((response) => {
            if (response.data.success) {
              resolve(response.data.event); // Adjust based on your API response structure
            } else {
              reject(new Error("Event not found"));
            }
          })
          .catch((error) => {
            reject(error);
          });
      });
    }
  },
}
