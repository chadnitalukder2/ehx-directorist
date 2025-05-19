<template>
  <div class="ehxd_form_wrapper">
    <el-form ref="categoryForm" :model="localCategory" :rules="rules" label-position="top" @submit.prevent>
      <el-form-item label="Category Name *" prop="name">
        <el-input class="ehxd_input" v-model="localCategory.name" placeholder="Please Input Category Name"
          size="large" />
      </el-form-item>

      <!-- <el-form-item label="Category Slug *" prop="slug"> -->
      <el-input type="hidden" class="ehxd_input" v-model="localCategory.slug" placeholder="Auto-generated from name"
        size="large" readonly />
      <!-- </el-form-item> -->

      <el-form-item label="Description">
        <el-input class="ehxd_input" v-model="localCategory.description" placeholder="Please Input Description"
          size="large" type="textarea" rows="5" />
      </el-form-item>

      <el-form-item style="margin-bottom: 0px !important; ">
        <el-button type="primary" size="large" @click="submitCategoryForm">
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
    'localCategory.name'(newName) {
      this.localCategory.slug = this.slugify(newName);
    },
  },
  methods: {
    slugify(text) {
      return text
        .toString()
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')        // Replace spaces with -
        .replace(/[^\w\-]+/g, '')    // Remove all non-word chars
        .replace(/\-\-+/g, '-')      // Replace multiple - with single -
        .replace(/^-+/, '')          // Trim - from start of text
        .replace(/-+$/, '');         // Trim - from end of text
    },
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

          if (response.data.success === true) {
            this.$notify({
              title: "Success",
              message: `Category ${isUpdate ? "updated" : "created"} successfully`,
              type: "success",
            });

            this.$emit("updateDataAfterNewAdd", response.data.category);

            if (!isUpdate) {
              this.localCategory = {
                name: "",
                slug: "",
                description: "",
              };
            }
          } else {
            this.$notify({
              title: "Error",
              message: `Failed to ${isUpdate ? "update" : "create"} category`,
              type: "error",
            });
          }
        } catch (error) {
          console.error("Error saving category:", error);
          this.$notify({
            title: "Error",
            message: "An unexpected error occurred while saving the category.",
            type: "error",
          });
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