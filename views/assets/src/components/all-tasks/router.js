import AllTasks from '@components/all-tasks/all-tasks';
weDevsPMRegisterChildrenRoute( "project_root",
    [
        {
            path: 'all-tasks',
            component: AllTasks,
            name: 'all-tasks',
            meta: {
                label: 'All Tasks',
                order: 3,
            }
        }
    ]
);

weDevs_PM_Components.push({
    hook: 'pm-my-task-menu',
    component: 'pm-pro-my-task-menu',
    property: Menu
});

// The placeholder reports menu for the free version of the plugin

import Menu from '@components/all-tasks/menu';
import Reports from '@components/all-tasks/reports';

weDevsPMRegisterChildrenRoute( "my_tasks",
  [
    {
      path: 'reports',
      component: Reports,
      name: 'mytask-reports',
    }
  ]
);

weDevs_PM_Components.push({
  hook: 'pm-my-task-menu',
  component: 'pm-pro-my-task-menu',
  property: Menu
});
