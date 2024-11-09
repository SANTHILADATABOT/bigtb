<script>
import Draggable from "vuedraggable";
import ProjectNewProjectBtn from "@components/project-lists/project-new-project-btn.vue";
import CuteMenu from "@components/global-kanban/cute-menu.vue";
import ProjectTile from "@components/global-kanban/project-tile.vue";
import rootMixins from "@helpers/mixin/mixin.js";
import GKMixins from "@components/global-kanban/mixin";
import ColumnMenu from "@components/global-kanban/column-menu.vue";

export default {
  name: "kanban-column",
  props: {
    title: {
      type: String,
      required: true
    },
    allProjects: {
      type: Array,
      required: false,
      default: () => []
    },
    id: {
      type: Number,
      required: true
    }
  },
  mixins: [rootMixins, GKMixins],
  components: {
    ColumnMenu,
    ProjectTile,
    CuteMenu,
    Draggable,
    'new-project-btn' : ProjectNewProjectBtn,
    'multiselect' : pm.Multiselect.Multiselect
  },
  methods: {
    toggleDropdown(dropdownKey) {
      this.dropdowns[dropdownKey] = !this.dropdowns[dropdownKey];
      // Make sure only one is ever opened
      for (let key in this.dropdowns) {
        if (key !== dropdownKey) {
          this.dropdowns[key] = false;
        }
      }
    },
    moveProject(evt) {
      let fromBoardId = evt.from.id.split("_")[1];
      let toBoardId = evt.to.id.split("_")[1];
      let projectId = evt.item.id.split("_")[1];
      this.updateProjectBoardable(fromBoardId, projectId, toBoardId);
    },
    addProject(project) {
      this.addProjectBoardable(this.id, project);
      this.dropdowns.addProjMenu = false;
    },
    deleteColumn() {
      this.deleteBoard(this.id);
    },
    allowMove() {
      // this exclusively approves the move - doesn't even console.log besides that
      return true;
    }
  },

  created() {
    this.getProjectBoardables(this.id);
  },
  data() {
    return {
      dropdowns: {
        addProjMenu: false,
        columnMenu: false
      },
      board_id: "board_" + this.id,
      editable: true
    };
  },
  computed: {
    colProjects() {
      return this.$store.state.globalKanban_boardables[this.id] || [];
    },
    dragOptions() {
      return {
        animation: 0,
        group: "description",
        disabled: !this.editable,
        ghostClass: "ghost"
      }
    }
  }
}
</script>

<template>
  <b-container class="mt-4" :data-section_id='id'>
    <!---------------------------Header of the Column-------------------------->
    <b-row>
      <b-col class="d-flex justify-content-start align-items-center text-truncate" cols="8">
        <h5>{{ title }}</h5>
      </b-col>
      <b-col class="d-flex justify-content-end align-items-center" cols="4">
        <!---------------------------Column Buttons----------------->
        <div class="trigger" style="display: inline-block;">
          <!------------Add Project Button------>
          <b-button variant="primary" id="gk-add-project-btn" @click="toggleDropdown('addProjMenu')">
            <span><i aria-hidden="true" class="fa fa-plus"></i></span>
          </b-button>
          <!------------Options Button------>
          <b-button variant="primary" id="gk-col-options-btn" @click="deleteColumn">
            <span><i aria-hidden="true" class="fa fa-trash"></i></span>
          </b-button>
          <!------------Add Project Dropdown ------->
          <div v-show="dropdowns.addProjMenu" class="dropdown-content">
            <multiselect class="gk-add-proj" ref="select" :options="allProjects" label="title" @input="addProject"></multiselect>
          </div>
        </div>
      </b-col>
    </b-row>
    <!---------------------------Projects in the Column-------------------------->
    <div class="kbc-tasks-wrap">
      <draggable :move="allowMove" class="gk-col-drag-area" :id="board_id" v-bind="dragOptions" @end="moveProject">
        <project-tile v-for="project in colProjects" :key="`project_${project.id}`" :project="project" :board_id="id"></project-tile>
      </draggable>
    </div>
    <div class="kbc-section-footer"></div>
  </b-container>
</template>

<style scoped lang="less">
.gk-col-drag-area {
  height: 100%;
  width: 100%;
}
.gk-add-proj {
  width: 14rem;
  position: absolute;
  top: 100%; /* Position just below the container */
  left: 0;
  z-index: 1000;
}
.kbc-section-header-wrap {
  height: 50px;
  background: #fbfcfd;
  border-bottom: 1px solid #eff1f7;
}
.kbc-section-header {
  max-height: 52px;
  overflow-y: auto;
  width: 70%;
  display: inline-block;
  padding: 15px 0 15px 14px;
}

.kbc-section-header {
  float: left;
}

.kbc-section-action {
  vertical-align: top;
  text-align: right;
  position: relative;
  float: right;
  padding: 15px 10px 15px 0;
}
.kbc-action-icon-wrap {
  position: relative;
}
.kbc-tasks-wrap {
  height: 54vh;
}
/* Kanban styles end */
</style>
