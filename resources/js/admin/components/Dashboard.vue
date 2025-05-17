<template>
  <div class="ehxd_dashboard_overview">
    <div class="ehxd_overview_card_warper">
      <div class="ehxd_menu_item_card">
        <span class="dashicons dashicons-list-view ehxd_icon"></span>
        <div class="ehxd_menu_card_details ">
          <h5 class="ehxd_title">Total Directory Listing</h5>
          <p class="ehxd_total">{{ total_listing }}</p>
          <router-link to="/directory-listing" class="ehxd_view_all">directory listing</router-link>
        </div>
      </div>

      <div class="ehxd_menu_item_card">
        <span class="dashicons dashicons-category ehxd_icon"></span>
        <div class="ehxd_menu_card_details ">
          <h5 class="ehxd_title">Total Category</h5>
          <p class="ehxd_total">{{ total_category }}</p>
          <router-link to="/categories" class="ehxd_view_all">Total category</router-link>
        </div>
      </div>
      <div class="ehxd_menu_item_card">
        <span class="dashicons dashicons-tag ehxd_icon"></span>
        <div class="ehxd_menu_card_details ">
          <h5 class="ehxd_title">Total Tag</h5>
          <p class="ehxd_total">{{ total_Tag }}</p>
          <router-link to="/tags" class="ehxd_view_all">Total Tag Table</router-link>
        </div>
      </div>

    </div>

    <AppTable :tableData="listings" v-loading="loading">
      <template #header>
        <div class="ehxd_title">
          <h1 class="table-title">Recent directory Listing</h1>
        </div>


      </template>

      <template #columns>
        <el-table-column prop="id" label="ID" width="60" />
        <el-table-column label="Logo" width="auto">
          <template #default="{ row }">
            <img v-if="row?.logo" :src="row?.logo" alt="logo" style="width: 50px; height: 50px; object-fit: cover;">
            <span v-else>No Image</span>
          </template>
        </el-table-column>
        <el-table-column prop="name" label="Name" width="auto" />
        <el-table-column prop="phone" label="Phone" width="auto" />
        <el-table-column prop="email" label="Email" width="auto" />
        <el-table-column prop="address" label="Address" width="auto" />
        <el-table-column prop="added_date" label="Add Date" width="auto">
          <template #default="{ row }">
            {{ formatAddedDate(row.created_at) }}
          </template>
        </el-table-column>
      </template>



    </AppTable>



  </div>
</template>
<script>
import axios from 'axios';
import AppTable from './AppTable.vue'
export default {
  name: 'Dashboard',
  components: {
    AppTable,
  },
  data() {
    return {
      loading: false,
      listings: [],
      total_listing: 0,
      total_category: 0,
      total_tag: 0,
      nonce: window.EhxDirectoristData.nonce,
      rest_api: window.EhxDirectoristData.rest_api,
    }
  },
  methods: {
    formatAddedDate(date) {
      if (!date) return '';
      const options = { day: 'numeric', month: 'long', year: 'numeric' };
      return new Date(date).toLocaleDateString('en-GB', options);
    },
    async getAllListings() {
      this.loading = true;
      try {
        const response = await axios.get(`${this.rest_api}/getAllListings`, {
          params: {
            page: this.currentPage,
            limit: this.pageSize,
            search: this.search || '',
          },
          headers: {
            'X-WP-Nonce': this.nonce
          }
        });
        this.listings = response?.data?.listings_data;
        this.total_listing = response?.data?.total || 0;
        this.last_page = response?.data?.last_page || 1;
        this.loading = false;
      } catch (error) {
        this.loading = false;
        console.error('Error fetching couriers:',);
      }
    },

    async getAllCategories() {
      this.loading = true;
      try {
        const response = await axios.get(`${this.rest_api}/getAllCategories`, {
          params: {
            page: this.currentPage,
            limit: this.pageSize,
            search: this.search || '',
          },
          headers: {
            'X-WP-Nonce': this.nonce
          }
        });
        this.total_category = response?.data?.total || 0;
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
        this.total_Tag = response?.data?.total || 0;
        this.loading = false;
      } catch (error) {
        this.loading = false;
        console.error('Error fetching couriers:',);
      }
    },

  },
  mounted() {
    this.getAllListings();
    this.getAllCategories();
    this.getAllTag();
  }
}
</script>

<style lang="scss">
.ehxd_menu_card_details {
  .ehxd_title {
    font-size: 15px !important;
    font-weight: 500 !important;
  }

  .ehxd_total {
    font-size: 22px !important;
    font-weight: 600 !important;
  }
}

.ehxd-admin-page {
  background: #F8F9FC;
}
</style>