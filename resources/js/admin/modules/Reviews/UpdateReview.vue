<template>
    <div class="ehxd_form_wrapper">
      <el-form
        ref="ReviewForm"
        :model="localReview"
        :rules="rules"
        label-position="top"
        @submit.prevent
      >
        <el-form-item label="Status" prop="status">
            <el-select class="erm_input" v-model="localReview.status" placeholder="Select Status" size="large"
              style="width: 100%">
              <el-option value="pending">Pending</el-option>
              <el-option value="complete">Complete</el-option>
              <el-option value="cancel">Cancel</el-option>
            </el-select>
        </el-form-item>
  
        <el-form-item style="margin-bottom: 0px !important; ">
          <el-button   type="primary" size="large" @click="submitReviewForm">
            {{ localReview.id ? "Update Review" : "Save Review" }} 
        
          </el-button>
        </el-form-item>
      </el-form>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    name: "UpdateReview",
    props: {
      reviews_data: {
        type: Object,
        default: () => ({}),
      },
    },
    data() {
      return {
        localReview: {
          status: "",
        },
        rest_api: window.EhxDirectoristData.rest_api,
        nonce: window.EhxDirectoristData.nonce,
      };
    },
    watch: {
        reviews_data: {
        immediate: true,
        deep: true,
        handler(val) {
          this.localReview = { ...val }; // clone to avoid mutating parent directly
        },
      },
    },
    methods: {
      async submitReviewForm() {
        this.$refs.ReviewForm.validate(async (valid) => {
          if (!valid) return;
  
          const isUpdate = !!this.localReview.id;
          const url = isUpdate
            ? `${this.rest_api}/updateReview/${this.localReview.id}`
            : `${this.rest_api}/postReview`;
  
          try {
            const response = await axios.post(url, this.localReview, {
              headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": this.nonce,
              },
            });
  
            this.$notify({
              title: "Success",
              message: `Review data ${isUpdate ? "updated" : "created"} successfully`,
              type: "success",
            });
  
            if (!isUpdate) {
              this.$emit("updateDataAfterNewAdd", response.data.Review);
              this.localReview = {
                name: "",
                slug: "",
                description: "",
              };
            } else {
              this.$emit("updateDataAfterNewAdd", response.data.Review);
            }
          } catch (error) {
            console.error("Error saving Review:", error);
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
  