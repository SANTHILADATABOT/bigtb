<template>
    <h1>Test 31231..!</h1>
    <ejs-schedule height="550px" currentView="Month" :eventSettings="appointmentData">
    </ejs-schedule>
  </template>
  
  <script>
  import { ScheduleComponent, Day, Week, WorkWeek, Month, Agenda } from "@syncfusion/ej2-vue-schedule";
  // import { DataManager, WebApiAdaptor } from "@syncfusion/ej2-data";
  
  // var remoteData = new DataManager({
  //   url: 'https://ej2services.syncfusion.com/production/web-services/api/Schedule',
  //   adaptor: new WebApiAdaptor,
  //   crossDomain: true
  // });
  
  export default {
    name: 'SyncFusion',
    components: {
      'ejs-schedule': ScheduleComponent
    },
    provide: {
      schedule: [Day, Week, WorkWeek, Month, Agenda]
    },
    data() {
      return {
        // schedulerSelectedDate : new Date(2022, 4, 12),
        appointmentData: {
          // dataSource : remoteData
          // dataSource : [
          //     {
          //      Subject : 'Project LMS',
          //      StartTime: new Date(2024, 24, 10, 9, 0),
          //      EndTime: new Date(2024, 4, 11, 10, 0)
          //    },
          //    {
          //      Subject : 'Review Meeting',
          //      StartTime: new Date(2024, 11, 2, 10, 0),
          //      EndTime: new Date(2024, 11, 2, 11, 30),
          //      IsReadonly : true
          //    },
          //    {
          //      Subject : 'Project Sanjeevinimart',
          //      StartTime: new Date(2024, 11, 1, 11, 30),
          //      EndTime: new Date(2024, 11, 3, 12, 30)
          //    }
          dataSource:
            [
              { "Id": 1, "Subject": "Offshore Project", "StartTime": "2024-11-01T00:00:00Z", "EndTime": "2024-11-11T00:00:00Z", "IsAllDay": true, "IsBlock": false, "IsReadonly": false, "RoomId": 1, "ResourceId": 1 },
              { "Id": 2, "Subject": "LMS Project", "StartTime": "2024-10-21T00:00:00Z", "EndTime": "2024-11-10T00:00:00Z", "IsAllDay": true, "IsBlock": false, "IsReadonly": false, "RoomId": 1, "ResourceId": 2 }
            ]
        }
      };
    },
  }
  </script>
  
  
  <!-- <style>
  @import '@syncfusion/ej2-base/styles/material.css';
  @import '@syncfusion/ej2-buttons/styles/material.css';
  @import '@syncfusion/ej2-calendars/styles/material.css';
  @import '@syncfusion/ej2-dropdowns/styles/material.css';
  @import '@syncfusion/ej2-inputs/styles/material.css';
  @import '@syncfusion/ej2-navigations/styles/material.css';
  @import '@syncfusion/ej2-popups/styles/material.css';
  @import '@syncfusion/ej2-vue-schedule/styles/material.css';

  </style> -->
  