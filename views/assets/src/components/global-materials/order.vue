<script>
import "@assets/css/variables.css";
import MaterialsMixin from "@components/global-materials/mixin.js";
export default {
  name: "order.vue",
  mixins: [MaterialsMixin],
  props: {
    order: {
      type: Object,
      required: true,
      validator: function (order) {
        return order.hasOwnProperty('title') &&
                order.hasOwnProperty('cost') &&
                order.hasOwnProperty('date') &&
                order.hasOwnProperty('ordered_by');
      }
    },
  },
  methods: {
    deleteOrder() {
      this.deleteMaterialOrder(this.order.id);
      this.deleted = true;
    }
  },
  computed: {
    orderedBy() {
      const orderingUser = this.$store.state.users.find(user => parseInt(user.id) === parseInt(this.order.ordered_by));
      return orderingUser || {display_name: "loading..."};
    }
  },
  data() {
    return {
      deleted: false
    }
  }
}
</script>

<template>
  <b-col cols="4">
    <b-card class="text-dark" v-if="!deleted" :title="order.title" :sub-title="order.description">
      <b-card-text>Cost: ${{ order.cost }}</b-card-text>
      <b-card-text>Ordered by {{ orderedBy.display_name }} on {{ order.date }}</b-card-text>
      <b-card-text v-if="order.projects.length === 1">Associated with {{ order.projects[0].title }}</b-card-text>
      <div v-if="order.projects.length > 1">
        <b-card-text>Associated with:</b-card-text>
        <ul>
          <li v-for="p in order.projects">{{ p.title }}</li>
        </ul>
      </div>
      <b-card-text>Ordered by {{ orderedBy.display_name }} on {{ order.date }}</b-card-text>
      <b-button variant="danger" @click="deleteOrder"><i class="fa fa-trash text-white"></i></b-button>
    </b-card>
  </b-col>
</template>

<style scoped lang="less">
.mo-order {
  border: 1px solid;
  width: 100%;
  min-height: 80px;
  padding: 10px;
  margin: 10px 0;
  text-indent: 0;
}
.mo-top-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  height: 1.3rem;
  margin: 0;
  padding: 0 10px;
}
.title-and-button {
  display: flex;
  align-items: center;
}
.mo-title {
  display: inline;
  color: black;
  font-size: 1rem;
}

button {
  border: none;
  color: black;
  margin-left: 10px; /* Adjust as needed */
}
button:hover {
  cursor: pointer;
  color: var(--red-accent);
}
.mo-description {
  display: inline-block;
  margin: 5px 10px 5px 10px;
  white-space: pre-line;
  text-indent: 0;
}
.mo-cost {
  display: inline;
  color: black;
  font-weight: bold;
  text-align: right;
}
.mo-assoc-proj {
  display: block;
  text-align: right;
  font-style: italic;
  margin: 0 10px 0 0 ;
}
.mo-stamp {
  display: block;
  text-align: right;
  font-style: italic;
  margin: 0 10px 0 0 ;
}
</style>
