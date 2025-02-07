
weDevsPmRegisterModule('projectLists', 'project-lists');

import overview from '@components/project/project-overview/router';
import activities from '@components/project/project-activities/router';
import files from '@components/project/project-files/router';
import {task_lists, single_list} from '@components/project/project-task-lists/router';
import {discussions, single_discussion} from '@components/project/project-discussions/router';
import {milestones} from '@components/project/project-milestones/router';
import {materials} from '@components/project/project-materials/router';

import empty from './empty.vue'

// These are Vue Components
import project_lists from './active-projects.vue'
import all_projects from './all-projects.vue'
import completed_projects from './completed-projects.vue'
import favourite_projects from './favourite-projects.vue'

weDevsPMRegisterChildrenRoute('projects', 
    [
        {
            path: 'active', 
            component: project_lists,
            name: 'project_lists',
            meta: {
                label: __( 'Projects', 'wedevs-project-manager'),
                order: 1,
            },
            children: [
                {
                    path: 'pages/:current_page_number', 
                    component: project_lists,
                    name: 'project_pagination',
                },
            ]
        },

        {
            path: 'all', 
            component: all_projects,
            name: 'all_projects',

            children: [
                {
                    path: 'pages/:current_page_number', 
                    component: all_projects,
                    name: 'all_project_pagination',
                },
            ]
        },

        {
            path: 'completed', 
            component: completed_projects,
            name: 'completed_projects',

            children: [
                {
                    path: 'pages/:current_page_number', 
                    component: completed_projects,
                    name: 'completed_project_pagination',
                },
            ]
        },

        {
            path: 'favourite', 
            component: favourite_projects,
            name: 'favourite_projects',

            children: [
                {
                    path: 'pages/:current_page_number', 
                    component: favourite_projects,
                    name: 'favourite_project_pagination',
                },
            ]
        },

    ]
)



weDevsPMRegisterChildrenRoute('project_root', 
    [   
        {
            path: 'projects', 
            component: empty,
            name: 'projects',
            children: wedevsPMGetRegisterChildrenRoute('projects'),
        }
    ]
);




