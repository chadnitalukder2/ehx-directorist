<template>
    <div>
        <div v-if="selectedFile" class="fraise_image_wrapper" style="width: 100px; height: 100px;">
            <el-image :src="selectedFile" />
            <span @click="removeFile" class="file-del-btn"><el-icon><Delete /></el-icon></span>
        </div>
        <el-button type="primary" @click="openMediaUploader">{{ btnTitle }}</el-button>
    </div>
</template>

<script>
export default {
    props: {
        selectedFile: {
            type: String,
            default: ''
        },
        btnTitle: {
            type: String,
            default: 'Upload Image'
        }
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