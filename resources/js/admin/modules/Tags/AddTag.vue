<template>
    <div class="ehxd_form_wrapper">
      <el-form
        ref="TagForm"
        :model="localTag"
        :rules="rules"
        label-position="top"
        @submit.prevent
      >
        <el-form-item label="Tag Name" prop="name">
          <el-input
            class="ehxd_input"
            v-model="localTag.name"
            placeholder="Please Input Tag Name"
            size="large"
          />
        </el-form-item>
  
        <el-form-item label="Tag Slug" prop="slug">
          <el-input
            class="ehxd_input"
            v-model="localTag.slug"
            placeholder="Auto-generated from name"
            size="large"
          />
        </el-form-item>
  
        <el-form-item style="margin-bottom: 0px !important; ">
          <el-button   type="primary" size="large" @click="submitTagForm">
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
          slug: [
            { required: true, message: "Slug is required", trigger: "blur" },
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
    },
    methods: {
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
  
            this.$notify({
              title: "Success",
              message: `Tag data ${isUpdate ? "updated" : "created"} successfully`,
              type: "success",
            });
  
            if (!isUpdate) {
              this.$emit("updateDataAfterNewAdd", response.data.Tag);
              this.localTag = {
                name: "",
                slug: "",
              };
            } else {
              this.$emit("updateDataAfterNewAdd", response.data.Tag);
            }
          } catch (error) {
            console.error("Error saving Tag:", error);
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
  