
import SchedulerComp from './vue-scheduler.vue';

weDevsPMRegisterChildrenRoute('project_root', 
    [
        { 
            path: '/schedule',
            component: SchedulerComp,
            name: 'scheduler',
        },
    ]
);
