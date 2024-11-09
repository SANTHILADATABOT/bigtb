<script>
import GKMixins from "@components/global-kanban/mixin";
export default {
  name: "AddNewColumn",
  mixins: [GKMixins],
  methods: {
    addColumn() {
      console.log(this.title);
      this.addBoard(this.title);
      this.title = "";
    }
  },
  data() {
    return {
      title: "",
    }
  }
}
</script>

<template>
  <input class="mr-2" type="text" placeholder="Add New Column" required="required" @keyup.enter="addColumn" v-model="title">
</template>
