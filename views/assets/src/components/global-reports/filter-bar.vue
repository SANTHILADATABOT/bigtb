<script>
export default {
  props: {
    value: {
      type: String,
      default: ''
    }
  },
  methods: {
    updateFilter(event) {
      this.$emit('input', event.target.value);
    }
  }
}
</script>

<template>
  <div class="mb-3" style="max-width: 500px;">
    <input type="text" :value="value" @input="updateFilter" class="form-control form-control-lg" placeholder="Filter rows by.." aria-label="Recipient's username" aria-describedby="filter button">
  </div>
</template>

<style scoped lang="less">

</style>
