<template>
  <div>
    <a href="#" id="pm-create-global-task" @click.prevent="is_popup_active()" class="pm-btn pm-btn-primary pm-btn-uppercase">
      <i class="pm-icon flaticon-plus" aria-hidden="true"></i>&nbsp;&nbsp;New Task
    </a>
  </div>
</template>

<script>
var global_task_btn = {
  methods: {
    is_popup_active () {
      jQuery('#pm-global-task-dialog').dialog( "open" );
      //this.$store.commit('is_popup_active', {is_active: true});
    }
  }
}

export default global_task_btn;
</script>
