<template>
  <div class="ehxd_form_wrapper">
    <el-form ref="ListForm" :model="localList" :rules="rules" label-position="top" @submit.prevent>
      <el-form-item label="Listing Name" prop="name">
        <el-input v-model="localList.name" placeholder="Enter Listing name" />
      </el-form-item>

      <el-form-item label="Slug" prop="slug">
        <el-input v-model="localList.slug" placeholder="Enter slug" />
      </el-form-item>

      <el-form-item label="Email" prop="email">
        <el-input v-model="localList.email" placeholder="Enter email" />
      </el-form-item>

      <el-form-item label="Phone" prop="phone">
        <el-input v-model="localList.phone" placeholder="Enter phone number" />
      </el-form-item>

      <el-form-item label="Address" prop="address">
        <!-- <el-input v-model="localList.address" placeholder="Enter address" /> -->
         <GoogleMap />
      </el-form-item>

      <el-form-item label="Website Url" prop="website_url">
        <PairInput :pair="localList.website_url" />
      </el-form-item>

      <el-form-item label="Social Link" prop="social_links">
        <PairInput :pair="localList.social_links" />
      </el-form-item>

      <el-form-item label="Longitude" prop="longitude">
        <el-input-number v-model="localList.longitude" :step="0.000001" controls-position="right" />
      </el-form-item>

      <el-form-item label="Short Description " prop="short_description">
        <el-input type="textarea" v-model="localList.short_description" />
      </el-form-item>

      <el-form-item>
        <el-button type="primary" size="large" @click="submitListForm">
          {{ localList.id ? "Update List" : "Save List" }}

        </el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import axios from "axios";
import GoogleMap from '../../components/GoogleMap.vue';
import PairInput from '../../components/PairInput.vue';
import AppFileUpload from '../../components/AppFileUpload.vue';
import WpEditor from "../../components/WpEditor.vue";
export default {
  name: "AddList",
  components: {
        AppFileUpload,
        PairInput,
        WpEditor,
        GoogleMap
    },
  props: {
    listings_data: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      localList: {
        name: '',
        slug: '',
        email: '',
        phone: '',
        website_url: [{ name: "small" , value: "200"}, { name: "large" , value: "400"}],
        social_links:[{ name: "Cheese" , value: "50"}],
        address: '',
        postal_code: '',
        latitude: null,
        longitude: null,
        logo: null,
        short_description: '',
        description: '',
        meta: {},
        directory_builder_id: 1,
        category_id: 0,
        tag_id: 0,

      },
      rules: {
        name: [{ required: true, message: "Listing name is required", trigger: "blur" }],
        slug: [{ required: true, message: "Slug is required", trigger: "blur" }],
        email: [
          { required: true, message: "Email is required", trigger: "blur" },
          { type: "email", message: "Invalid email", trigger: "blur" }
        ],
        phone: [{ required: true, message: "Phone number is required", trigger: "blur" }],
        address: [{ required: true, message: "Address is required", trigger: "blur" }],
        short_description: [{ required: true, message: "Short description is required", trigger: "blur" }],
      },
      rest_api: window.EhxDirectoristData.rest_api,
      nonce: window.EhxDirectoristData.nonce,
    };
  },
  watch: {
    listings_data: {
      immediate: true,
      deep: true,
      handler(val) {
        this.localList = { ...val };
      },
    },
  },
  methods: {
    async submitListForm() {
      this.$refs.ListForm.validate(async (valid) => {
        if (!valid) return;

        const isUpdate = !!this.localList.id;
        const url = isUpdate
          ? `${this.rest_api}/updateList/${this.localList.id}`
          : `${this.rest_api}/postList`;

        try {
          const response = await axios.post(url, this.localList, {
            headers: {
              "Content-Type": "application/json",
              "X-WP-Nonce": this.nonce,
            },
          });

          this.$notify({
            title: "Success",
            message: `List ${isUpdate ? "updated" : "created"} successfully`,
            type: "success",
          });

          if (!isUpdate) {
            this.$emit("updateDataAfterNewAdd", response.data.List);
            this.localList = {
              name: '',
              slug: '',
              email: '',
              phone: '',
              website_url: {},
              social_links: {},
              address: '',
              postal_code: '',
              latitude: null,
              longitude: null,
              logo: null,
              short_description: '',
              description: '',
              meta: {},
              directory_builder_id: 1,
              category_id: 0,
              tag_id: 0,

            };
          } else {
            this.$emit("updateDataAfterNewAdd", response.data.List);
          }
        } catch (error) {
          console.error("Error saving List:", error);
        }
      });
    },

  },
};
</script>

<style scoped>
.ehxd_form_wrapper {
  /* padding: 20px; */
}

.ehxd_input {
  width: 100%;
}
</style>