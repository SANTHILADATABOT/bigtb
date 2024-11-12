import Calendar from '@components/calendar/calendar.vue';
import Scheduler from './scheduler.vue';
// import Schedule from './schedule.vue';

weDevsPMRegisterChildrenRoute('project_root', 
    [
        { 
            path: '/calendar',
            component: Scheduler,
            // component: Calendar,
            name: 'scheduler',
        },
    ]
);
