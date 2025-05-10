<template>
  <div class="ehxd_wrapper">
    <div class="ehxd-header-section">
      <div class="ehxd_title">
        <h1 class="table-title">Add Directory Listing</h1>
        <p class="table-short-dsc">Create and publish a new directory listing with essential details, links, and
          location.</p>
      </div>
      <div class="ehxd_header_btn">
        <router-link to="/directory-listing">
          <el-button size="large" type="primary" class="ltm_button">
            All Directory Listing
            <el-icon style="margin-left: 8px;">
              <Right />
            </el-icon>
          </el-button>
        </router-link>
      </div>

    </div>
    <div class="ehxd_form_wrapper">
      <el-form ref="ListForm" :model="localList" :rules="rules" label-position="top" @submit.prevent>
        <div class="ehxd_input_warpper">
          <div class="ehxd_input_item">
            <el-form-item label="Name" prop="name">
              <el-input v-model="localList.name" placeholder="Enter name" />
            </el-form-item>
          </div>
          <div class="ehxd_input_item">
            <el-form-item label="Email" prop="email">
              <el-input v-model="localList.email" placeholder="demo@example.com" />
            </el-form-item>
          </div>

        </div>

        <div class="ehxd_input_warpper">
          <div class="ehxd_input_item">
            <el-form-item label="Phone" prop="phone">
              <el-input v-model="localList.phone" placeholder="Phone number" />
            </el-form-item>
          </div>
          <div class="ehxd_input_item">
            <el-form-item label="Website" prop="website_url">
              <el-input v-model="localList.website_url" placeholder="https://example.com/" />
            </el-form-item>
          </div>

        </div>

        <div class="ehxd_input_warpper">
          <div class="ehxd_input_item">
            <el-form-item label="Category Name" prop="category_id">
              <el-select class="erm_input" v-model="localList.category_id" placeholder="Category Name" size="large"
                style="width: 100%">
                <el-option v-for="category in categories" :key="category.value" :label="category.name"
                    :value="category.id" />
            </el-select>

            </el-form-item>
          </div>
          <div class="ehxd_input_item">
            <el-form-item label="Tag Name" prop="tag_id">
              <el-select class="erm_input" multiple v-model="localList.tag_id" placeholder="Tag Name" size="large"
                style="width: 100%">
                <el-option v-for="tag in tags" :key="tag.value" :label="tag.name"
                    :value="tag.id" />
            </el-select>
            </el-form-item>
          </div>

        </div>

        <el-form-item label="Address" prop="address">
          <GoogleMap />
        </el-form-item>
        <el-form-item label="Image " prop="short_description">
         <AppFileUpload />
        </el-form-item>
        <el-form-item label="Short Description " prop="short_description">
          <el-input type="textarea" v-model="localList.short_description" />
        </el-form-item>
        <el-form-item label="Short Description " prop="short_description">
        <WpEditor v-model="localList.short_description" />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" size="large" @click="submitListForm">
            Save List

          </el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

import Icon from "../../components/Icons/AppIcon.vue";
import GoogleMap from "../../components/GoogleMap.vue";
import AppFileUpload from "../../components/AppFileUpload.vue";
import WpEditor from "../../components/WpEditor.vue";
import { id } from "element-plus/es/locale/index.mjs";
export default {
  components: {
    Icon,
    GoogleMap,
    AppFileUpload,
    WpEditor,
  },
  data() {
    return {
      localList: {
        name: "",
        email: "",
        phone: "",
        address: "",
        social_links: [],
        short_description: "",
        category_id: '',
        tag_id: '',
      },
      tags: [],
      categories: [],
      nonce: window.EhxDirectoristData.nonce,
      rest_api: window.EhxDirectoristData.rest_api,
    }
  },


  methods: {
    async getAllCategories() {
            this.loading = true;
            try {
                const response = await axios.get(`${this.rest_api}/getAllCategories`, {
                    headers: {
                        'X-WP-Nonce': this.nonce
                    }
                });
                this.categories = response?.data?.categories_data;
                this.total_category = response?.data?.total || 0;
                this.last_page = response?.data?.last_page || 1;
                this.loading = false;
            } catch (error) {
                this.loading = false;
                console.error('Error fetching couriers:',);
            }
        },

        async getAllTag() {
            this.loading = true;
            try {
                const response = await axios.get(`${this.rest_api}/getAllTag`, {
                    params: {
                        page: this.currentPage,
                        limit: this.pageSize,
                        search: this.search || '',
                    },
                    headers: {
                        'X-WP-Nonce': this.nonce
                    }
                });
                this.tags = response?.data?.tags_data;
                this.loading = false;
            } catch (error) {
                this.loading = false;
                console.error('Error fetching couriers:',);
            }
        },

  },

  mounted() {
        // console.log('window', window);
        this.getAllCategories();
        this.getAllTag();
    },

}
</script>

<style lang="scss" scoped>
.ehxd_wrapper {
  padding: 20px 35px;
  background-color: #F8F9FC;
  font-family: Inter;

  .ehxd-header-section {
    align-items: center;
    background-color: #fff;
    display: flex;
    border-radius: 10px;
    padding: 12px 30px;
    justify-content: space-between;

    .ehxd_title {
      .table-title {
        margin: 0;
        color: #2F3448;
        font-family: Inter;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: 24px;
      }

      .table-short-dsc {
        padding-top: 4px;
        margin: 0px;
        font-size: 12px;
        color: #3c434a;
        line-height: 1.5;
      }
    }

    .ehxd_header_btn {
      .ltm_button {
        padding: 0px 10px;
        font-size: 13px;
        font-weight: 500;
        line-height: 24px;
        border-radius: 8px;
        height: 35px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      }
    }
  }

  .ehxd_form_wrapper {
    margin: 20px 0px;
    border-radius: 8px;
    padding: 30px;
    background: #fff;
    .ehxd_input_warpper{
      display: flex;
      justify-content: space-between;
      gap: 30px;
      .ehxd_input_item {
        width: 100%;
        max-width: 50%;
      }
    }
  }
}
</style>