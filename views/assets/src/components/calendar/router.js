
import Calendar from '@components/calendar/calendar.vue';
import SchedulerComponent from '@components/calendar/scheduler.vue';
import MonthlyComponent from '@components/calendar/Monthlycalender.vue';  // Assuming you have this component
import MonthlyTimelineComponent from '@components/calendar/MonthlyTimelinecalender.vue';  // Assuming you have this component
import WeeklyCalendarComponent from '@components/calendar/weeklycalender.vue';  // Assuming you have this component
import MonthlyResourceTimelineComponent from '@components/calendar/Monthlyresourcetimeline.vue';  // Assuming you have this component
import ResourceTogglePanelComponent from '@components/calendar/resourseTogglepanel.vue';  // Assuming you have this component

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
