<template>
    <div>
        <project-list-header></project-list-header>
        
        <div class="active-projects">
<!--------------------------------------------------------------------------- Show the loading animation -------------->
            <div v-if="loading" class="pm-row pm-data-load-before" >
                <div class="pm-col-4">
                    <project-loading></project-loading>
                </div>
                <div class="pm-col-4">
                    <project-loading></project-loading>
                </div>
                <div class="pm-col-4">
                    <project-loading ></project-loading>
                </div>
                <div class="pm-col-4">
                    <project-loading ></project-loading>
                </div>
                <div class="pm-col-4">
                    <project-loading ></project-loading>
                </div>
                <div class="pm-col-4">
                    <project-loading ></project-loading>
                </div>
            </div>
<!--------------------------------------------------------------------------- Show Projects when loaded --------------->
            <div v-else>
                <div class="pm-projects pm-row pm-no-padding pm-no-margin" :class="[projects_view_class()]">
                    <div class="pm-overview-container">
                        <div class="pm-panel pm-overview-panel">
                            <!-- pm panel header --------White box with viewing options ------------------->
                            <div class="pm-panel-header">
                                <div class="pm-grid-row">
                                    <!-- project page header menu ---"Active", "Completed"... -------->
                                    <project-header-menu></project-header-menu>

                                    <!-- pm header right column -->
                                    <div class="pm-col-6-sm">
                                        <!-- pm view style container -->
                                        <div class="pm-view-style-container pm-text-right">
                                            <!-- project view component ---- Grid View, List View ---->
                                            <project-view></project-view>
                                            <!-- filter by category component -->
                                            <project-filter-by-category></project-filter-by-category>
                                        </div>
                                    </div> <!-- end col -->
                                </div>
                            </div>

                            <!-- start panel body --------------Where the projects are -------------------->
                            <div class="pm-panel-body">
                                <div class="pm-grid-row">
                                    <project-summary></project-summary>
                                    <pm-pagination 
                                        :total_pages="total_pages" 
                                        :current_page_number="current_page_number" 
                                        component_name='project_pagination'>
                                    </pm-pagination> 
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>       
        </div>
    </div>
</template>

<script>
    import project_header_menu from './project-header-menu.vue';
    import project_view from './project-view.vue';
    import project_filter_by_category from './project-filter-by-category.vue';
    import project_summary from './project-summary.vue';
    import pagination from './../common/pagination.vue';
    import after_project from './../common/do-action.vue';
    import project_loading from './project-loading.vue';
    import Header from './header.vue';
    import Mixins from './mixin';

    export default  {

        data () {
            return {
                current_page_number: 1,
                loading: true,
            }
        },

        mixins: [Mixins],

        created () {
            this.projectQuery();
            this.getRoles();
            this.getProjectCategories();
        },

        watch: {
            '$route' (route) {
                this.current_page_number = route.params.current_page_number;
                this.projectQuery();
            }
        },

        computed: {
            total_pages () {
                return this.$store.state.pagination.total_pages;
            },
            project () {
                
                var projects = this.$root.$store.state.projects;
                var index = this.getIndex(projects, this.project_id, 'id');
                var project = {};
                
                if ( index !== false ) {
                    project = projects[index];
                } 
                
                return project;
            },
        },
        
        components: {
            'project-summary': project_summary,
            'pm-pagination': pagination,
            'do-action': after_project,
            'project-loading': project_loading,
            'project-list-header': Header,
            'project-header-menu': project_header_menu,
            'project-view': project_view,
            'project-filter-by-category': project_filter_by_category
        },

        methods: {
            projectQuery () {
                var self = this;
                var args = {
                    conditions: {
                       status: 'incomplete',
                       with: 'assignees',
                       project_meta: 'all',
                       orderby: 'id:desc',
                    },

                    callback (res) {
                        self.projectFetchStatus(true);
                        self.loading = false;
                    }
                }

                this.loading = true;
                this.getProjects(args);
            }
        }
    }

</script>

<style>
.pm-project-meta .message .fa-circle {
    color: #4975a8;
}   

.pm-project-meta .todo .fa-circle {
    color: #68af94;
}   

.pm-project-meta .files .fa-circle {
    color: #71c8cb;
}   

.pm-project-meta .milestone .fa-circle {
    color: #4975a8;
}   

</style>

