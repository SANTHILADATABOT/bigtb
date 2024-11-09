<script>
import MaterialsMixin from "@components/global-materials/mixin.js";

export default {
  name: "new-order",
  mixins: [MaterialsMixin],
  methods: {
    recordOrder(event) {
      event.preventDefault();
      console.log("associated projects: ", this.associated_projects);
      const newOrder = {
        title: this.title,
        vendor_id: this.vendor,
        description: this.description,
        cost: this.cost,
        date: this.date,
        ordered_by: this.ordered_by,
        associated_projects: this.associated_projects
      }
      this.addMaterialOrder(newOrder);
      this.clearForm();
      this.$bvModal.hide('new-order-modal')
    },
    clearForm() {
      this.title = "";
      this.vendor = null;
      this.description = "";
      this.cost = "";
      this.date = new Intl.DateTimeFormat('en-CA', { year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date());
      this.ordered_by = null;
      this.associated_projects = [];
      this.isFormVisible = false; // hide the form after submission
    },
  },
  computed: {
    vendors() { return this.$store.state.materialVendors },
    users() {
      return this.$store.state.users;
    },
    currentUser() { return this.$store.state.currentUser },
    projects() { return this.$store.state.projects },
    vendorOptions() {
      return this.vendors.map(v => ({ value: v.id, text: v.name }));
    },
    orderedByOptions() {
      return this.users.map(u => ({ value: u.id, text: u.display_name }));
    },
    associatedProjectsOptions() {
      return this.projects.map(p => ({ value: p.id, text: p.title }));
    }
  },
  watch: {
    currentUser() {
      this.ordered_by = this.currentUser.id;
    }
  },
  data () {
    return {
      title: "",
      vendor: null,
      description: "",
      cost: "",
      date: new Intl.DateTimeFormat('en-CA', { year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date()),
      ordered_by: null,
      associated_projects: [],
      isFormVisible: false
    }
  },
  created() {
    this.getCurrentUser();
  }
}
</script>

<template>
  <div id="add-new-mo">
    <b-button variant="primary" @click="$bvModal.show('new-order-modal')">Add Order</b-button>
    <b-modal id="new-order-modal" centered title="Add an Order to a Vendor" hide-footer @cancel="clearForm" @close="clearForm">
      <form id="new-order-form" @submit="recordOrder">
        <!-------------------------- Select a vendor ---------------------->
        <label for="vendor">Vendor</label>
        <b-form-select id="vendor" class="form-select-override" v-model="vendor" :options="vendorOptions" required>
          <template #first>
            <b-form-select-option :value="null" disabled>Please select a vendor</b-form-select-option>
          </template>
        </b-form-select>
        <!-------------------------------- Title ---------------------------->
        <label for="title">Title</label>
        <b-form-input type="text" name="title" placeholder="Subject for the order" v-model="title" />
        <!---------------------------- Description ------------------------>
        <label for="description">Description</label>
        <b-form-textarea type="text" name="description" placeholder="Describe your order here" v-model="description"></b-form-textarea>
        <!------------------------------- Cost ---------------------------->
        <label for="cost">Cost</label>
        <b-form-input type="text" name="cost" v-model="cost" required />
        <!------------------------------- Date ---------------------------->
        <label for="date">Date</label>
        <b-form-input type="date" name="date" v-model="date" required />
        <!----------------------- Who made the order ------------------>
        <label for="orderedBy">Ordered By</label>
        <b-form-select id="vendor" class="form-select-override" v-model="ordered_by" :options="orderedByOptions" required></b-form-select>
        --------------------- Who made the order ----------------
        <label for="assocProjects">Associated Projects</label>
        <b-form-select id="vendor" class="form-select-override" v-model="associated_projects" :options="associatedProjectsOptions" :select-size="4" required></b-form-select>
        <!------------------------------ Submit --------------------------->
        <b-button type="submit" variant="primary">Submit</b-button>
      </form>
    </b-modal>
  </div>
</template>

<style scoped lang="less">
  .form-select-override {
    max-width: none !important;
  }
  #add-new-mo {
    margin: 5px;
  }
  #new-order-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
</style>
