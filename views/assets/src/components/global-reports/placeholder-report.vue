<script>
export default {
  props: {
    rows: Array,
    required: false
  },
  computed: {
  },
  methods: {
    capitalize(str) {
      return str.charAt(0).toUpperCase() + str.slice(1);
    }
  }
}
</script>

<template>
  <table class="table table-hover">
    <thead>
    <tr>
      <th scope="col">Budgets</th>
      <th scope="col" v-for="field in Object.keys(rows[0])" :key="field">{{ capitalize(field) }}</th>
    </tr>
    </thead>
    <tbody>
    <tr scope="row" v-for="row in rows">
      <td></td>
      <td v-for="field in Object.values(row)" :key="field">{{ field > 0 ? `$${field}` : '0.00' }}</td>
    </tr>
    <tr scope="row">
      <td>
        <b>Totals</b>
      </td>
      <td v-for="field in Object.keys(rows[0])" :key="field">${{ rows.reduce((acc, row) => acc + parseInt(row[field]), 0) }}</td>
    </tr>
    </tbody>
  </table>
</template>

<style scoped lang="less">

</style>
