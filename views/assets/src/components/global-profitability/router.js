import Profitability from '@components/global-profitability/profitability.vue'

weDevsPMRegisterChildrenRoute('project_root',
    [
        {
            path: '/profitability',
            component: Profitability,
            name: 'profitability',
            meta: {
              label: 'Profitability',
              order: 6,
            }
        }
    ]
);
