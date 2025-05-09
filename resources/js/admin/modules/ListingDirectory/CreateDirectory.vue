<template>
    <div class="tabs-container">
        <el-menu mode="horizontal" class="tabs" :default-active="activeTab" @select="handleTabChange">
            <el-menu-item index="general">
                <el-icon>
                    <HomeFilled />
                </el-icon>
                <span>General</span>
            </el-menu-item>
            <el-menu-item index="addListing">
                <el-icon>
                    <Document />
                </el-icon>
                <span>Add Listing Form</span>
            </el-menu-item>
        </el-menu>
    </div>
    
    <DirectorySettings v-if="activeTab == 'general'" />
    <FormBuilder v-if="activeTab == 'addListing'" />

</template>

<script>
import DirectorySettings from './components/DirectorySettings.vue';
import FormBuilder from './components/FormBuilder.vue';
export default {
    name: "CreateDirectory",
    components: {
        DirectorySettings,
        FormBuilder
    },
    data() {
        return {
            activeTab: 'general',
        }
    },
    methods: {
        handleTabChange(tab) {
            this.activeTab = tab
        }
    }
}
</script>