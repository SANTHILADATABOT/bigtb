<script>
import GKMixins from "@components/global-kanban/mixin";
export default {
  name: "project-tile",
  props: {
    // the project object should really have everything you need
    // id, title, description, updated_at, created_at, etc.
    project: {
      type: Object,
      required: true
    },
    board_id: {
      type: Number,
      required: true
    }
  },
  mixins: [GKMixins],
  methods: {
    removeProj() {
      // removes the project from the board (meaning, the column)
      this.removeProjectBoardable(this.board_id, this.project.id);
    }
  },
  computed: {
    truncatedDescription() {
      if (this.project.description.content.length > 80) {
        return this.project.description.content.substring(0, 80) + '...';
      }
      return this.project.description.content;
    }
  },
  data () {
    return {
      id: "project_" + this.project.id,
    }
  }
}
</script>

<template>
  <b-card bg-variant="primary" text-variant="white" :title="project.title">
    <b-card-text v-if="project.description">{{ truncatedDescription }}</b-card-text>
    <router-link :to="{ name: 'task_lists', params: { project_id: project.id } }" tag="button" class="btn btn-light">See Project</router-link>
    <b-button variant="danger" @click="removeProj"><i class="fa fa-trash"></i></b-button>
  </b-card>
</template>
