import projects from '@components/project-lists/router';
import categories from '@components/categories/router';
import add_ons from '@components/add-ons/router';
import '@components/importtools/router';
import '@components/my-tasks/router';
import '@components/global-kanban/router';
import '@components/global-materials/router';
import '@components/global-profitability/router';
import '@components/global-reports/router';
import '@components/project/project-materials/router';
import '@components/all-tasks/router';
import '@components/calendar/router';
import '@components/progress/router';

import {general, email} from '@components/settings/router';
import Empty from '@components/root/init.vue';

weDevs_PM_Routers.push({
	path: '/',
    component:  Empty,
    name: 'project_root',

	children: wedevsPMGetRegisterChildrenRoute('project_root')
});

var router = new pm.VueRouter({
	routes: weDevs_PM_Routers,
});

router.beforeEach((to, from, next) => {
    pm.NProgress.start();
    next();
});

//Load all components mixin
weDevsPmModules.forEach(function(module) {
    let mixin = require('@components/'+module.path+'/mixin.js');
    PmMixin[module.name] = mixin.default;
});


export default router;
