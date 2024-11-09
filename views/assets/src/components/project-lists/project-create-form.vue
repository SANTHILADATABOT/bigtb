<script>
import ProjectNewUserForm from '@components/project-lists/project-new-user-form.vue';
import Mixins from './mixin';
import  '@components/project-lists/directive.js'

export default {
  props: { //projectFormStatus
    project: {
      type: Object,
      default () {
        return {};
      }
    }
  },
  data () {
    return {
      project_name: '',
      project_cat: 0,
      project_description: typeof this.project.description == 'undefined' ? '' : this.project.description.content,
      project_notify: false,
      assignees: [],
      show_spinner: false,
      name_of_the_project: 'Name of the customer',
      details_of_project: 'Some details about the customer (optional)',
      search_user: __( 'Search users...', 'wedevs-project-manager'),
      create_new_user: __( 'Create a new user', 'wedevs-project-manager'),
      add_new_project:'Add New Customer',
      update_project: 'Update Customer',
      client: __("Client", 'wedevs-project-manager'), // Dont Remove this one its require for Client translation
    }
  },
  components: { ProjectNewUserForm },
  computed: {
    roles () {
      return this.$root.$store.state.roles;
    },
    categories () {
      return this.$root.$store.state.categories;
    },
    selectedUsers () {
      if(!this.project.hasOwnProperty('assignees')) {
        return this.$store.state.assignees;
      } else {
        const projects = this.$store.state.projects;
        const index = projects.findIndex(i => i.id == this.project.id);
        if (index !== -1) {
          return projects[index].assignees.data;
        }
      }
    },
    project_category: {
      get () {
        if ( this.project.hasOwnProperty('id') ) {
          if (
              typeof this.project.categories !== 'undefined'
              &&
              this.project.categories.data.length
          ) {

            this.project_cat = this.project.categories.data[0].id;

            return this.project.categories.data[0].id;
          }
        }

        return this.project_cat;
      },
      set (cat) {
        this.project_cat = cat;
      }
    },
    show_role_field () {
      return typeof PM_BP_Vars !== 'undefined' ? PM_BP_Vars.show_role_field : true;
    },
    getProjectDetails(){
      try {
        var project = this.$store.state.project ;
        if(this.$router.currentRoute.fullPath == '/projects/active'){
          this.project_description = '' ;
        }else{
          this.project_description = project.description.content ;
          return project ;
        }
      }catch (e) {}
    }
  },
  methods: {
    deleteUser (del_user) {
      if ( !this.canUserEdit(del_user.id) ) {
        return;
      }

      this.$store.commit(
          'afterDeleteUserFromProject',
          {
            project_id: this.project_id,
            user_id: del_user.id
          }
      );
    },
    canUserEdit (user) {

      if (this.has_manage_capability()) {
        return true;
      }

      if (user.manage_capability) {
        return false;
      }

      if (this.current_user.data.ID == user.id) {
        return false;
      }

      return true

    },
    projectFormAction () {
      if ( this.show_spinner ) {
        return;
      }

      if ( !this.project.title ) {
        pm.Toastr.error('Customer name is required.');
        return;
      }

      this.show_spinner = true;

      var args = {
        data: {
          'title': this.project.title,
          'categories': this.project_cat ? [this.project_cat]: null,
          'description': this.project_description,
          'notify_users': this.project_notify,
          'assignees': this.formatUsers(this.selectedUsers),
          'status': this.project.status,
          'department_id': this.project.department_id
        }
      }

      var self = this;
      if (this.project.hasOwnProperty('id')) {
        args.data.id = this.project.id;
        args.callback = function ( res ) {
          self.show_spinner = false;
          self.$store.commit('setProjectUsers', res.data.assignees.data);
          self.$emit('makeFromClose', false);
        }
        this.updateProject ( args );


      } else {
        args.callback = function(res) {
          self.project.title = '';
          self.project_cat = 0;
          self.project.description = ''
          self.project_notify = [];
          self.project.status = '';
          self.show_spinner = false;
          self.$router.push({
            name: 'pm_overview',
            params: {
              project_id: res.data.id
            }
          });
        }

        this.newProject(args);
      }
    },
    setProjectUser () {
      if ( this.project.hasOwnProperty('id') ) {
        this.$root.$store.commit('setSeletedUser', this.project.assignees.data);
      } else {
        this.$root.$store.commit('resetSelectedUsers');
      }
    },
    closeForm () {
      if(!this.project.hasOwnProperty('id')) {
        this.project.title = '';
        this.project_cat = 0;
        this.project.description = ''
        this.project_notify = [];
        this.project.status = '';
        this.$store.commit('setSeletedUser', []);
      }
      this.$emit('disableProjectForm');
      this.showHideProjectForm(false);
    }
  },
  mixins: [Mixins],
  updated () {
    this.getProjectDetails ;
  },
  created () {
    this.setProjectUser();
  },
}
</script>

<template>
  <div class="create-project-modal">
    <div class="popup-mask">
      <div class="popup-container">
        <!--The X button-->
        <span class="close-modal">
          <a  @click="closeForm"><span class="dashicons dashicons-no"></span></a>
        </span>
        <div class="popup-body" style="overflow: hidden;">
          <form action="" method="post" class="pm-form pm-project-form" @submit.prevent="projectFormAction();">
            <!-------------------------------------------Customer Name------------------>
            <div class="item project-name">
              <input type="text" v-model="project.title"  id="project_name" :placeholder="name_of_the_project" size="45" />
            </div>
            <!---------------------------------------------Category--------------------->
            <div class="pm-form-item item project-category">
              <select v-model="project_category"  id='project_cat' class='chosen-select'>
                <option value="0">Category</option>
                <option v-for="category in categories" :value="category.id" :key="category.id" >{{ category.title }}</option>
              </select>
            </div>
            <!--------------------------------------------Description------------------->
            <div class="pm-form-item item project-detail">
              <textarea v-model="project_description"  class="pm-project-description" id="" rows="5" :placeholder="details_of_project"></textarea>
            </div>
            <!-----------------------------------------Personnel Involved--------------->
            <div class="pm-project-form-users-wrap" v-if="selectedUsers.length">
              <div class="pm-form-item pm-project-role" v-if="show_role_field">
                <table>
                  <tr v-for="projectUser in selectedUsers" :key="projectUser.id">
                    <td>{{ projectUser.display_name }}</td>
                    <td class="user-td">
                      <select  v-model="projectUser.roles.data[0].id" :disabled="!canUserEdit(projectUser)">
                        <option v-for="role in roles" :value="role.id" :key="role.id" >{{ __(role.title, 'wedevs-project-manager') }}</option>
                      </select>
                    </td>
                    <td>
                      <a @click.prevent="deleteUser(projectUser)" v-if="canUserEdit(projectUser)" hraf="#" class="pm-del-proj-role pm-assign-del-user">
                        <span class="dashicons dashicons-trash"></span>
                        <!-- <span class="title">{{ __( 'Delete', 'wedevs-project-manager') }}</span> -->
                      </a>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
            <!--------------------------------------------Add Personnel----------------->
            <div class="pm-form-item item project-users" v-if="show_role_field">
              <input v-pm-users class="pm-project-coworker" type="text" name="user" :placeholder="search_user" size="45">
            </div>
            <pm-do-action hook="pm_project_form" :actionData="project"></pm-do-action>
            <div class="pm-form-item item project-notify">
              <label>
                <input type="checkbox" v-model="project_notify" name="project_notify" id="project-notify" value="yes" />
                {{ __( 'Notify Co-Workers', 'wedevs-project-manager') }}
              </label>
            </div>
            <div class="submit">
              <input v-if="project.id" type="submit" name="update_project" id="update_project" class="pm-button pm-primary" :value="update_project">
              <input v-if="!project.id" type="submit" name="add_project" id="add_project" class="pm-button pm-primary" :value="add_new_project">
              <a @click="closeForm" class="pm-button pm-secondary project-cancel" href="#">{{ __( 'Close', 'wedevs-project-manager') }}</a>
              <span v-show="show_spinner" class="pm-loading"></span>
            </div>
          </form>
          <div v-pm-user-create-popup-box id="pm-create-user-wrap" class="pm-new-user-wrap" :title="create_new_user">
            <project-new-user-form></project-new-user-form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="less" scoped>
.create-project-modal .popup-mask .popup-container {
  height: 400px !important;
  top: 110px !important;
}

.pm-project-form {
  .project-department {
    label {
      line-height: 1;
      display: block;
      margin-bottom: 5px;
    }
    select {
      display: block;
    }
  }
  .pm-project-form-users-wrap {
    overflow: hidden;
    .pm-project-role {
      max-height: 150px;
      overflow: auto;

      .user-td {
        width: 115px;
        select {
          padding-left: 10px !important;
        }
      }
    }
  }
}
</style>
