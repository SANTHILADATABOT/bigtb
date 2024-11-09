<script>
import MaterialsMixin from "@components/global-materials/mixin.js";
export default {
  name: "new-vendor",
  mixins: [MaterialsMixin],
  data () {
    return {
      name: "",
      description: "",
      phone: "",
      email: "",
      address: "",
      isFormVisible: false // new data property
    }
  },
  methods: {
    addVendor(event) {
      event.preventDefault();
      const newVendor = {
        name: this.name,
        description: this.description,
        phone: this.phone,
        email: this.email,
        address: this.address
      }
      this.addMaterialVendor(newVendor);
      this.clearFields();
    },
    clearFields() {
      this.name = '';
      this.description = '';
      this.phone = '';
      this.email = '';
      this.address = '';
      this.isFormVisible = false; // hide the form after submission
    },
  }
}
</script>

<template>
  <div id="add-new-mv">
    <b-button variant="primary" @click="$bvModal.show('new-vendor-modal')">Add Vendor</b-button>
    <b-modal id="new-vendor-modal" centered title="Add a New Vendor" hide-footer>
      <form id="new-vendor-form" @submit="addVendor">
        <!---------------------------------- Name -------------------------------->
        <label for="name">Name</label>
        <b-form-input type="text" name="name" v-model="name" placeholder="Vendor Name Here" required />
        <!------------------------------- Description ---------------------------->
        <label for="description">Description</label>
        <b-form-textarea type="text" name="description" v-model="description" placeholder="A simple description of the vendor and their wares"></b-form-textarea>
        <!--------------------------------- Phone -------------------------------->
        <label for="phone">Phone</label>
        <b-form-input type="number" name="phone" v-model="phone" placeholder="000-000-0000" required />
        <!---------------------------------- Email ------------------------------->
        <label for="email">Email</label>
        <b-form-input type="email" name="email" v-model="email" placeholder="contact@vendor.com" />
        <!-------------------------------- Address ------------------------------->
        <label for="address">Address</label>
        <b-form-input type="text" name="address" v-model="address" placeholder="Physical Address" required />
        <!--------------------------------- Submit & Cancel ---------------------->
        <b-button type="submit" variant="primary" @click="$bvModal.hide('new-vendor-modal')">Submit</b-button>
        <b-button type="button" variant="danger" @click="clearFields(); $bvModal.hide('new-vendor-modal')">Cancel</b-button>
      </form>
    </b-modal>
  </div>
</template>

<style scoped lang="less">
  #add-new-mv {
    margin: 5px;
  }
  #new-vendor-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
</style>
