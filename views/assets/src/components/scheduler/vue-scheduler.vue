<template>
    <div>
      <h2>Vue Scheduler Example</h2>
      <!-- Use the ScheduleComponent -->
      <ScheduleComponent :eventSettings="eventSettings" :startHour="startHour" :endHour="endHour" :modelchanged="true">
      </ScheduleComponent>
    </div>
  </template>
  <script>
import { ScheduleComponent } from '@syncfusion/ej2-vue-schedule';
import { registerLicense } from '@syncfusion/ej2-base';
import '@node/@syncfusion/ej2-base/styles/material.css';
import '@node/@syncfusion/ej2-schedule/styles/material.css';
import '@node/@syncfusion/ej2-buttons/styles/material.css';
import '@node/@syncfusion/ej2-calendars/styles/material.css';
import '@node/@syncfusion/ej2-dropdowns/styles/material.css';
import '@node/@syncfusion/ej2-inputs/styles/material.css';
import '@node/@syncfusion/ej2-navigations/styles/material.css';
import '@node/@syncfusion/ej2-popups/styles/material.css';

registerLicense('ORg4AjUWIQA/Gnt2UlhhQlVMfV5AQmFWfFN0QXNYdV92fldAcC0sT3RfQFliSH5RdkRiXn1feXRWQg==');

  export default {
    name: 'Vue Scheduler App',
    data() {
      return {
        // Define scheduler events and configurations
        eventSettings: {
          dataSource: [
            {
              Id: 1,
              Subject: 'Meeting',
              StartTime: new Date(2024, 10, 15, 10, 0),
              EndTime: new Date(2024, 10, 15, 12, 0),
            },
            {
              Id: 2,
              Subject: 'Conference',
              StartTime: new Date(2024, 10, 16, 14, 0),
              EndTime: new Date(2024, 10, 16, 16, 0),
            },
          ],
        },
        startHour: '06:00',
        endHour: '20:00',
      };
    },
  };
  </script>
  
