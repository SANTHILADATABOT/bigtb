<script>
export default {
  name: "material-costs",
  props: {
    vendors: Array
  },
  methods: {
    getTotalExp(vendor) {
      return vendor.orders.reduce((acc, o) => acc + parseInt(o.cost), 0);
    }
  }
}
</script>

<template>
  <div class="mc-table-container">
    <h3>Material Costs by Vendor</h3>
    <table class="mc-table" aria-describedby="Table of costs in materials">
      <thead>
        <tr>
          <th>Vendor</th>
          <th>Total Expenditure</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="v in vendors">
          <td>{{ v.name }}</td>
          <td>${{ getTotalExp(v) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped lang="less">
.mc-table-container {
  font-family: Arial, sans-serif;
}

.mc-table {
  width: 62%;
  border-collapse: collapse;
}

.mc-table th, .mc-table td {
  border: 1px solid #ddd;
  text-align: left;
  padding: 8px;
}

.mc-table th {
  background-color: #f8f8f8;
}

.mc-table tbody tr:nth-child(odd) {
  background-color: #f2f2f2;
}

.mc-table tbody tr:nth-child(even) {
  background-color: #fff;
}

.mc-table tbody tr:hover {
  background-color: #ddd;
}
</style>
