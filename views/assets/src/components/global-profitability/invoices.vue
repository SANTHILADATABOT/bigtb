<script>
export default {
  name: "invoice",
  props: {
    invoices: Array
  },
  computed: {
    totalCosts() {
      return this.invoices.reduce((acc, invoice) => acc + this.getLaborCost(invoice), 0);
    },
    totalDue() {
      return this.invoices.reduce((acc, invoice) => acc + invoice.due_amount, 0);
    },
    totalPaid() {
      return this.invoices.reduce((acc, invoice) => acc + invoice.paid_amount, 0);
    },
    totalInvoice() {
      return this.invoices.reduce((acc, invoice) => acc + invoice.invoice_total, 0);
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
  <div class="invoice-table-container">
    <h3>All Invoices</h3>
    <table class="invoice-table" aria-describedby="Table of all invoices">
      <thead>
      <tr>
        <th>Title</th>
        <th>Created</th>
        <th>Due</th>
        <th>Status</th>
        <th>Discount</th>
        <th>Labor Cost</th>
        <th>Due</th>
        <th>Total</th>
        <th>Paid</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="i in invoices" :key="i.id">
        <td>
          <router-link :to="`/projects/${i.project_id}/invoice/${i.id}`">{{ i.title }}</router-link>
        </td>
        <td>{{ i.start_at.date }}</td>
        <td>{{ i.due_date.date }}</td>
        <td>{{ i.status }}</td>
        <td>{{ i.discount }}</td>
        <td>${{ getLaborCost(i) }}</td>
        <td>${{ i.due_amount }}</td>
        <td>${{ i.invoice_total }}</td>
        <td>${{ i.paid_amount }}</td>
      </tr>
      <tr>
        <td colspan="5">
          <b>Totals</b>
        </td>
        <td>${{ totalCosts }}</td>
        <td>${{ totalDue }}</td>
        <td>${{ totalInvoice }}</td>
        <td>${{ totalPaid }}</td>
      </tr>
      </tbody>
    </table>
  </div>
</template>


<style scoped>
.invoice-table-container {
  font-family: Arial, sans-serif;
}

.invoice-table {
  width: 100%;
  border-collapse: collapse;
}

.invoice-table th, .invoice-table td {
  border: 1px solid #ddd;
  text-align: left;
  padding: 8px;
}

.invoice-table th {
  background-color: #f8f8f8;
}

.invoice-table tbody tr:nth-child(odd) {
  background-color: #f2f2f2;
}

.invoice-table tbody tr:nth-child(even) {
  background-color: #fff;
}

.invoice-table tbody tr:hover {
  background-color: #ddd;
}

.action-btn {
  border: none;
  background: none;
  cursor: pointer;
}

.edit {
  color: #007bff;
}

.delete {
  color: #ff0000;
}

/* Additional styling for responsive behavior and other visual tweaks can be added here */
</style>

