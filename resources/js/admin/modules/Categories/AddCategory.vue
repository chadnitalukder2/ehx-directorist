<template>
    <div class="ehxd_form_wrapper">
      <el-form
        ref="categoryForm"
        :model="localCategory"
        :rules="rules"
        label-position="top"
        @submit.prevent
      >
        <el-form-item label="Category Name *" prop="name">
          <el-input
            class="ehxd_input"
            v-model="localCategory.name"
            placeholder="Please Input Category Name"
            size="large"
          />
        </el-form-item>
  
        <el-form-item label="Category Slug *" prop="slug">
          <el-input
            class="ehxd_input"
            v-model="localCategory.slug"
            placeholder="Please Input Category Slug"
            size="large"
          />
        </el-form-item>
  
        <el-form-item label="Description">
          <el-input
            class="ehxd_input"
            v-model="localCategory.description"
            placeholder="Please Input Description"
            size="large"
            type="textarea" row="5"
          />
        </el-form-item>
  
        <el-form-item>
          <el-button   type="primary" size="large" @click="submitCategoryForm">
            {{ localCategory.id ? "Update Category" : "Save Category" }} 
        
          </el-button>
        </el-form-item>
      </el-form>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    name: "AddCategory",
    props: {
      categories_data: {
        type: Object,
        default: () => ({}),
      },
    },
    data() {
      return {
        localCategory: {
          name: "",
          slug: "",
          description: "",
        },
        rules: {
          name: [
            { required: true, message: "Category name is required", trigger: "blur" },
          ],
          slug: [
            { required: true, message: "Slug is required", trigger: "blur" },
          ],
        },
        rest_api: window.EhxDirectoristData.rest_api,
        nonce: window.EhxDirectoristData.nonce,
      };
    },
    watch: {
      categories_data: {
        immediate: true,
        deep: true,
        handler(val) {
          this.localCategory = { ...val }; // clone to avoid mutating parent directly
        },
      },
    },
    methods: {
      async submitCategoryForm() {
        this.$refs.categoryForm.validate(async (valid) => {
          if (!valid) return;
  
          const isUpdate = !!this.localCategory.id;
          const url = isUpdate
            ? `${this.rest_api}/updateCategory/${this.localCategory.id}`
            : `${this.rest_api}/postCategory`;
  
          try {
            const response = await axios.post(url, this.localCategory, {
              headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": this.nonce,
              },
            });
  
            this.$notify({
              title: "Success",
              message: `Category ${isUpdate ? "updated" : "created"} successfully`,
              type: "success",
            });
  
            if (!isUpdate) {
              this.$emit("updateDataAfterNewAdd", response.data.category);
              this.localCategory = {
                name: "",
                slug: "",
                description: "",
              };
            } else {
              this.$emit("updateDataAfterNewAdd", response.data.category);
            }
          } catch (error) {
            console.error("Error saving category:", error);
          }
        });
      },
    },
  };
  </script>
  
  <style scoped>
  .ehxd_form_wrapper {
    padding: 20px;
  }
  .ehxd_input {
    width: 100%;
  }
  </style>
  