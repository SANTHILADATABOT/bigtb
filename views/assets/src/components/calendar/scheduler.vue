<template>
  <div class="wrap nosubsub">
    <h1 class="wp-heading-inline" style = "color: blueviolet;">{{ __('Categories', 'wedevs-project-manager') }}</h1>
    <hr class="wp-header-end">
    <div id="col-left">
      <div class="col-wrap test-class111">
        <ejs-schedule :height="'550px'" :width="'550px'" :selectedDate="userSelectedDate"
          :eventSettings="eventSettings">
        </ejs-schedule>
      </div>
    </div>
    <h1 class="test-class-123" style="color: tomato;">****** Test ******</h1>
  </div>
</template>

<script>
import { ScheduleComponent, Day, Week, WorkWeek, Month, Agenda } from "@syncfusion/ej2-vue-schedule";
import { scheduleData } from './datasource';
// import "@assets/css/syncfusion/syncfusion.css";

console.log("scheduleData => ", scheduleData);
export default {
  name: 'scheduler',
  components: {
    'ejs-schedule': ScheduleComponent,
  },
  provide: {
    schedule: [Day, Week, WorkWeek, Month, Agenda]
  },

  data() {
    return {
      userSelectedDate: new Date(),
      eventSettings: {
        // dataSource: extend([], scheduleData, null, true),
        dataSource:
          [
            { "Id": 1, "Subject": "Offshore Project", "StartTime": "2024-11-01T00:00:00Z", "EndTime": "2024-11-11T00:00:00Z", "IsAllDay": true, "IsBlock": false, "IsReadonly": false, "RoomId": 1, "ResourceId": 1 },
            { "Id": 2, "Subject": "LMS Project", "StartTime": "2024-10-21T00:00:00Z", "EndTime": "2024-11-10T00:00:00Z", "IsAllDay": true, "IsBlock": false, "IsReadonly": false, "RoomId": 1, "ResourceId": 2 }
          ],
        selectedDate: new Date(2024, 11, 6),
      }
    };
  },
  mounted: function () {
    pm.NProgress.done();
  },
  methods: {
    getImage() {
      return (data) => {
        return 'source/schedule/images/' + this.getEmployeeName(data).toLowerCase() + '.png';
      };
    },
    // oneventRendered: function (args) {
    //             let scheduleObj = this.$refs.ScheduleObj;
    //             let categoryColor = args.data.CategoryColor;
    //             if (!args.element || !categoryColor) {
    //                 return;
    //             }
    //             if (scheduleObj.ej2Instances.currentView === 'Agenda') {
    //                 (args.element.firstChild).style.borderLeftColor = categoryColor;
    //             } else {
    //                 args.element.style.backgroundColor = categoryColor;
    //             }

    //         }
  }
};
</script>

<style type="text/css">

/* @import '@syncfusion/ej2-base/styles/material.css'; */
/* @import '@syncfusion/ej2-buttons/styles/material.css';
@import '@syncfusion/ej2-calendars/styles/material.css';
@import '@syncfusion/ej2-dropdowns/styles/material.css';
@import '@syncfusion/ej2-inputs/styles/material.css';
@import '@syncfusion/ej2-navigations/styles/material.css';
@import '@syncfusion/ej2-popups/styles/material.css';
 */
/* @import '@syncfusion/ej2-vue-schedule/styles/material.css'; */
</style>
