weDevsPmRegisterModule('projectMilestones', 'project/project-milestones');

import milestones_route from './milestones.vue'

weDevsPMRegisterChildrenRoute('projects', 
    [
        {
            path: ':project_id/milestones/', 
            component: milestones_route,
            name: 'milestones',

            children: [
                {
                    path: 'pages/:current_page_number', 
                    component: milestones_route,
                    name: 'milestone_pagination',
                },
            ]
        }
    ]
)
