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
      validator: function (o) {
        return o.hasOwnProperty('title') &&
            o.hasOwnProperty('cost') &&
            o.hasOwnProperty('date') &&
            o.hasOwnProperty('ordered_by');
      }
    },
    vendor: {
      type: Object,
      required: true,
      validator: function (v) {
        return v.hasOwnProperty('name');
      }
    }
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
  <li v-if="!deleted">
    <div class="mo-order">
      <div class="mo-top-box">
        <div class="title-and-button">
          <b class="mo-title">{{ order.title }} - {{ vendor.name }}</b>
          <button @click="deleteOrder">
              <span>
                <i class="fa fa-trash"></i>
              </span>
          </button>
        </div>
        <p class="mo-cost">${{ order.cost }}</p>
      </div>
      <p class="mo-description">{{ order.description }}</p>
      <p v-if="order.projects.length === 1" class="mo-assoc-proj">Associated with {{ order.projects[0].title }}</p>
      <div v-if="order.projects.length > 1">
        <p class="mo-assoc-proj">Associated with:</p>
        <ul>
          <li v-for="p in order.projects" class="mo-assoc-proj">{{ p.title }}</li>
        </ul>
      </div>
      <p class="mo-stamp">Ordered by {{ orderedBy.display_name }} on {{ order.date }}</p>
    </div>
  </li>
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
