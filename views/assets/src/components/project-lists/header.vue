<template>

        <!-- header -->
        <div class="pm-header">
            <h1 style="display: none;"></h1>
            <div class="pm-container">
                <div class="pm-header-row">
                    <!-- header-left -->
                    <div class="pm-header-left">
                        <h2 class="pm-d-inline"> {{ __('Project Manager', 'wedevs-project-manager') }}</h2>
                        <project-new-project-btn v-if="userCanAccess( PM_Vars.manager_cap_slug )"></project-new-project-btn>


                    </div> <!-- end header-left -->

                    <!-- header-right -->
                    <div class="pm-header-right pm-text-right">
                        <pm-do-action hook="pm_projects_header" ></pm-do-action>
                    </div>
                </div>
            </div>
        </div>

        
</template>

<script>
    import project_new_project_btn from './project-new-project-btn.vue';
    import project_filter_by_category from './project-filter-by-category.vue';
    import project_search_by_client from './project-search-by-client.vue';
    import project_search_all from './project-search-all.vue';
    import project_header_menu from './project-header-menu.vue';
    import project_view from './project-view.vue';
    import project_create_form from './project-create-form.vue';

    // custom imports
    import globally_task_create_form from '../common/globally-task-create-form.vue';
    import globally_task_create_btn from './globally-task-create-btn.vue';
    // import project home page's styles
    import '@helpers/less/project/pm-project.less';



    export default {
        components: {
            'project-new-project-btn': project_new_project_btn,
            'project-filter-by-category': project_filter_by_category,
            'project-search-by-client': project_search_by_client,
            'project-search-all': project_search_all,
            'project-header-menu': project_header_menu,
            'project-view': project_view,
            'project-create-form': project_create_form,
            'globally-task-create-form': globally_task_create_form,
            'globally-task-create-btn': globally_task_create_btn
        },
        data () {
            return {
                start_new_project: "Add a New Customer",
                start_new_task: "Add a New Task"
            }
        },

        created () {
            
        }

    }
</script>

<style lang="less">
    .pm-page-wrapper .pm-overview-panel .pm-panel-header {
        border-radius: 3px 3px 0 0 !important;
    }

    .pm-project-content {

    }
    

    .ui-dialog {
        left: 50% !important;
        transform: translate(-50%, 0);
        max-width: calc(100% - 50px);
    }

</style>


