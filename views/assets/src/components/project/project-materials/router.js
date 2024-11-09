
weDevsPmRegisterModule('projectMaterials', 'project/project-materials');

import projectMaterials from './project-materials.vue'

weDevsPMRegisterChildrenRoute('projects',
  [
    {
      path: ':project_id/materials/',
      component: projectMaterials,
      name: 'project_materials',
    }
  ]
);
