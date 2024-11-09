


<template>
    <div class="schedule-vue-sample">
        <div class="col-md-12 control-section">
            <div class="content-wrapper">
                <ejs-schedule id='Schedule' ref="ScheduleObj" height="650px" :selectedDate='selectedDate' :eventSettings='eventSettings' :eventRendered="oneventRendered"></ejs-schedule>
            </div>
        </div>

    </div>
</template>
<script>
    import { extend } from '@syncfusion/ej2-base';
    import { zooEventsData } from './datasource';
    import { ScheduleComponent, Day, Week, WorkWeek, Month, Agenda, Resize, DragAndDrop } from "@syncfusion/ej2-vue-schedule";
    
    export default {
        components: {
          'ejs-schedule': ScheduleComponent
        },
        data: function () {
            return {
                eventSettings: { dataSource: extend([], zooEventsData, null, true) },
                selectedDate: new Date(2021, 1, 15),
            }
        },
        provide: {
            schedule: [Day, Week, WorkWeek, Month, Agenda, Resize, DragAndDrop]
        },
        methods: {
            oneventRendered: function (args) {
                let scheduleObj = this.$refs.ScheduleObj;
                let categoryColor = args.data.CategoryColor;
                if (!args.element || !categoryColor) {
                    return;
                }
                if (scheduleObj.ej2Instances.currentView === 'Agenda') {
                    (args.element.firstChild).style.borderLeftColor = categoryColor;
                } else {
                    args.element.style.backgroundColor = categoryColor;
                }

            }
        }
    }

</script>