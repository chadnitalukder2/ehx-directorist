<template>
    <div class="ehxd_wrapper">

        <AppModal :title="'Add New Tag'" :width="700" :showFooter="false" ref="add_Tag_modal">
            <template #body>
                <AddTag />
            </template>
        </AppModal>

        <AppTable :tableData="tags" v-loading="loading">
            <template #header>
                <h1 class="table-title">All Tag</h1>
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
                        <!-- {{ formatAddedDate(row.added_date) }} -->
                    </template>
                </el-table-column>
                <el-table-column label="Operations" width="120">
                    <template #default="{ row }">
                        <el-tooltip class="box-item" effect="dark" content="Click to view books" placement="top-start">
                            <el-button class="ehxd_box_icon" link size="small">
                                <Icon icon="ehxd-edit" />
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="box-item" effect="dark" content="Click to delete books"
                            placement="top-start">
                            <el-button class="ehxd_box_icon" link size="small">
                                <Icon icon="ehxd-delete" />
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </template>

            <template #footer>
                <el-pagination v-model:current-page="currentPage" v-model:page-size="pageSize"
                    :page-sizes="[10, 20, 30, 40]" large :disabled="total_book <= pageSize" background
                    layout="total, sizes, prev, pager, next" :total="+total_book" />
            </template>

        </AppTable>

    </div>
</template>




<script>
import axios from "axios";

import AppTable from "../../components/AppTable.vue";
import Icon from "../../components/Icons/AppIcon.vue";
import AppModal from "../../components/AppModal.vue";
import AddTag from "./AddTag.vue";
export default {
    components: {
        AppTable,
        Icon,
        AppModal,
        AddTag,
    },
    data() {
        return {
            search: '',
            tags: [],
            book: {},
            total_book: 0,
            loading: false,
            currentPage: 1,
            pageSize: 10,
            active_id: null,
            add_Tag_modal: false,
            nonce: window.EhxDirectoristData.nonce,
            rest_api: window.EhxDirectoristData.rest_api,
        }
    },

    methods: {
        openTagAddModal() {
            if (this.$refs.add_Tag_modal) {
                this.$refs.add_Tag_modal.openModel();
            } else {
                console.log("Modal ref not found! Ensure AppModal is rendered.");
            }
        },
        async getAlltags() {
            this.loading = true;
            try {
                const response = await axios.get(`${this.rest_api}/getAllTags`, {
                    headers: {
                        'X-WP-Nonce': this.nonce
                    }
                });
                this.tags = response?.data?.tags_data;
               console.log('tags',response.data);
                this.loading = false;
                this.$notify({
                    title: 'Success',
                    message: 'Tag fetched successfully',
                    type: 'success',
                })
            } catch (error) {
                this.loading = false;
                console.error('Error fetching couriers:',);
            }
        },
    },

    mounted() {
        // console.log('window', window);
        this.getAlltags();
    },

}
</script>