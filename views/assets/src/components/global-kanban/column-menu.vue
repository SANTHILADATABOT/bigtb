<script>
import CuteMenu from "@components/global-kanban/cute-menu.vue";
import GKMixins from "@components/global-kanban/mixin";

export default {
  name: "column-menu",
  components: {CuteMenu},
  mixins: [GKMixins],
  props: {
    board_id: {
      type: Number,
      required: true
    }
  },
  methods: {
    deleteColumn() {
      this.deleteBoard(this.board_id);
    }
  },
}
</script>

<template>
  <cute-menu>
    <button href="#">Edit</button>
    <button @click="deleteColumn">Delete</button>
  </cute-menu>
</template>

<style scoped lang="less">

</style>
