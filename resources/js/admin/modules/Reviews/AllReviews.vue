<template>
    <div class="ehxd_wrapper">

        <AppTable :tableData="reviews" v-loading="loading">
            <template #header>
                <div class="ehxd_title">
                    <h1 class="table-title">All Review</h1>
                    <p class="table-short-dsc">Manage and view all your review</p>
                </div>
            </template>

            <template #filter>
                <el-input class="ehxd-search-input ehxd_input" v-model="search" style="width: 240px" size="large"
                    placeholder="Please Input" prefix-icon="Search" />
            </template>

            <template #columns>
                <el-table-column prop="id" label="ID" width="60" />
                <el-table-column prop="directory_list_id" label="listing Id" width="auto" />
                <el-table-column prop="name" label="Name" width="auto" />
                <el-table-column prop="email" label="Email" width="auto" />
                <el-table-column prop="rating" label="Rating" width="auto" />
                <el-table-column prop="status" label="Status" width="auto" />
                <el-table-column prop="title" label="Title" width="auto" />
                <el-table-column label="Operations" width="120">
                    <template #default="{ row }">
                        <el-tooltip class="box-item" effect="dark" content="Click to edit Review"
                            placement="top-start">
                            <el-button @click="openUpdateReviewModal(row)" class="ehxd_box_icon" link size="small">
                                <Icon icon="ehxd-edit" />
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="box-item" effect="dark" content="Click to delete Review"
                            placement="top-start">
                            <el-button @click="openDeleteReviewModal(row)" class="ehxd_box_icon" link size="small">
                                <Icon icon="ehxd-delete" />
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </template>

            <template #footer>
                <div class="ehxd_footer_page">
                    <p>Page {{ currentPage }} of {{ last_page}}</p>
                </div>
              
                <el-pagination v-model:current-page="currentPage" v-model:page-size="pageSize"
                    :page-sizes="[10, 20, 30, 40]" large :disabled="total_Review <= pageSize" background
                    layout="total, sizes, prev, pager, next" :total="+total_Review" />
            </template>

        </AppTable>

        <AppModal :title="'Update Review'" :width="700" :showFooter="false" ref="update_review_modal">
            <template #body>
                <div>
                    <UpdateReview ref="addReview" :reviews_data="Review"
                        @updateDataAfterNewAdd="handleUpdatedreview" />
                </div>
            </template>
            <template #footer>

            </template>
        </AppModal>

        <AppModal :title="'Delete Review'" :width="500" :showFooter="false" ref="delete_Review_modal">
            <template #body>

                <div class="delete-modal-body">
                    <h1>Are you sure ?</h1>
                    <p>You want to delete this Review</p>
                </div>
            </template>
            <template #footer>
                <div class="exd-modal-footer" style="text-align: center;">
                    <el-button @click="$refs.delete_Review_modal.handleClose()" type="default"
                        size="medium">Cancel</el-button>
                    <el-button @click="deleteReview" type="primary" size="medium">Delete</el-button>
                </div>
            </template>
        </AppModal>

    </div>
</template>

<script>
import axios from "axios";

import AppTable from "../../components/AppTable.vue";
import Icon from "../../components/Icons/AppIcon.vue";
import AppModal from "../../components/AppModal.vue";
import UpdateReview from "./UpdateReview.vue";
import { id } from "element-plus/es/locale/index.mjs";
export default {
    components: {
        AppTable,
        Icon,
        AppModal,
        UpdateReview
    },
    data() {
        return {
            search: '',
            reviews: [],
            Review: {},
            total_Review: 0,
            loading: false,
            currentPage: 1,
            last_page: 1,
            pageSize: 10,
            active_id: null,
            add_Review_modal: false,
            nonce: window.EhxDirectoristData.nonce,
            rest_api: window.EhxDirectoristData.rest_api,
        }
    },
    watch: {
        currentPage() {
            this.getAllReviews();
        },
        pageSize() {
            this.currentPage = 1;
            this.getAllReviews();
        },
        search: {
            handler() {
                this.currentPage = 1; 
                this.getAllReviews();
            },
            immediate: false
        },
    },

    methods: {
       
        formatAddedDate(date) {
            if (!date) return '';
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            return new Date(date).toLocaleDateString('en-GB', options);
        },

        async getAllReviews() {
            this.loading = true;
            try {
                const response = await axios.get(`${this.rest_api}/getAllReviews`, {
                    params: {
                        page: this.currentPage,
                        limit: this.pageSize,
                        search: this.search || '',
                    },
                    headers: {
                        'X-WP-Nonce': this.nonce
                    }
                });
                this.reviews = response?.data?.reviews_data;
                this.total_Review = response?.data?.total || 0;
                this.last_page = response?.data?.last_page || 1;
                this.loading = false;
            } catch (error) {
                this.loading = false;
                console.error('Error fetching couriers:',);
            }
        },

        openDeleteReviewModal(row) {
            this.active_id = row.id;
            this.$refs.delete_Review_modal.openModel();
        },
        async deleteReview() {
            this.loading = true;
            const id = this.active_id;
            try {
                const response = await axios.post(`${this.rest_api}/deleteReview/${id}`, {
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': this.nonce
                    }
                });
                this.loading = false;
                this.$refs.delete_Review_modal.handleClose();
                this.getAllReviews();
                this.$notify({
                    title: 'Success',
                    message: 'Review data deleted successfully',
                    type: 'success',
                })
            } catch (error) {
                this.loading = false;
                console.error('Error deleting Review:', error);
            }
        },

        openUpdateReviewModal(row) {
            this.review = row;
            this.$refs.update_review_modal.openModel();
        },
        handleUpdatedReview(updated) {
            this.getAllReviews(); // or update the array locally
            this.$refs.update_review_modal.handleClose();
        },



    },

    mounted() {
        // console.log('window', window);
        this.getAllReviews();
    },

}
</script>

<style lang="scss" scoped>

</style>