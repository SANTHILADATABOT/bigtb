import GlobalKanban from '@components/global-kanban/global-kanban.vue'
// Pro tip: In order top put a menu on the frontend, just add a meta section.
weDevsPMRegisterChildrenRoute('project_root',
    [
        {
            path: 'kanban',
            component: GlobalKanban,
            name: 'kanban',
            meta: {
              label: 'Master Kanban',
              order: 2,
            }
        }
    ]
);
