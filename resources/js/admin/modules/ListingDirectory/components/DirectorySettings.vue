<template>
    <div class="listing-settings-container">
      <div class="settings-section">
        <h2 class="section-title">Default listing status</h2>
        
        <div class="setting-group">
          <div class="setting-label">New listing</div>
          <el-select v-model="newListingStatus" class="status-select" popper-class="status-select-dropdown">
            <el-option value="pending" label="Pending"></el-option>
            <el-option value="active" label="Active"></el-option>
            <el-option value="inactive" label="Inactive"></el-option>
          </el-select>
        </div>
        
        <div class="setting-group">
          <div class="setting-label">Edited listing</div>
          <el-select v-model="editedListingStatus" class="status-select" popper-class="status-select-dropdown">
            <el-option value="pending" label="Pending"></el-option>
            <el-option value="active" label="Active"></el-option>
            <el-option value="inactive" label="Inactive"></el-option>
          </el-select>
        </div>
      </div>
      
      <div class="settings-section">
        <h2 class="section-title">Default listing expiration days</h2>
        <div class="setting-description">Set the number of days before a listing automatically expires.</div>
        
        <div class="setting-group">
          <el-input-number 
            v-model="expirationDays" 
            :min="1" 
            :max="365" 
            controls-position="none"
            class="days-input"
          ></el-input-number>
        </div>
      </div>
      
      <div class="settings-section">
        <h2 class="section-title">Default listing preview image</h2>
        <div class="setting-description">
          This image will appear when a listing does not have a preview image uploaded. 
          Leave it empty to hide the preview image.
        </div>
        
        <div class="preview-image-container">
          <div class="preview-image-uploader">
            <img v-if="previewImage" :src="previewImage" class="preview-image" />
            <div v-else class="empty-preview">
              <el-icon class="image-icon"><Picture /></el-icon>
            </div>
            
            <div class="upload-button-container">
              <el-button type="primary" class="upload-button">Upload Image</el-button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { Picture } from '@element-plus/icons-vue'
  
  export default {
    name: 'ListingSettings',
    components: {
      Picture
    },
    data() {
      return {
        newListingStatus: 'pending',
        editedListingStatus: 'pending',
        expirationDays: 30,
        previewImage: null
      }
    },
    methods: {
      handleUpload() {
        // Upload handling logic would go here
        console.log('Upload button clicked')
      }
    }
  }
  </script>
  
  <style lang="scss" scoped>
  .listing-settings-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    
    .settings-section {
      margin-bottom: 40px;
      
      .section-title {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        margin-top: 0;
      }
      
      .setting-description {
        font-size: 16px;
        color: #666;
        margin-bottom: 20px;
        line-height: 1.5;
      }
      
      .setting-group {
        margin-bottom: 20px;
        
        .setting-label {
          font-size: 16px;
          font-weight: 500;
          color: #333;
          margin-bottom: 10px;
        }
        
        .status-select {
          width: 100%;
          height: 50px;
          border-radius: 8px;
          
          :deep(.el-input__wrapper) {
            padding: 0 16px;
            
            .el-input__inner {
              height: 50px;
              font-size: 16px;
            }
          }
        }
        
        .days-input {
          width: 100%;
          
          :deep(.el-input__wrapper) {
            padding: 0 16px;
            height: 50px;
            border-radius: 8px;
            
            .el-input__inner {
              height: 50px;
              font-size: 16px;
            }
          }
        }
      }
      
      .preview-image-container {
        margin-top: 20px;
        
        .preview-image-uploader {
          border: 1px dashed #dcdfe6;
          border-radius: 8px;
          padding: 20px;
          text-align: center;
          background-color: #f9f9f9;
          position: relative;
          
          .preview-image {
            max-width: 100%;
            max-height: 300px;
            margin-bottom: 20px;
          }
          
          .empty-preview {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            
            .image-icon {
              font-size: 60px;
              color: #c0c4cc;
            }
          }
          
          .upload-button-container {
            margin-top: 20px;
            
            .upload-button {
              width: 150px;
              height: 40px;
              border-radius: 4px;
            }
          }
        }
      }
    }
  }
  
  // Override Element Plus default styles
  :deep(.el-select-dropdown__item) {
    height: 40px;
    line-height: 40px;
    font-size: 16px;
  }
  
  :deep(.el-select-dropdown.status-select-dropdown) {
    border-radius: 8px;
  }
  </style>