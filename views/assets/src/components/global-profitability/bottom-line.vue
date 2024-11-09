<script>
export default {
  name: "bottom-line",
  props: {
    invoices: Array,
    vendors: Array
  },
  computed: {
    totalCosts() {
      const materialCosts = this.vendors.reduce((acc, vendor) => acc + vendor.orders.reduce((acc, o) => acc + parseInt(o.cost), 0), 0);
      const laborCosts = this.invoices.reduce((acc, invoice) => acc + this.getLaborCost(invoice), 0);
      return materialCosts + laborCosts;
    },
    totalRevenue() {
      return this.invoices.reduce((acc, invoice) => acc + parseInt(invoice.paid_amount), 0);
    },
    totalProfit() {
      return this.totalRevenue - this.totalCosts;
    },
    percentProfit() {
      return ((this.totalProfit / this.totalCosts) * 100).toFixed(1);
    }
  },
  methods: {
    getLaborCost(invoice) {
      return invoice.entryTasks.reduce((acc, task) => acc + (task.amount * task.hour), 0);
    }
  }
}
</script>

<template>
  <div class="bl-table-container">
    <h3>Bottom Line</h3>
    <table class="bl-table">
      <tbody>
        <tr>
          <th>Total Costs</th>
          <td>${{ totalCosts }}</td>
        </tr>
        <tr>
          <th>Revenue</th>
          <td>${{ totalRevenue }}</td>
        </tr>
        <tr>
          <th>Profit</th>
          <td>${{ totalProfit }}</td>
        </tr>
        <tr>
          <th>Profit %</th>
          <td>{{ percentProfit }}%</td>
        </tr>
      </tbody>
    </table>
  </div>

</template>

<style scoped lang="less">
.bl-table-container {
  font-family: Arial, sans-serif;
}

.bl-table {
  width: 62%;
  border-collapse: collapse;
}

.bl-table th, .bl-table td {
  border: 1px solid #ddd;
  text-align: left;
  padding: 8px;
}

.bl-table th {
  background-color: #f8f8f8;
}

.bl-table tbody tr:nth-child(odd) {
  background-color: #f2f2f2;
}

.bl-table tbody tr:nth-child(even) {
  background-color: #fff;
}

.bl-table tbody tr:hover {
  background-color: #ddd;
}
</style>

