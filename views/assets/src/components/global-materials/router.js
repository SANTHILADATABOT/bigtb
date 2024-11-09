import MaterialOrdering from '@components/global-materials/material-ordering.vue'

weDevsPMRegisterChildrenRoute('project_root',
    [
        {
            path: '/materials',
            component: MaterialOrdering,
            name: 'materials',
            meta: {
                label: 'Materials',
                order: 4,
            }
        }
    ]
);
