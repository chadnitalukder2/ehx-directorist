<template>
  <div class="ehxd_form_wrapper">
    <el-form ref="TagForm" :model="localTag" :rules="rules" label-position="top" @submit.prevent>
      <el-form-item label="Tag Name" prop="name">
        <el-input class="ehxd_input" v-model="localTag.name" placeholder="Please Input Tag Name" size="large" />
      </el-form-item>

      <el-input type="hidden" class="ehxd_input" v-model="localTag.slug" placeholder="Auto-generated from name"
        size="large" />

      <el-form-item style="margin-bottom: 0px !important; ">
        <el-button type="primary" size="large" @click="submitTagForm">
          {{ localTag.id ? "Update Tag" : "Save Tag" }}
        </el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "AddTag",
  props: {
    tags_data: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      localTag: {
        name: "",
        slug: "",
      },
      rules: {
        name: [
          { required: true, message: "Tag name is required", trigger: "blur" },
        ],
      },
      rest_api: window.EhxDirectoristData.rest_api,
      nonce: window.EhxDirectoristData.nonce,
    };
  },
  watch: {
    tags_data: {
      immediate: true,
      deep: true,
      handler(val) {
        this.localTag = { ...val }; // clone to avoid mutating parent directly
      },
    },
    'localTag.name'(newName) {
      this.localTag.slug = this.slugify(newName);
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
    async submitTagForm() {
      this.$refs.TagForm.validate(async (valid) => {
        if (!valid) return;

        const isUpdate = !!this.localTag.id;
        const url = isUpdate
          ? `${this.rest_api}/updateTag/${this.localTag.id}`
          : `${this.rest_api}/postTag`;

        try {
          const response = await axios.post(url, this.localTag, {
            headers: {
              "Content-Type": "application/json",
              "X-WP-Nonce": this.nonce,
            },
          });

          if (response.data.success === true) {
            this.$notify({
              title: "Success",
              message: `Tag ${isUpdate ? "updated" : "created"} successfully`,
              type: "success",
            });

            this.$emit("updateDataAfterNewAdd", response.data.Tag);

            if (!isUpdate) {
              this.localTag = {
                name: "",
                slug: "",
              };
            }
          } else {
            this.$notify({
              title: "Error",
              message: "Failed to save tag data",
              type: "error",
            });
          }

        } catch (error) {
          console.error("Error saving tag:", error);
          this.$notify({
            title: "Error",
            message: "An unexpected error occurred while saving the tag.",
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