
import Calendar from '@components/calendar/calendar.vue';
import SchedulerComponent from '@components/calendar/scheduler.vue';

weDevsPMRegisterChildrenRoute('project_root', 
    [
        { 
            path: '/calendar',
            // component: Calendar,
            component: SchedulerComponent,
            name: 'sfscheduler',
        },
    ]
);
