export default new pm.Vuex.Store({ 
    state: {
        allTasks: [],
        manageCapability:[],
        is_need_fetch_view_type: true,
        // project state
        projectLoaded : false,
        projectOverviewLoaded: false,
        projectDiscussLoaded: false,
        projectTaskListLoaded: false,
        projectTaskLoaded: false,
        projectMilestoneLoaded: false,
        projectFileLoaded: false,
        projectActivityLoaded: false,
        isFetchCategories: false,
        projects: [],
        project: {},
        projectMeta: {},
        project_users: [],
        // categories state
        categories: [],
        categoryMeta: {},
        // Global Kanban state
        globalKanban_columns: [],
        globalKanban_boardables: {},
        // Material Orders state
        materialOrders: [],
        materialVendors: [],
        users: [],
        currentUser: {},
        // Profitability
        invoices: [],
        materialCosts: 0,
        laborCosts: 0,
        // Reports
        reports: [],
        records: [],
        reportRows: [],
        employees: [],
        // more
        is_single_task: false,
        roles: [],
        milestones: [],
        milestones_load: false,
        is_project_form_active: false,
        projects_meta: {},
        pagination: {},
        listView: 'list',
        dropDownProjects: [],
        dropDownTaskTypes: [],
        taskCreateFormLists: [],
        
        getIndex: function ( itemList, id, slug) {
            var index = false;

            if(typeof itemList === 'undefined') return index;

            itemList.forEach(function(item, key) {
                if (item[slug] == id) {
                    index = key;
                }
            });

            return index;
        },
        assignees: [],
        history: {
            to: {},
            from: {}
        },
        showDescription: false
    },

    mutations: {
        setAllTasks (state, tasks) {
            state.allTasks = tasks;
        },
        updateShowDescription( state, status ) {
            status = status || false;

            state.showDescription = status;
        },
        afterDeleteProjectCount (state, project) {
            if (typeof project.project === 'undefined') {
                return;
            }
            if(project.project.status == 'incomplete') {
                state.projects_meta.total_incomplete = parseInt(state.projects_meta.total_incomplete) - 1;
            } else {
               state.projects_meta.total_complete = parseInt(state.projects_meta.total_complete) - 1;
            }
        },
        updateListViewType(state, view) {
 
            if(
                state.projectMeta.hasOwnProperty('list_view_type')
                    &&
                state.projectMeta.list_view_type
            ) {
                state.projectMeta.list_view_type.meta_value = view;
            } else {

                state.projectMeta['list_view_type']= {
                    meta_value: view
                }
            }
        },
        isSigleTask (state, status) {
            state.is_single_task = status;
        },
        // Global Kanban mutations
        setGK_columns (state, columns) {
            if (columns !== undefined) {
                state.globalKanban_columns = columns;
            }
        },
        setGK_boardables (state, boardables) {
            if (boardables !== undefined) {
                if (Object.keys(state.globalKanban_boardables).length === 0) {
                    state.globalKanban_boardables = boardables;
                } else {
                    state.globalKanban_boardables = Object.assign({}, state.globalKanban_boardables, boardables);
                }
            }
        },
        // Material Orders mutations
        setMaterialOrders (state, orders) {
            state.materialOrders = orders;
        },
        setMaterialVendors (state, vendors) {
            state.materialVendors = vendors;
        },
        setUsers (state, users) {
            state.users = users;
        },
        setCurrentUser (state, user) {
            state.currentUser = user;
        },
        // Profitability
        setMaterialCosts (state, material_costs) {
          state.materialCosts = material_costs;
        },
        setLaborCosts (state, labor_costs) {
          state.laborCosts = labor_costs;
        },
        setInvoices (state, invoices) {
          state.invoices = invoices;
        },
        // Reports
        setReports (state, reports) {
            state.reports = reports;
        },
        setRecords (state, records) {
            state.records = records;
        },
        setReportRows (state, rows) {
            state.reportRows = rows;
        },
        setEmployees (state, employees) {
            state.employees = employees;
        },
        // Project mutations
        setProjects (state, projects) {
            state.projects = projects.projects;
        },
        setProject (state, project) {
            if (state.projects.findIndex(x => x.id === project.id) === -1) {
                state.projects.push(project);
            }
            state.project = jQuery.extend(true, {}, project);
            state.projectLoaded = true;
        },
        setProjectMeta (state, projectMeta) {
            state.projectMeta = projectMeta;
        },
        updateProjectMeta (state, type) {
            if ( typeof state.project.meta === 'undefined') {
                return ;
            }

            if (state.project.meta.data.hasOwnProperty(type)) {
                state.project.meta.data[type] ++;
            }

        },
        decrementProjectMeta (state, type) {
            if ( typeof state.project.meta === 'undefined') {
                return ;
            }

            if (state.project.meta.data.hasOwnProperty(type)) {
                state.project.meta.data[type] --;
            }
        },
        setProjectUsers (state, users) {
            state.project_users = users;
        },
        // Category mutations
        afterNewCategories (state, categories) {
            state.categories.push(categories);
        },
        setCategories (state, categories) {
            state.categories = categories;
            state.isFetchCategories = true;
        },
        setCategoryMeta (state, meta) {
            state.categoryMeta = meta;
        },
        afterUpdateCategories (state, category) {
            var category_index = state.getIndex(state.categories, category.id, 'id');
            state.categories.splice(category_index,1, category);
        },
        afterDeleteCategory (state, id) {
            var category_index = state.getIndex(state.categories, id, 'id');
            state.categories.splice(category_index,1);
        },
        setRoles (state, roles) {
            state.roles = roles;
        },
        // More Project mutations
        newProject (state, projects) {
            var per_page = state.pagination.per_page,
                length   = state.projects.length;

            if (per_page <= length) {
                state.projects.splice(0,0,projects);
                state.projects.pop();
            } else {
                state.projects.splice(0,0,projects);
            }

            //update pagination
            state.pagination.total = state.pagination.total + 1;
            state.projects_meta.total_incomplete = state.projects_meta.total_incomplete + 1;
            state.pagination.total_pages = Math.ceil( state.pagination.total / state.pagination.per_page );
        },
        showHideProjectForm (state, status) {
            if ( status === 'toggle' ) {
                state.is_project_form_active = state.is_project_form_active ? false : true;
            } else {
                state.is_project_form_active = status;
            }
        },
        setProjectsMeta (state, data) {
            if ( typeof data !== 'undefined' ) {
                state.projects_meta = data;
                state.pagination = data.pagination;
            }
        },
        afterDeleteProject (state, project_id) {
            var project_index = state.getIndex(state.projects, project_id, 'id');
            state.projects.splice(project_index,1);
        },
        updateProject (state, project) {
            var index = state.getIndex(state.projects, project.id, 'id');
            
            pm.Vue.set(state.projects, index, project);
            //state.projects[index] = jQuery.extend(true, {}, project);
            state.project = jQuery.extend(true, {}, project);
        },
        showHideProjectDropDownAction (state, data) {
            var index = state.getIndex(state.projects, data.project_id, 'id');

            if (data.status === 'toggle') {
                state.projects[index].settings_hide = state.projects[index].settings_hide ? false : true;
            } else {
                state.projects[index].settings_hide = data.status;
            }
        },
        afterDeleteUserFromProject (state, data) {
            
            if ( data.project_id ) {
                var index = state.getIndex(state.projects, data.project_id, 'id');
                var users = state.projects[index].assignees.data;
                var user_index = state.getIndex(users, data.user_id, 'id');

                state.projects[index].assignees.data.splice(user_index, 1);
            } else {
                let index = state.getIndex(state.assignees, data.user_id, 'id');
                state.assignees.splice(index, 1);
            }
        },
        // User mutations
        updateSeletedUser (state, data) {
            if(data.project_id) {
                var index = state.getIndex(state.projects, data.project_id, 'id');
                state.projects[index].assignees.data.unshift(data.item);
            } else {
                state.assignees.push(data.item);
            }
            
            $('.pm-project-role').animate({ scrollTop: 0 }, "slow");
        },
        setSeletedUser(state, assignees) {
            state.assignees = assignees;
        },
        resetSelectedUsers (state) {
            state.assignees = [];
        },
        setMilestones(state, milestones){
            state.milestones = milestones;
            state.milestones_load = true;
        },
        is_need_fetch_view_type (state, status) {
            state.is_need_fetch_view_type = status;
        },
        setManageCapability( state, capability ) {
            state.manageCapability = capability;
        },
        setDefaultLoaded (state) {
            state.projectLoaded = false;
            state.projectOverviewLoaded = false;
            state.projectDiscussLoaded = false;
            state.projectTaskListLoaded = false
            state.projectTaskLoaded = false;
            state.projectMilesotneLoaded = false;
            state.projectFileLoaded = false;
            state.projectActivityLoaded = false;
        },
        recordHistory(state, history) {
            const to = jQuery.extend(true, {}, history.to);
            const from = jQuery.extend(true, {}, history.from);

            state.history = {
                to, from
            };
        },
        setCreatedUser (state, created_user) {
            state.newlyCreated = created_user;
        },
        resetCreatedUser (state){
            state.newlyCreated = {};
        },
        // Project and DropDown mutations
        setListInProject (state, data) {

            var index = state.getIndex(state.projects, data.project_id, 'id');

            if(index === false) return;

            //var project = state.projects[index];
          
            if( typeof state.projects[index].task_lists !== 'undefined') {
                
                data.lists.forEach(function(list, index) {
                    var listIndex = state.getIndex(state.projects[index].task_lists, list.id, 'id');
                    
                    if(index === false) {
                        state.projects[index].task_lists.push(list);
                    }
                });
            } 

            if( typeof state.projects[index].task_lists === 'undefined') {
                pm.Vue.set(state.projects[index], 'task_lists', {
                    'data': data.lists
                });
            }
        },
        setDropDownProjects (state, projects) {
            state.dropDownProjects = projects;
        },
        setDropDownProject (state, projects) {
            projects.forEach( project => {
                let index = state.getIndex( state.dropDownProjects, project.id, 'id' );
                
                if(index === false) {
                    state.dropDownProjects.push(project);
                }

            })
        },
        setDropDownTaskTypes (state, taskTypes) {
            state.dropDownTaskTypes = taskTypes;
        },
        updateTaskCreateFormLists (state, data) {

            if ( !Array.isArray(data.lists) ) {
                return;
            }

            if ( !parseInt(data.projectId) ) {
                return;
            }

            data.lists.forEach( list => {
                let index = state.getIndex( state.taskCreateFormLists, list.id, 'id' );
                
                if(index === false) {
                    state.taskCreateFormLists.push(list);
                }
            } )
            
        }
    }

});
