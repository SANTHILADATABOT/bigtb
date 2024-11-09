export default {
    data () {
        return {

        }
    },
    computed: {
        ...pm.Vuex.mapState('projectLists',
            {
                projects_view: state => state.projects_view,
            }
        ),

        isFetchProjects () {
            return this.$store.state.projectLists.isFetchProjects;
        }
    },
    methods: {
        getGlobalKanban() {
            const self = this;
            const request_data = {
                type: 'GET',
                url: self.base_url + 'pm/v2/global-kanboard',
                success: function (res) {
                    // res.data provides a list of the boards associated with the global kanban
                    if (res.data !== undefined) {
                        self.$store.commit("setGK_columns", res.data);
                    }
                },
                error: function (res) {
                    console.error('Failed to fetch global kanban:', res);
                }
            };
            self.httpRequest(request_data);
        },
        getProjectBoardables(board_id) {
            const self = this;
            const request_data = {
                type: 'GET',
                url: `${self.base_url}pm/v2/global-kanboard/${board_id}/projects`,
                success: function (res) {
                    // res.data provides a list of the projects associated with the global kanban column
                    if (res.data !== undefined) {
                        self.$store.commit("setGK_boardables", { [board_id] : res.data });
                    }
                },
                error: function (res) {
                    console.error('Failed to fetch global kanban:', res);
                }
            };
            self.httpRequest(request_data);
        },
        addProjectBoardable(board_id, project) {
            // Adds Project to pm_boardables table in the database
            // Not to be confused for use as updateProjectBoardable or removeProjectBoardable
            const self = this;
            const request_data = {
                type: 'POST',
                url: self.base_url + 'pm/v2/global-kanboard/' + board_id + '/project/' + project.id,
                success: function (res) {
                    // res.data is empty in this case, simply refresh the global kanban
                    self.getProjectBoardables(board_id);
                },
                error: function (res) {
                    console.error('Failed to set project as boardable:', res);
                }
            };
            self.httpRequest(request_data);
        },
        updateProjectBoardable(from_board_id, project_id, to_board_id) {
            const self = this;
            const request_data = {
                type: 'PUT',
                url: self.base_url + 'pm/v2/global-kanboard/boardable',
                data: {from_board_id, project_id, to_board_id},
                error: function (res) {
                    console.error('Failed to update project as boardable:', res);
                }
            };
            self.httpRequest(request_data);
        },
        removeProjectBoardable(board_id, project_id) {
            // Removes Project from pm_boardables table in the database
            // Not to be confused for use as addProjectBoardable or updateProjectBoardable
            const self = this;
            const request_data = {
                type: 'DELETE',
                url: self.base_url + 'pm/v2/global-kanboard/' + board_id + '/boardable/' + project_id,
                success: function (res) {
                    self.getProjectBoardables(board_id);
                },
                error: function (res) {
                    console.error('Failed to remove project as boardable:', res);
                }
            };
            self.httpRequest(request_data);
        },
        addBoard(title) {
          const self = this;
          const request_data = {
            type: 'POST',
            url: self.base_url + 'pm/v2/global-kanboard',
            data: { title },
            success: function (res) {
              self.getGlobalKanban();
            },
            error: function (res) {
              console.error('Failed to add board:', res);
            }
          };
          self.httpRequest(request_data);
        },
        deleteBoard(board_id) {
          const self = this;
          const request_data = {
            type: 'DELETE',
            url: self.base_url + 'pm/v2/global-kanboard/' + board_id,
            success: function (res) {
              console.log("board deleted");
              self.getGlobalKanban();
            },
            error: function (res) {
              console.error('Failed to delete board:', res);
            }
          };
          self.httpRequest(request_data);
        },
        updateBoardOrder(updated_boards) {
          const self = this;

          for (let i = 0; i < updated_boards.length; i++) {
            updated_boards[i].order = i;
          }

          const request_data = {
            type: 'PUT',
            url: self.base_url + 'pm/v2/global-kanboard/',
            data: { updated_boards },
            error: function (res) {
              console.error('Failed to update board order:', res);
            }
          };
          self.httpRequest(request_data);
        }
    }
}
