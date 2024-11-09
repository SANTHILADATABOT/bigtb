<script>
import TRMixin from '@components/all-tasks/mixin.js';
import Task from '@components/all-tasks/task.vue';
export default {
  name: "all-tasks",
  mixins: [TRMixin],
  components: { Task },
  computed: {
    tasks() {
      return this.$store.state['allTasks'] || [];
    },
  },
  watch: {
    tasks() {
      this.tasks.length ? this.loading = false : this.loading = true;
    }
  },
  data() {
    return {
      loading : true
    }
  },
  created() {
    this.getAllTasks();
    this.getProjects();
  },
}
</script>

<template>
  <div>
    <h2>All Tasks, Sorted by Input Date</h2>
    <ul v-if="!loading">
      <task v-for="t in tasks" :key="t.id" :task="t">
      </task>
    </ul>
    <p v-if="loading">Looks like there's nothing to do</p>
  </div>
</template>

<style scoped lang="less">

</style>
