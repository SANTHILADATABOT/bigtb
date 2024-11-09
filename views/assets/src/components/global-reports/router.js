import ReportsNav from '@components/global-reports/reports-nav.vue';
import ReportsHome from '@components/global-reports/reports-home.vue';
import ReportsPage from '@components/global-reports/reports-page.vue';
import PartsLookup from '@components/global-reports/parts-lookup.vue';
import WIPSchedule from '@components/global-reports/wip-schedule.vue';

weDevsPMRegisterChildrenRoute('project_root',
    [
        {
            path: '/db-reports',
            component: ReportsNav,
            name: 'db-reports',
            meta: {
                label: 'Reports Home',
                order: 7,
            },
            children: [
                {
                    path: 'home',
                    component: ReportsHome,
                    name: 'home'
                },
                {
                    path: 'reports',
                    component: ReportsPage,
                    name: 'reports'
                },
                {
                    path: 'parts',
                    component: PartsLookup,
                    name: 'parts'
                },
                {
                    path: 'wip',
                    component: WIPSchedule,
                    name: 'wip'
                }
            ]
        }
    ]
);
