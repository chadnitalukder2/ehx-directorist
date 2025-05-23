<template>  
    <div class="file-upload-container">
        <!-- Preview area when file is selected -->
        <div v-if="selectedFile" class="file-preview">
            <!-- File type detection and appropriate preview -->
            <div class="preview-wrapper">
                <!-- Image preview -->
                <el-image v-if="isImageFile" :src="selectedFile" fit="cover" class="file-thumbnail"
                    :preview-src-list="[selectedFile]" />
                <!-- Document/other file preview -->
                <div v-else class="file-icon">
                    <el-icon :size="32">
                        <Document />
                    </el-icon>
                    <span class="file-name">{{ getFileName(selectedFile) }}</span>
                </div>
            </div>

            <!-- Hover overlay with actions -->
            <div class="file-actions">
                <el-tooltip content="Remove File" placement="top">
                    <el-button type="danger" circle @click="removeFile" class="action-button" size="small">
                        <el-icon>
                            <Delete />
                        </el-icon>
                    </el-button>
                </el-tooltip>

                <el-tooltip content="Change File" placement="top">
                    <el-button type="primary" circle @click="openMediaUploader" class="action-button" size="small">
                        <el-icon>
                            <Edit />
                        </el-icon>
                    </el-button>
                </el-tooltip>
            </div>
        </div>

        <!-- Upload button when no file is selected -->
        <div v-else class="upload-button-container">

            <el-button @click="openMediaUploader" class="media-library-button" style="padding: 15px 9px;">
                <el-icon>
                    <Plus />
                </el-icon>
            </el-button>
        </div>

        <!-- Error message display -->
        <div v-if="errorMessage" class="error-message">
            <el-alert :title="errorMessage" type="error" show-icon @close="errorMessage = ''" />
        </div>
    </div>
</template>

<script>
import { Delete, Upload, Document, Picture, Edit } from '@element-plus/icons-vue'

export default {
    name: 'FileUploader',
    components: {
        Delete,
        Upload,
        Document,
        Picture,
        Edit
    },
    props: {
        selectedFile: {
            type: String,
            default: ''
        },
        btnTitle: {
            type: String,
            default: 'Upload File'
        },
        acceptedTypes: {
            type: Array,
            default: () => ['image/*', 'application/pdf', '.doc', '.docx', '.xls', '.xlsx',]
        },
        maxFileSize: {
            type: Number,
            default: 5 // in MB
        }
    },
    data() {
        return {
            fileList: [],
            errorMessage: '',
            isDragging: false
        };
    },
    computed: {
        isImageFile() {
            if (!this.selectedFile) return false;
            const extension = this.getFileExtension(this.selectedFile).toLowerCase();
            return ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'].includes(extension);
        }
    },
    methods: {
        openMediaUploader() {
            // Check if WordPress Media Library is available
            if (!window.wp || !window.wp.media) {
                console.error('WordPress Media Uploader not found');
                this.errorMessage = 'WordPress Media Uploader not available';
                return;
            }

            const frame = wp.media({
                title: 'Select or Upload Media',
                button: { text: 'Use this file' },
                multiple: false,
                library: { type: this.acceptedTypes.filter(type => !type.startsWith('.')).join(',') }
            });

            frame.on('select', () => {
                const attachment = frame.state().get('selection').first().toJSON();

                // Validate file size
                if (attachment.filesizeInBytes > this.maxFileSize * 1024 * 1024) {
                    this.errorMessage = `File size exceeds the maximum limit of ${this.maxFileSize}MB`;
                    return;
                }

                this.$emit('update:selectedFile', attachment.url);
                this.$emit('file-selected', attachment);
                this.fileList = [{ name: attachment.filename, url: attachment.url }];
                this.errorMessage = '';
            });

            frame.open();
        },



        removeFile() {
            this.$emit('update:selectedFile', '');
            this.$emit('file-removed');
            this.fileList = [];
            this.errorMessage = '';
        },

        getFileExtension(file) {
            const url = typeof file === 'string' ? file : file?.url || '';
            return url.split('.').pop().toLowerCase();
        },

        getFileName(file) {
            const url = typeof file === 'string' ? file : file?.url || '';
            return url.split('/').pop() || 'file';
        }
    }
};
</script>

<style scoped lang="scss">
.file-upload-container {
    width: 100%;
    max-width: 300px;
}

.file-preview {
    position: relative;
    width: 300px;
    height: 200px;
    border-radius: 8px;
    border: 2px dashed #E8EAF1;
    overflow: hidden;
    margin-bottom: 10px;
}

.preview-wrapper {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f7fa;
}

.file-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.file-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.file-name {
    margin-top: 10px;
    font-size: 14px;
    color: #606266;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.file-actions {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.file-preview:hover .file-actions {
    opacity: 1;
}

.action-button {
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
}

.upload-button-container {
    height: 200px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: 2px dashed #dcdfe6;
    background-color: #f5f7fa;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: all 0.3s ease;
}

.upload-button-container:hover {
    border-color: #E8EAF1;
    background-color: #f7fafc30;
}

.upload-button {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-icon {
    margin-right: 5px;
}

.divider-text {
    color: #909399;
    font-size: 12px;
}

.media-library-button {
    &:hover {
        background-color: #1A1B1C;
        color: #fff;
    }
}



.error-message {
    margin-top: 10px;
}
</style>