<script>
import KanbanColumn from "@components/global-kanban/kanban-column.vue";
import ProjectNewProjectBtn from "@components/project-lists/project-new-project-btn.vue";
import KanbanMixins from "@components/global-kanban/mixin";
import AddNewColumn from "@components/global-kanban/add-new-column.vue";
import Draggable from "vuedraggable";

export default {
  components: {
    AddNewColumn,
    Draggable,
    KanbanColumn,
    "new-project-task-btns" : ProjectNewProjectBtn
  },
  computed: {
    gkColumns() {
      return [...this.$store.state.globalKanban_columns];
    },
    dragOptions() {
      return {
        animation: 0,
        group: "columns",
        sort: true,
        disabled: !this.editable,
        ghostClass: "ghost",
        handle: ".column-drag-handle"
      }
    }
  },
  mixins: [KanbanMixins],
  methods: {
    toggleFullScreen() {
      if (!document.fullscreenElement) {
        const kanboard = document.getElementById('gk-main');
        kanboard.requestFullscreen().catch(err => {
          alert(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
        });
      } else {
        document.exitFullscreen();
      }
    },
    moveColumn(evt) {
      this.$store.commit('setGK_columns', this.gkColumns);
      this.updateBoardOrder(this.gkColumns);
    },
    allowMove() {
      return true;
    },
  },
  data() {
    return {
      editable: true
    }
  },
  created () {
    const pArgs = {
      status: 0,
      per_page: 0
    };

    this.getProjects(pArgs);
    this.getGlobalKanban();
  }
}
</script>

<template>
  <div class="pm-wrap pm pm-kanboard">
    <!---------------------------- Menu Buttons/Options ------------------>
    <b-container fluid class="mt-4">
      <b-row>
        <b-col class="d-flex justify-content-start align-items-center">
          <new-project-task-btns></new-project-task-btns>
        </b-col>
        <b-col class="d-flex justify-content-end align-items-center">
          <add-new-column />
          <b-button variant="primary" class="fullscreen-view-btn" @click="toggleFullScreen">
            <span class="icon-pm-fullscreen"></span>
            <span class="icon-pm-fullscreen-text">{{ __( ' Fullscreen', 'wedevs-project-manager' ) }}</span>
          </b-button>
        </b-col>
      </b-row>
    </b-container>
    <!---------------------------- The Board Itself ------------------>
    <div id="gk-main" class="pm-kanboard-fullscreen pm-project-module-page">
      <div class="kanban-container">
        <div class="kanban-row">
          <div v-for="board in gkColumns" :key="board.id" class="kanban-column">
            <kanban-column :title="board.title" :allProjects="$store.state.projects" :id="board.id"></kanban-column>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="less" scoped>
.kanban-row {
  align-items: stretch;
  display: flex;
  flex-direction: row;
  height: 100%;
}

.kanban-column {
  border: 3px solid #007bff; /* Bootstrap primary color */
  display: inline-block;
  margin: 10px 10px 0 10px;
  min-width: 400px;
  width: 400px;
}

.kanban-container {
  height: 100%;
  overflow-x: auto;
  white-space: nowrap;

  .pm-kanboard-fullscreen {
    position: relative;
    background: #ffffff; /* Set background to white */
  }

  .pm-kanboard-fullscreen-active {
    background: #f1f1f1;
    padding: 22px 15px 15px 22px;
  }

  .fullscreen-view-btn {
    display: inline-flex;
    height: 30px;
    font-size: 12px;
    white-space: nowrap;
    align-items: center;
    background: #fff;
    border: 1px solid #E2E2E2;
    border-radius: 3px 0 0 3px;
    color: #788383;
    border-right-color: #fff;
    padding: 0 12px;
    margin-right: 10px;
  }

  .kanboard-menu-wrap {
    padding: 10px 0;
    display: block;
    overflow: hidden;

    .fullscreen-view-btn {
      .icon-pm-fullscreen {
        &:before {
          vertical-align: middle;
        }
      }
      .icon-pm-fullscreen-text {
        margin-left: 8px;
        font-size: 12px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
        font-weight: 600;
      }

      &:hover {
        .icon-pm-fullscreen, .icon-pm-fullscreen-text {
          color: #1A9ED4 !important;
        }

        border-color: #1A9ED4;
      }
    }
  }
}
</style>
