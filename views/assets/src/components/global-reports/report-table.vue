<script>

export default {
  props: {
    rows: {
      type: Array,
      required: true
    },
    label: {
      type: String,
      required: true
    },
    totals: {
      type: Boolean,
      default: false
    },
    filter: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      index: 0
    }
  },
  computed: {
    filteredRows() {
      if (!this.filter || this.filter.length === 0) {
        return this.rows;
      }
      const filter = this.filter.toLowerCase();
      return this.rows.filter(row => {
        for (let col of Object.values(row)) {
          if (col && col.toString().toLowerCase().includes(filter)) {
            return true;
          }
        }
        return false;
      });
    }
  },
  methods: {
    toNormalCase(str) {
      if (!str || str.length === 0) {
        return '';
      }
      return str
      .split(/[-_ ]+/)
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
    }
  },
  created() {
    console.log("rows: ", this.rows);
  }
}
</script>

<template>
  <table v-if="rows.length > 0 && rows[0]" class="table table-hover" aria-describedby="table to show report">
    <thead class="sticky-top bg-light">
    <tr>
      <th scope="col">{{ toNormalCase(label) }}</th>
      <th scope="col" v-for="field in rows[0] ? Object.keys(rows[0]) : []" :key="field">{{ toNormalCase(field) }}</th>
    </tr>
    </thead>
    <tbody>
    <tr v-for="row in filteredRows" v-if="row">
      <td></td>
      <td v-for="(col, i) in Object.values(row)" class="text-truncate" style="max-width: 250px;" :key="`${col}${i}`">
        {{ isNaN(col) ? col : `${Math.round(col * 100) / 100}` }}
      </td>
    </tr>
    </tbody>
  </table>
</template>
