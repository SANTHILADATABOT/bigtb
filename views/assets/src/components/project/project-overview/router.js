import overview from './overview.vue'

weDevsPmRegisterModule('projectOverview', 'project/project-overview');

weDevsPMRegisterChildrenRoute('projects', [
    {
      path: ':project_id/overview',
      component: overview,
      name: 'pm_overview',
    },
  ]
)

