<style>
.e-subject {
    color: red;
}

@import '../../../../../node_modules/@syncfusion/ej2-base/styles/material.css';
@import '../../../../../node_modules/@syncfusion/ej2-buttons/styles/material.css';
@import '../../../../../node_modules/@syncfusion/ej2-calendars/styles/material.css';
@import '../../../../../node_modules/@syncfusion/ej2-dropdowns/styles/material.css';
@import '../../../../../node_modules/@syncfusion/ej2-inputs/styles/material.css';
@import '../../../../../node_modules/@syncfusion/ej2-navigations/styles/material.css';
@import '../../../../../node_modules/@syncfusion/ej2-popups/styles/material.css';
@import '../../../../../node_modules/@syncfusion/ej2-schedule/styles/material.css';
</style> 

<template>
    <div  id="app">
        <ejs-schedule id='Schedule' height="550px" :eventSettings="appointmentData" enablePersistence="true"></ejs-schedule>

        <h1 class="e-subject">Test scheduler</h1>
    </div>
</template>

<script>
import { ScheduleComponent, Day, Week, WorkWeek, Month, Agenda } from "@node/@syncfusion/ej2-vue-schedule";
import UpgraderOverlay from '@components/upgrade/overlay';
import { registerLicense } from "@node/@syncfusion/ej2-base";
import '@node/@syncfusion/ej2-base/styles/material.css';
import '@node/@syncfusion/ej2-schedule/styles/material.css';
import '@node/@syncfusion/ej2-buttons/styles/material.css';
import '@node/@syncfusion/ej2-calendars/styles/material.css';
import '@node/@syncfusion/ej2-dropdowns/styles/material.css';
import '@node/@syncfusion/ej2-inputs/styles/material.css';
import '@node/@syncfusion/ej2-navigations/styles/material.css';
import '@node/@syncfusion/ej2-popups/styles/material.css';

// registerLicense("ORg4AjUWIQA/Gnt2UlhhQlVMfV5AQmFWfFN0QXNYdV92fldAcC0sT3RfQFliSH5RdkRiXn1feXRWQg==");//trial key
registerLicense("ORg4AjUWIQA/Gnt2UlhhQlVMfV5AQmBIYVp/TGpJfl96cVxMZVVBJAtUQF1hTX9SdkNjWH1ccXdURGhe");

const height = '550';
const width = '650';
let datasource = [
    {
        Id: 1,
        Subject: 'Explosion of Betelgeuse Star',
        Location: 'Space Centre USA',
        StartTime: new Date(2021, 0, 10, 9, 30),
        EndTime: new Date(2021, 0, 10, 11, 0),
        CategoryColor: '#1aaa55'
    }, {
        Id: 2,
        Subject: 'Thule Air Crash Report',
        Location: 'Newyork City',
        StartTime: new Date(2021, 0, 11, 12, 0),
        EndTime: new Date(2021, 0, 11, 14, 0),
        CategoryColor: '#357cd2'
    }, {
        Id: 3,
        Subject: 'Blue Moon Eclipse',
        Location: 'Space Centre USA',
        StartTime: new Date(2021, 0, 12, 9, 30),
        EndTime: new Date(2021, 0, 12, 11, 0),
        CategoryColor: '#7fa900'
    }, 
];

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
            selectedDate: new Date(2024, 11, 11),
            appointmentData: {
                dataSource: datasource
            },
            // fields: {
            //     id: 'Id',
            //     subject: { name: 'EventName' },
            //     isAllDay: { name: 'IsAllDay' },
            //     startTime: { name: 'StartTime' },
            //     endTime: { name: 'EndTime' }
            // }
        };
    },
    mounted() {
        pm.NProgress.done();

        // This code will be executed after the component is mounted
        console.log('Component is mounted!');

        // You can perform DOM manipulations or other actions here
        // const element = document.getElementById('scheduler-id');
        // if (element) {
        //     element.style.color = 'red';
        // }
    },
    methods: {
        closeModal() {
            console.log('Close Modal Func');
        }
    },
}
</script>

