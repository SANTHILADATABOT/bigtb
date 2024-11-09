<template>
  <div>
    <!-- "New Project" Button -->
    <b-button variant="primary" @click="showProjectModal = true">New Project</b-button>
    <!-- "New Customer" Button -->
    <b-button variant="primary" @click="showProjectModal = true">New Customer</b-button>
    <!-- "New Task" Button -->
    <b-button variant="primary" @click="showTaskModal = true">New Task</b-button>
    <!-- Customer/Project Modal -->
    <project-create-form v-if="showProjectModal" @update:is-active="val => showProjectModal = val" @disableProjectForm="showProjectModal=false"></project-create-form>
    <!-- Task Modal -->
    <new-task-form v-if='showTaskModal' @disableTaskForm="showTaskModal=false"></new-task-form>
  </div>
</template>

<script>
import ModalComponent from "@components/common/modal.vue";
import ProjectCreateForm from "@components/project-lists/project-create-form.vue";
import newTaskForm from "@components/my-tasks/new-task.vue";

export default {
  components: { newTaskForm, ModalComponent, ProjectCreateForm },
  data() {
    return {
      showProjectModal: false,
      showTaskModal: false
    };
  }
};
</script>
