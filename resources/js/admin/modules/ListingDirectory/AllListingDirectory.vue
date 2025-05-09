<template>
    <div class="ehxd_wrapper">

        <AppModal :title="'Add New List'" :width="700" :showFooter="false" ref="add_List_modal">
            <template #body>
                <AddListingDirectory @updateDataAfterNewAdd="handleAddedList" />
            </template>
        </AppModal>

        <AppTable :tableData="listings" v-loading="loading">
            <template #header>
                <div class="ehxd_title">
                    <h1 class="table-title">All List</h1>
                    <p class="table-short-dsc">Manage and view all your listings</p>
                </div>
                <el-button @click="openListAddModal()" size="large" type="primary" icon="Plus" class="ltm_button">
                    Add New List
                </el-button>
            </template>

            <template #filter>
                <el-input class="ehxd-search-input ehxd_input" v-model="search" style="width: 240px" size="large"
                    placeholder="Please Input" prefix-icon="Search" />
            </template>

            <template #columns>
                <el-table-column prop="id" label="ID" width="60" />
                <el-table-column prop="name" label="Logo" width="auto" />
                <el-table-column prop="name" label="Name" width="auto" />
                <el-table-column prop="phone" label="Phone" width="auto" />
                <el-table-column prop="email" label="Email" width="auto" />
                <el-table-column prop="address" label="Address" width="auto" />
                <el-table-column prop="added_date" label="Add Date" width="auto">
                    <template #default="{ row }">
                        {{ formatAddedDate(row.created_at) }}
                    </template>
                </el-table-column>
                <el-table-column label="Operations" width="120">
                    <template #default="{ row }">
                        <el-tooltip class="box-item" effect="dark" content="Click to view listing details"
                            placement="top-start">
                            <el-button @click="openDeleteListModal(row)" class="ehxd_box_icon" link size="small">
                                <Icon icon="ehxd-eye" />
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="box-item" effect="dark" content="Click to edit List"
                            placement="top-start">
                            <el-button @click="openUpdateListModal(row)" class="ehxd_box_icon" link size="small">
                                <Icon icon="ehxd-edit" />
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="box-item" effect="dark" content="Click to delete List"
                            placement="top-start">
                            <el-button @click="openDeleteListModal(row)" class="ehxd_box_icon" link size="small">
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
                    :page-sizes="[10, 20, 30, 40]" large :disabled="total_List <= pageSize" background
                    layout="total, sizes, prev, pager, next" :total="+total_List" />
            </template>

        </AppTable>

        <AppModal :title="'Update List'" :width="800" :showFooter="false" ref="update_List_modal">
            <template #body>
                <div>
                    <AddListingDirectory ref="AddListingDirectory" :listings_data="List"
                        @updateDataAfterNewAdd="handleUpdatedList" />
                </div>
            </template>
            <template #footer>

            </template>
        </AppModal>


        <AppModal :title="'Delete List'" :width="500" :showFooter="false" ref="delete_List_modal">
            <template #body>

                <div class="delete-modal-body">
                    <h1>Are you sure ?</h1>
                    <p>You want to delete this List</p>
                </div>
            </template>
            <template #footer>
                <div class="exd-modal-footer" style="text-align: center;">
                    <el-button @click="$refs.delete_List_modal.handleClose()" type="default"
                        size="medium">Cancel</el-button>
                    <el-button @click="deleteList" type="primary" size="medium">Delete</el-button>
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
import AddListingDirectory from "./AddListingDirectory.vue";
import { id } from "element-plus/es/locale/index.mjs";
export default {
    components: {
        AppTable,
        Icon,
        AppModal,
        AddListingDirectory
    },
    data() {
        return {
            search: '',
            listings: [],
            List: {},
            total_List: 0,
            loading: false,
            currentPage: 1,
            last_page: 1,
            pageSize: 10,
            active_id: null,
            add_List_modal: false,
            nonce: window.EhxDirectoristData.nonce,
            rest_api: window.EhxDirectoristData.rest_api,
        }
    },
    watch: {
        currentPage() {
            this.getAlllistings();
        },
        pageSize() {
            this.currentPage = 1;
            this.getAlllistings();
        },
        search: {
            handler() {
                this.currentPage = 1; 
                this.getAlllistings();
            },
            immediate: false
        },
    },

    methods: {
        openListAddModal() {
            if (this.$refs.add_List_modal) {
                this.$refs.add_List_modal.openModel();
            } else {
                console.log("Modal ref not found! Ensure AppModal is rendered.");
            }
        },

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
                this.total_List = response?.data?.total || 0;
                this.last_page = response?.data?.last_page || 1;
                this.loading = false;
            } catch (error) {
                this.loading = false;
                console.error('Error fetching couriers:',);
            }
        },

        openDeleteListModal(row) {
            this.active_id = row.id;
            this.$refs.delete_List_modal.openModel();
        },
        async deleteList() {
            this.loading = true;
            const id = this.active_id;
            try {
                const response = await axios.post(`${this.rest_api}/deleteList/${id}`, {
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': this.nonce
                    }
                });
                this.loading = false;
                this.$refs.delete_List_modal.handleClose();
                this.getAlllistings();
                this.$notify({
                    title: 'Success',
                    message: 'List deleted successfully',
                    type: 'success',
                })
            } catch (error) {
                this.loading = false;
                console.error('Error deleting List:', error);
            }
        },

        openUpdateListModal(row) {
            this.List = row;
            this.$refs.update_List_modal.openModel();
        },

        handleUpdatedList(updated) {
            this.getAlllistings(); // or update the array locally
            this.$refs.update_List_modal.handleClose();
        },

        handleAddedList(newList) {
            this.getAlllistings();
            // this.$refs.add_List_modal.handleClose();
        }

    },

    mounted() {
        // console.log('window', window);
        this.getAllListings();
    },

}
</script>

<style lang="scss" scoped>

</style>