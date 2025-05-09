<template>
    <div>
        <el-button type="primary" @click="openMediaUploader">{{ btnTitle }}</el-button>
        <div v-if="selectedFile" class="fraise_image_wrapper">
            <el-image :src="selectedFile" />
            <span @click="removeFile" class="file-del-btn"><el-icon><Delete /></el-icon></span>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        selectedFile : '',
        btnTitle: 'Upload File'
    },
    data() {
        return {
            fileList: [],
            test: window.fluentRaiseAdmin
        };
    },
    methods: {
        openMediaUploader() {
            if (!wp || !wp.media) {
                console.error('WordPress Media Uploader not found');
                return;
            }
            
            const frame = wp.media({
                title: 'Select or Upload Media',
                button: { text: 'Use this file' },
                multiple: false
            });

            frame.on('select', () => {
                const attachment = frame.state().get('selection').first().toJSON();
                this.$emit('update:selectedFile', attachment.url);
                this.fileList = [{ name: attachment.filename, url: attachment.url }];
            });
            
            frame.open();
        },
        removeFile() {
            this.$emit('update:selectedFile', '');
            this.fileList = [];
        }
    }
};
</script>