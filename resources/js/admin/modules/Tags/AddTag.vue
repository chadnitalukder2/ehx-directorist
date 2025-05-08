<template>
    <div class="ehxd_form_wrapper">

        <div class="input-wrapper">
            <p class="form-label" for="name">Tag Name *</p>
            <el-input class="ehxd_input" v-model="tags.name" style="width: 100%"
                placeholder="Please Input Tag Name" size="large" />
            <p class="error-message" style="margin: 0px 0px 10px 0px;">{{ name_error }}</p>
        </div>

        <div class="input-wrapper">
            <p class="form-label" for="name">Tag Slug *</p>
            <el-input class="ehxd_input" v-model="tags.slug" style="width: 100%"
                placeholder="Please Input Tag Slug" size="large" />
            <p class="error-message" style="margin: 0px 0px 10px 0px;">{{ slug_error }}</p>
        </div><br>

        <div class="input-wrapper" @click="saveTag()">
            <el-button size="large" type="primary">Save Tag</el-button>
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
            tags: {
                name: "",
                slug: "",
            },
            name_error: "",
            slug_error: "",
            rest_api: window.EhxDirectoristData.rest_api,
        };

    },

    methods: {
        async saveTag() {
            this.name_error = "";
            this.slug_error = "";
            if (!this.tags.name) {
                this.name_error = "Tag name is required";
                return;
            }
            if (!this.tags.slug) {
                this.slug_error = "slug name is required";
                return;
            }
            try {
                const response = await axios.post(`${this.rest_api}/postTag`, this.tags);
                this.tags = {
                    name: "",
                    slug: "",
                    description: "",
                };
                this.$notify({
                    title: 'Success',
                    message: 'Tag Created successfully',
                    type: 'success',
                })
               // console.log('Tag:', response.data);
            } catch (error) {
                console.error('Error fetching Tag:', error);
            }
        },

    }
};
</script>