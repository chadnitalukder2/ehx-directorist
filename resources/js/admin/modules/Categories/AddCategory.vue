<template>
    <div class="ehxd_form_wrapper">

        <div class="input-wrapper">
            <p class="form-label" for="name">Category Name *</p>
            <el-input class="ehxd_input" v-model="categories.name" style="width: 100%"
                placeholder="Please Input Category Name" size="large" />
            <p class="error-message" style="margin: 0px 0px 10px 0px;">{{ name_error }}</p>
        </div>

        <div class="input-wrapper">
            <p class="form-label" for="name">Category Slug *</p>
            <el-input class="ehxd_input" v-model="categories.slug" style="width: 100%"
                placeholder="Please Input Category Slug" size="large" />
            <p class="error-message" style="margin: 0px 0px 10px 0px;">{{ slug_error }}</p>
        </div>

        <div class="input-wrapper">
            <p class="form-label" for="name">Description</p>
            <el-input class="ehxd_input" v-model="categories.description" style="width: 100%"
                placeholder="Please Input Description" size="large" type="textarea" />
        </div><br>

        <div class="input-wrapper" @click="saveCategory()">
            <el-button size="large" type="primary">Save Category</el-button>
        </div>

    </div>
</template>

<script>
import axios from "axios"; // Import Axios


export default {
    components: {
     
    },
    data() {
        return {
            categories: {
                name: "",
                slug: "",
                description: "",
            },
            name_error: "",
            slug_error: "",
            rest_api: window.EhxDirectoristData.rest_api,
        };

    },
    props: {
        categories_data: {
            type: Object,
        }
    },
    watch: {
        categories_data: {
            handler: function (val) {
                this.categories = val;
            },
            deep: true
        }
    },

    methods: {
        async saveCategory() {
            console.log("Category data:", this.categories);
            this.name_error = "";
            this.slug_error = "";
            if (!this.categories.name) {
                this.name_error = "Category name is required";
                return;
            }
            if (!this.categories.slug) {
                this.slug_error = "slug name is required";
                return;
            }
            try {
                const response = await axios.post(`${this.rest_api}/postCategory`, this.categories);
                this.$emit("updateDataAfterNewAdd", this.categories);
                this.categories = {
                    name: "",
                    slug: "",
                    description: "",
                };
                this.$notify({
                    title: 'Success',
                    message: 'Category Created successfully',
                    type: 'success',
                })
               // console.log('Category:', response.data);
            } catch (error) {
                console.error('Error fetching category:', error);
            }
        },

    },
    mounted() {
        if (this.categories_data) {
            console.log('Categories:', this.categories_data);
            this.categories = this.categories_data;
        }
    }
};
</script>