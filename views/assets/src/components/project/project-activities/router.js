
weDevsPmRegisterModule('projectActivities', 'project/project-activities');

import activities_route from './activities.vue'

weDevsPMRegisterChildrenRoute('projects', 
	[
		{
		    path: ':project_id/activities/', 
		    component: activities_route, 
		    name: 'activities',
            children: [
                {
                    path: 'pages/:current_page_number', 
                    component: activities_route, 
                    name: 'activities_pagination',
                },
                {
                    path: 'tasks/:task_id', 
                    components: {
                        singleTask: pm.SingleTask
                    },
                    name: 'activity_single_task',
                },
            ]
		}
	]
);
 
