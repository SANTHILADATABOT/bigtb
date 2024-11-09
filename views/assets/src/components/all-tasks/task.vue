<script>

export default{
  props: ['task'],

  mixins: [PmMixin.projectTaskLists],

  data () {
    return{
      task_status : this.task.status == 'complete' ? 1 : 0,
      taskId: false,
      projectId: false
    }
  },

  components: {
    'single-task': pm.SingleTask
  },

  created () {
    pmBus.$on('pm_after_close_single_task_modal', this.afterCloseSingleTaskModal);
  },

  methods: {
    getLabels (task) {
      return typeof task.labels == 'undefined' ? [] : task.labels.data;
    },
    doneUndone (){
      var self = this,
          task_status = this.task.status === 'complete' ? 0 : 1;
      var args = {
        data: {
          title: this.task.title,
          task_id: this.task.id,
          status : task_status,
          project_id: parseInt(this.task.project_id),
        },
        callback (res) {
          self.$store.commit("myTask/afterDoneUndoneTask", {task: res.data, route: self.$route.name});
        }
      }


      this.taskDoneUndone( args );
    },
    getSingleTask (task) {
      this.taskId = task.id;
      this.projectId = task.project_id;
    },
    afterCloseSingleTaskModal () {
      var params = {}, route = null;
      if (typeof this.$route.params.user_id !== 'undefined') {
        params.user_id = parseInt(this.$route.params.user_id)
      }

      if(this.$route.name == 'mytask_current_single_task') {
        route = 'mytask-current';
      }

      if(this.$route.name == 'mytask_complete_single_task') {
        route = 'mytask-complete';
      }

      if(this.$route.name == 'mytask_outstanding_single_task') {
        route = 'outstanding-task';
      }
      if(route) {
        this.$router.push({
          name: route,
          params: params
        });
      }
      this.taskId = false;
      this.projectId = false;
    },
  }
}
</script>

<template>
  <div class="pm-todo-wrap clearfix">
    <div class="pm-todo-inside">
<!--------------- Left Side -------------->
      <div class="tr-task-content-left">
<!------------------------------- Checkbox ------------------------------->
        <input :disabled="can_complete_task(task)" v-model="task_status" @click="doneUndone()" type="checkbox"  value="" name="" >
<!------------------------------- Task Title ------------------------------->
        <span class="task-title">
          <a href="#" @click.prevent="getSingleTask(task)" v-html="task.title"></a>
        </span>
<!------------------------------- Assigned Users-------------------------->
        <span class='pm-assigned-user' v-for="user in task.assignees.data" :key="user.ID">
          <a href="#" :title="user.display_name">
            <img class="tr-assignee" :src="user.avatar_url" :alt="user.display_name" height="48" width="48">
          </a>
        </span>
<!------------------------------- Due Date--------------------------------->
        <span v-if="taskTimeWrap(task)" :class="taskDateWrap(task.due_date.date)">
          <span>Due </span>
          <span v-if="task_start_field">{{ taskDateFormat( task.start_at.date ) }}</span>
          <span v-if="isBetweenDate( task_start_field, task.start_at.date, task.due_date.date )">&ndash;</span>
          <span>{{ taskDateFormat(task.due_date.date) }}</span>
        </span>
<!------------------------------- Label------------------------------------->
        <span class="task-label task-activity">
          <span class="label-block" v-for="(label, labelIndex) in getLabels(task)" :key="labelIndex">
            <span class="label-color" :style="'background-color:'+ label.color ">{{ label.title }}</span>
          </span>
        </span>
      </div>
<!------------------Center--------------->
      <div class="tr-task-content-center">
        <!------------------------------- Creation Date --------------------------->
        <span>
          <span>Created </span>
          <span>{{ taskDateFormat(task.created_at.date) }}</span>
        </span>
      </div>
      <div class="tr-task-content-right">
        <span class="pm-task-comment pm-todo-action-child">
          <router-link
            :to="{
              name: 'single_task',
              params: {
                task_id: task.id,
                list_id: task.task_list_id,
                project_id: task.project_id,
            }}">
            <span>View Task in Project / Comments</span>
            <span class="pm-comment-count">
              {{ task.meta.total_comment }}
            </span>
          </router-link>
        </span>
        <div class="pm-clearfix"></div>
      </div>
      <div class="clearfix"></div>
    </div>
    <div v-if="parseInt(taskId) && parseInt(projectId)">
      <single-task :taskId="taskId" :projectId="projectId"></single-task>
    </div>
  </div>
</template>

<style scoped>
.pm-todo-wrap {
  margin-bottom: 10px;
  padding: 5px;
  border: 1px solid #e0e0e0;
  background-color: #fff;
  align-items: center;
}

.pm-todo-inside {
  display: flex; /* This ensures that children elements are in a row */
  flex-wrap: nowrap; /* This prevents the children from wrapping */
  width: 100%; /* This ensures that the container takes full width */
  align-items: center; /* This vertically centers the children */
  justify-content: space-between; /* This distributes space between children */
}

.tr-task-content-left,
.tr-task-content-center,
.tr-task-content-right {
  display: flex;
  align-items: center;
}

.tr-task-content-left {
  justify-content: flex-start;
  text-align: left;
  flex: 3;
}

.tr-task-content-center {
  justify-content: center;
  flex: 1;
}

.tr-task-content-right {
  justify-content: flex-end;
  text-align: right;
  flex: 2;
}

.task-title {
  margin-left: 10px;
  margin-right: 10px;
}

.tr-assignee {
  margin-right: 5px;
  border-radius: 1rem;
  height: 1rem;
  width: 1rem;
}

.label-color {
  color: white;
  padding: 1px 3px;
  margin-left: 5px;
  border-radius: 2px;
}
</style>
