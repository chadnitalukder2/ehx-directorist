<template>
    <div class="ehxd_wrapper">

        <AppModal :title="'Add New Tag'" :width="700" :showFooter="false" ref="add_Tag_modal">
            <template #body>
                <AddTag @updateDataAfterNewAdd="handleAddedTag" />
            </template>
        </AppModal>

        <AppTable :tableData="tags" v-loading="loading">
            <template #header>
                <div class="ehxd_title">
                    <h1 class="table-title">All Tag</h1>
                    <p class="table-short-dsc">Manage and view all your tags</p>
                </div>
                <el-button @click="openTagAddModal()" size="large" type="primary" icon="Plus" class="ltm_button">
                    Add New Tag
                </el-button>
            </template>

            <template #filter>
                <el-input class="ehxd-search-input ehxd_input" v-model="search" style="width: 240px" size="large"
                    placeholder="Please Input" prefix-icon="Search" />
            </template>

            <template #columns>
                <el-table-column prop="id" label="ID" width="60" />
                <el-table-column prop="name" label="Name" width="auto" />
                <el-table-column prop="slug" label="Slug" width="auto" />
                <el-table-column prop="added_date" label="Add Date" width="auto">
                    <template #default="{ row }">
                        {{ formatAddedDate(row.created_at) }}
                    </template>
                </el-table-column>
                <el-table-column label="Operations" width="120">
                    <template #default="{ row }">
                        <el-tooltip class="box-item" effect="dark" content="Click to edit Tag" placement="top-start">
                            <el-button @click="openUpdateTagModal(row)" class="ehxd_box_icon" link size="small">
                                <Icon icon="ehxd-edit" />
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="box-item" effect="dark" content="Click to delete Tag" placement="top-start">
                            <el-button @click="openDeleteTagModal(row)" class="ehxd_box_icon" link size="small">
                                <Icon icon="ehxd-delete" />
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </template>

            <template #footer>
                <div class="ehxd_footer_page">
                    <p>Page {{ currentPage }} of {{ last_page }}</p>
                </div>

                <el-pagination v-model:current-page="currentPage" v-model:page-size="pageSize"
                    :page-sizes="[10, 20, 30, 40]" large :disabled="total_Tag <= pageSize" background
                    layout="total, sizes, prev, pager, next" :total="+total_Tag" />
            </template>

        </AppTable>

        <AppModal :title="'Update Tag'" :width="700" :showFooter="false" ref="update_Tag_modal">
            <template #body>
                <div>
                    <AddTag ref="AddTag" :tags_data="Tag" @updateDataAfterNewAdd="handleUpdatedTag" />
                </div>
            </template>
            <template #footer>

            </template>
        </AppModal>

        <AppModal :title="'Delete Tag'" :width="500" :showFooter="false" ref="delete_Tag_modal">
            <template #body>

                <div class="delete-modal-body">
                    <h1>Are you sure ?</h1>
                    <p>You want to delete this Tag</p>
                </div>
            </template>
            <template #footer>
                <div class="exd-modal-footer" style="text-align: center;">
                    <el-button @click="$refs.delete_Tag_modal.handleClose()" type="default"
                        size="medium">Cancel</el-button>
                    <el-button @click="deleteTag" type="primary" size="medium">Delete</el-button>
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
import AddTag from "./AddTag.vue";
import { id } from "element-plus/es/locale/index.mjs";
export default {
    components: {
        AppTable,
        Icon,
        AppModal,
        AddTag
    },
    data() {
        return {
            search: '',
            tags: [],
            Tag: {},
            total_Tag: 0,
            loading: false,
            currentPage: 1,
            last_page: 1,
            pageSize: 10,
            active_id: null,
            add_Tag_modal: false,
            nonce: window.EhxDirectoristData.nonce,
            rest_api: window.EhxDirectoristData.rest_api,
        }
    },
    watch: {
        currentPage() {
            this.getAllTag();
        },
        pageSize() {
            this.currentPage = 1;
            this.getAllTag();
        },
        search: {
            handler() {
                this.currentPage = 1;
                this.getAllTag();
            },
            immediate: false
        },
    },

    methods: {
        openTagAddModal() {
            if (this.$refs.add_Tag_modal) {
                this.$refs.add_Tag_modal.openModel();
            } else {
                console.log("Modal ref not found! Ensure AppModal is rendered.");
            }
        },

        formatAddedDate(date) {
            if (!date) return '';
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            return new Date(date).toLocaleDateString('en-GB', options);
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
                this.total_Tag = response?.data?.total || 0;
                this.last_page = response?.data?.last_page || 1;
                this.loading = false;
            } catch (error) {
                this.loading = false;
                console.error('Error fetching couriers:',);
            }
        },

        openDeleteTagModal(row) {
            this.active_id = row.id;
            this.$refs.delete_Tag_modal.openModel();
        },
        async deleteTag() {
            this.loading = true;
            const id = this.active_id;

            try {
                const response = await axios.post(`${this.rest_api}/deleteTag/${id}`, {}, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': this.nonce
                    }
                });

                this.loading = false;
                this.$refs.delete_Tag_modal.handleClose();

                if (response.data.success === true) {
                    this.getAllTag();
                    this.$notify({
                        title: 'Success',
                        message:  'Tag data deleted successfully',
                        type: 'success',
                    });
                } else {
                    this.$notify({
                        title: 'Error',
                        message: 'Failed to delete tag data',
                        type: 'error',
                    });
                }

            } catch (error) {
                this.loading = false;
                console.error('Error deleting Tag:', error);
                this.$notify({
                    title: 'Error',
                    message: 'An unexpected error occurred while deleting the tag.',
                    type: 'error',
                });
            }
        },

        openUpdateTagModal(row) {
            this.Tag = row;
            this.$refs.update_Tag_modal.openModel();
        },

        handleUpdatedTag(updated) {
            this.getAllTag(); // or update the array locally
            this.$refs.update_Tag_modal.handleClose();
        },

        handleAddedTag(newTag) {
            this.getAllTag();
            // this.$refs.add_Tag_modal.handleClose();
        }

    },

    mounted() {
        // console.log('window', window);
        this.getAllTag();
    },

}
</script>

<style lang="scss" scoped></style>