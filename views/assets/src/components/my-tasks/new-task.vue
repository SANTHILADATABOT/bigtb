<script>
import Multiselect from 'vue-multiselect'
export default {
  components: {
    'multiselect': Multiselect,
  },
  props: {
    users: {
      type: [Array],
      default () { return [] }
    },
  },
  methods: {
    afterCreateTask ( task ) {
      this.closePopup();

      // Refresh the pm-calendar data
      $('#pm-calendar').fullCalendar('removeEvents');
      $('#pm-calendar').fullCalendar('refetchEvents');

      // Refresh Task overview at a glance data
      this.$store.commit("myTask/afterCreateTask", {task: task.data, route: this.$route.name});
    },
    changeProject (project) {
      this.getLists(project.id);
      this.setUsers();
      this.setProjectId();

    },
    setProjectId () {
      this.project_id = this.project.id;
    },
    setUsers () {
      var self = this;
      let index = this.getIndex( this.projects, this.project.id, 'id' );
      var users = this.projects[index].assignees.data;
      var setUsers = [];

      if(this.users.length) {
        this.users.forEach(function(user_id) {
          let index = self.getIndex(users, parseInt(user_id), 'id');
          if(index !== false) {
            setUsers.push(users[index]);
          }
        });

        if(setUsers.length) {
          this.$store.commit('setProjectUsers', setUsers);
          this.task.assignees.data = setUsers;
        }

      } else {
        this.$store.commit('setProjectUsers', users);
      }
    },
    getProjects () {
      var self = this;
      var userID = PM_Vars.current_user.ID;
      if( !this.isEmpty( this.$route.params.user_id ) ) {
        userID = this.$route.params.user_id;
      }

      var data = {
        select: 'id, title',
        with: 'assignees',
        status: 'incomplete',
        inUsers: userID
      }

      if(this.users.length) {
        data.inUsers = this.users;
      }

      var request = {
        url: self.base_url + 'pm/v2/projects',
        data: data,
        success (res) {
          self.projects = res.data;
          self.fetchProjects = true;

          if(res.data.length) {
            self.project = res.data[0];
            self.getLists(res.data[0].id);
            self.setUsers();
            self.setProjectId();
          }
        },
        error (res) {
          console.error("failed to fetch projects for new task form");
        }
      }
      self.httpRequest(request);
    },
    asyncListsFind (title) {
      if(title == '') return;
      var self = this;
      clearTimeout(this.timeout);

      // Make a new timeout set to go off in 800ms
      this.timeout = setTimeout(function () {
        self.findLists(title);
      }, 500);
    },
    findLists (title) {
      var self = this;

      if(self.listAbort) {
        self.listAbort.abort();
      }

      var request = {
        url: self.base_url + 'pm/v2/advanced/'+self.project.id+'/task-lists',
        data: {
          select: 'id, title, project_id',
          project_id: self.project.id,
          title: title,
        },
        success: function(res) {
          self.loadingListSearch = false;
          self.setList(res.data);
        }
      }
      self.loadingListSearch = true;
      self.listAbort = self.httpRequest(request);
    },
    setList (lists) {
      var self = this;
      var newLists = [];

      lists.forEach(function(list) {
        let index = self.getIndex(self.lists, list.id, 'id');

        if(index === false) {
          newLists.push(list);

          let storeIndex = self.getIndex(self.storeLists, self.project.id, 'project_id');
          self.storeLists[storeIndex].lists.push(list);
        }
      })

      if(newLists.length) {
        this.lists = this.lists.concat(newLists);
      }
    },
    getLists (project_id) {
      var self = this;

      if(self.listHasInStore(project_id)) {
        self.setListInlocal(project_id);
        return;
      }

      var request = {
        url: self.base_url + 'pm/v2/advanced/'+project_id+'/task-lists',
        data: {
          select: 'id, title, project_id',
          project_id: project_id,
          per_page: 300
        },
        success (res) {
          self.lists = res.data;
          self.loadingLists = false;

          if(res.data.length) {
            self.list = res.data[0];
          }
          self.storeLists.push({
            'project_id': project_id,
            'lists': res.data
          });
        },

        error (res) {

        }
      }
      self.loadingLists = true;
      self.lists = [];
      self.httpRequest(request);
    },
    listHasInStore (project_id) {
      let index = this.getIndex(this.storeLists, project_id, 'project_id');

      if(index === false) {
        return false;
      }

      return true;
    },
    setListInlocal (project_id) {
      let index  = this.getIndex(this.storeLists, project_id, 'project_id');
      this.lists = this.storeLists[index].lists;
      this.list  = this.lists[0];
    },
    closePopup () {
      this.$emit('disableTaskForm');
    }
  },
  data () {
    return {
      timeout: '',
      listAbort: '',
      loadingListSearch: false,
      fetchProjects: false,
      loadingLists: false,
      projects: [],
      project: {},
      lists: [],
      list: {},
      task: {
        start_at: {date: ''},
        due_date: {date: ''},
        description: {content: ''},
        assignees: {data: []}
      },
      storeLists: []
    }
  },
  computed: {
    hasProjects () {
      return this.projects.length ? true : false;
    },
    hasLists () {
      return this.lists.length ? true : false;
    }
  },
  created () {
    // This is a local function - not from the mixin
    this.getProjects();
  }
}
</script>

<template>
    <div class="calendar-task-modal">
        <div class="popup-mask">
            <div class="popup-container">
                <!--The X button-->
                <span class="close-modal">
                    <a  @click.prevent="closePopup()"><span class="dashicons dashicons-no"></span></a>
                </span>
                <div class="popup-body" style="overflow: hidden;">
                    <!-- Form Title-->
                    <div class="new-task-label">
                        <span>{{__('New Task', 'wedevs-project-manager')}}</span>
                    </div>
                    <div class="form-fields project-list-drop-down">
                        <!---------------------------------------Project Field------>
                        <div>
                            <label  class="label">{{__('Project', 'wedevs-project-manager')}}</label>
                            <multiselect
                                v-model="project"
                                :options="projects"
                                :show-labels="false"
                                :placeholder="__( 'Type project name', 'wedevs-project-manager')"
                                @select="changeProject"
                                label="title"
                                :allowEmpty="false"
                                track-by="id">
                            </multiselect>

                            <div v-if="fetchProjects && !hasProjects">{{ __('No project found!', 'wedevs-project-manager') }}</div>

                            <div v-if="!fetchProjects && !hasProjects" class="loading-animation">
                                <div class="loading-projects-title">{{ __( 'Loading projects', 'wedevs-project-manager') }}</div>
                                <div class="load-spinner">
                                    <div class="rect1"></div>
                                    <div class="rect2"></div>
                                    <div class="rect3"></div>
                                    <div class="rect4"></div>
                                </div>
                            </div>
                        </div>
                      <!---------------------------------------Task List Field------>
                        <div v-if="hasProjects">
                            <div>
                                <div>
                                    <label class="label">{{__('In List', 'wedevs-project-manager')}}</label>
                                    <multiselect
                                        v-model="list"
                                        :options="lists"
                                        :show-labels="false"
                                        :loading="loadingListSearch"
                                        @search-change="asyncListsFind"
                                        :placeholder="__('Type task list name', 'wedevs-project-manager')"
                                        label="title"
                                        :allowEmpty="false"
                                        track-by="id">
                                    </multiselect>
                                </div>

                                <div v-if="!loadingLists && !hasLists">{{ __('No task lists found!', 'wedevs-project-manager') }}</div>

                                <div v-if="loadingLists && !hasLists" class="loading-animation">
                                    <div class="loading-projects-title">{{ __( 'Loading task lists', 'wedevs-project-manager') }}</div>
                                    <div class="load-spinner">
                                        <div class="rect1"></div>
                                        <div class="rect2"></div>
                                        <div class="rect3"></div>
                                        <div class="rect4"></div>
                                    </div>
                                </div>
                            </div>
                          <!---------------------------------------Name Field------>
                            <div v-if="hasLists">
                                <label class="label">{{__('Task', 'wedevs-project-manager')}}</label>
                                <pm-new-task-form  
                                    :task="task" 
                                    :list="list"
                                    @pm_after_create_task="afterCreateTask">
                                </pm-new-task-form>
                            </div>
                        </div>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="less">

.calendar-task-modal .popup-mask .popup-container {
  height: 300px !important;
  top: 110px !important;
  .label {
    color: #000;
  }
  .new-task-label {
    text-align: center;
    color: #000;
    font-size: 16px;
    font-weight: 600;
  }
  .form-fields {
    .loading-animation {
      display: flex;
      align-items: center;
      margin-left: 33%;
      color: #000;

      .load-spinner {
        margin: 0;
      }
    }
  }

  .project-list-drop-down {

    .multiselect {
      margin-bottom: 5px !important;
      border-radius: none;
      color: #999;
      font-weight: 300;

      .multiselect__tags {
        border-radius: 0;
      }

      .multiselect__single {
        margin-bottom: 0;
      }
      .multiselect__select {

      }
      .multiselect__input {
        border: none;
        box-shadow: none;
        margin: 0;
        font-size: 14px;
        vertical-align: baseline;
        height: 0;
      }
      .multiselect__element {
        .multiselect__option {
          font-weight: normal;
          white-space: normal;
          padding: 6px 12px;
          line-height: 25px;
          font-size: 14px;
        }

      }
      .multiselect__tags {
        min-height: auto;
        padding: 4px;
        border-color: #ddd;
        white-space: normal;

        .multiselect__tag {
          margin-bottom: 0;
          overflow: visible;
          border-radius: 3px;
          margin-top: 2px;
        }
      }
    }
  }
}
</style>
