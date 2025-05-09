<template>
    <div class="form-builder">
      <div class="form-builder-sections">
        <div v-for="section in sections" :key="section.id" class="section">
          <!-- Section Header -->
          <div class="section-header" @click="toggleSectionExpand(section.id)">
            <div class="section-title">{{ section.title }}</div>
            <div class="section-actions">
              <el-icon class="section-icon"><Setting /></el-icon>
              <span class="options-text">Options</span>
              <el-icon class="section-icon">
                <component :is="section.expanded ? 'ArrowUp' : 'ArrowDown'" />
              </el-icon>
            </div>
          </div>
          
          <!-- Section Content -->
          <div v-if="section.expanded" class="section-content">
            <div 
              v-for="field in section.fields" 
              :key="field.id" 
              class="field-item"
              :class="{ 'field-selected': selectedField === field.id }"
              @click="setSelectedField(field.id)"
            >
              <!-- Field Header -->
              <div class="field-header" @click.stop="toggleFieldExpand(field.id)">
                <div class="field-title">
                  <el-icon v-if="field.icon" class="field-icon">
                    <component :is="field.icon" />
                  </el-icon>
                  <span>{{ field.label }}</span>
                </div>
                <el-icon>
                  <component :is="field.expanded ? 'ArrowUp' : 'ArrowDown'" />
                </el-icon>
              </div>
              
              <!-- Field Editor -->
              <div v-if="field.expanded" class="field-editor">
                <div class="field-property">
                  <div class="property-label">Label</div>
                  <el-input v-model="field.label" placeholder="Enter label"></el-input>
                </div>
                
                <div class="field-property">
                  <div class="property-label">Placeholder</div>
                  <el-input v-model="field.placeholder" placeholder="Enter placeholder"></el-input>
                </div>
                
                <div class="field-property field-required">
                  <div class="property-label">Required</div>
                  <el-switch v-model="field.required" active-color="#409EFF"></el-switch>
                </div>
                
                <!-- Options for radio/select/checkbox fields -->
                <div v-if="hasOptions(field.type)" class="field-options">
                  <div class="options-header">
                    <div class="property-label">Options</div>
                    <el-button type="text" @click="addFieldOption(field.id)" class="add-option-btn">
                      <el-icon><Plus /></el-icon> Add Option
                    </el-button>
                  </div>
                  
                  <div v-for="option in field.options" :key="option.id" class="option-item">
                    <el-icon class="drag-handle"><Rank /></el-icon>
                    <el-input v-model="option.label" placeholder="Option label"></el-input>
                    <el-button 
                      type="text" 
                      @click="removeFieldOption(field.id, option.id)"
                      class="remove-option-btn"
                    >
                      <el-icon><Delete /></el-icon>
                    </el-button>
                  </div>
                </div>
              </div>
              
              <!-- Field Preview -->
              <div v-if="field.expanded" class="field-preview">
                <!-- Text input preview -->
                <el-input 
                  v-if="['text', 'email', 'address'].includes(field.type)"
                  :placeholder="field.placeholder"
                ></el-input>
                
                <!-- Radio preview -->
                <el-radio-group v-else-if="field.type === 'radio'">
                  <el-radio v-for="option in field.options" :key="option.id" :label="option.value">
                    {{ option.label }}
                  </el-radio>
                </el-radio-group>
                
                <!-- Select preview -->
                <el-select 
                  v-else-if="field.type === 'select'" 
                  :placeholder="field.placeholder"
                  style="width: 100%;"
                >
                  <el-option 
                    v-for="option in field.options" 
                    :key="option.id" 
                    :label="option.label" 
                    :value="option.value"
                  ></el-option>
                </el-select>
                
                <!-- Checkbox preview -->
                <el-checkbox-group v-else-if="field.type === 'checkbox'">
                  <div v-for="option in field.options" :key="option.id" class="checkbox-item">
                    <el-checkbox :label="option.value">{{ option.label }}</el-checkbox>
                  </div>
                </el-checkbox-group>
                
                <!-- Image preview -->
                <el-upload
                  v-else-if="field.type === 'image'"
                  action="#"
                  list-type="picture-card"
                  :auto-upload="false"
                >
                  <el-icon><Plus /></el-icon>
                </el-upload>
              </div>
            </div>
            
            <!-- Add Field Buttons -->
            <div class="add-field-buttons">
              <el-button @click="addField(section.id, 'text')">Add Text Field</el-button>
              <el-button @click="addField(section.id, 'radio')">Add Radio Field</el-button>
              <el-button @click="addField(section.id, 'select')">Add Select Field</el-button>
              <el-button @click="addField(section.id, 'checkbox')">Add Checkbox Field</el-button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  
  export default {
    name: 'FormBuilder',
    components: {
    },
    data() {
      return {
        sections: [
          {
            id: 'general',
            title: 'General Section',
            expanded: true,
            fields: [
              {
                id: 'field1',
                type: 'text',
                icon: '',
                label: 'Title',
                placeholder: 'Enter title',
                required: true,
                expanded: true,
                options: []
              },
              {
                id: 'field2',
                type: 'address',
                icon: 'Location',
                label: 'Address',
                placeholder: 'Enter address',
                required: false,
                expanded: false,
                options: []
              },
              {
                id: 'field3',
                type: 'image',
                icon: 'Picture',
                label: 'Images',
                placeholder: 'Upload images',
                required: false,
                expanded: false,
                options: []
              },
              {
                id: 'field4',
                type: 'email',
                icon: 'Message',
                label: 'Email',
                placeholder: 'Enter email',
                required: false,
                expanded: false,
                options: []
              }
            ]
          }
        ],
        selectedField: 'field1'
      }
    },
    methods: {
      toggleSectionExpand(sectionId) {
        this.sections = this.sections.map(section => 
          section.id === sectionId 
            ? { ...section, expanded: !section.expanded } 
            : section
        )
      },
      
      toggleFieldExpand(fieldId) {
        this.sections = this.sections.map(section => ({
          ...section,
          fields: section.fields.map(field => 
            field.id === fieldId 
              ? { ...field, expanded: !field.expanded } 
              : field
          )
        }))
      },
      
      setSelectedField(fieldId) {
        this.selectedField = fieldId
      },
      
      hasOptions(fieldType) {
        return ['radio', 'select', 'checkbox'].includes(fieldType)
      },
      
      addFieldOption(fieldId) {
        this.sections = this.sections.map(section => ({
          ...section,
          fields: section.fields.map(field => {
            if (field.id === fieldId) {
              const optionCount = field.options.length + 1
              return { 
                ...field, 
                options: [
                  ...field.options, 
                  { 
                    id: `option-${optionCount}`, 
                    label: `Option ${optionCount}`, 
                    value: `option-${optionCount}` 
                  }
                ] 
              }
            }
            return field
          })
        }))
      },
      
      removeFieldOption(fieldId, optionId) {
        this.sections = this.sections.map(section => ({
          ...section,
          fields: section.fields.map(field => 
            field.id === fieldId 
              ? { 
                  ...field, 
                  options: field.options.filter(option => option.id !== optionId)
                } 
              : field
          )
        }))
      },
      
      addField(sectionId, type) {
        const newField = {
          id: `field-${Date.now()}`,
          type: type || 'text',
          icon: this.getIconForType(type),
          label: 'New Field',
          placeholder: 'Enter value',
          required: false,
          expanded: true,
          options: this.hasOptions(type) ? [
            { id: 'option-1', label: 'Option 1', value: 'option-1' },
            { id: 'option-2', label: 'Option 2', value: 'option-2' }
          ] : []
        }
        
        this.sections = this.sections.map(section => 
          section.id === sectionId 
            ? { 
                ...section, 
                fields: [...section.fields, newField] 
              } 
            : section
        )
        
        this.selectedField = newField.id
      },
      
      getIconForType(type) {
        const iconMap = {
          'address': 'Location',
          'image': 'Picture',
          'email': 'Message'
        }
        return iconMap[type] || ''
      }
    }
  }
  </script>
  
  <style lang="scss">
  .form-builder {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f5f7fa;
    min-height: 100vh;
    border-radius: 8px;
    .form-builder-sections {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    
    .section {
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
      background-color: #fff;
      
      .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background-color: #1c1e21;
        color: white;
        cursor: pointer;
        
        .section-title {
          font-weight: 500;
        }
        
        .section-actions {
          display: flex;
          align-items: center;
          
          .section-icon {
            margin-left: 8px;
          }
          
          .options-text {
            margin-left: 8px;
            font-size: 14px;
          }
        }
      }
      
      .section-content {
        padding: 16px;
        
        .field-item {
          margin-bottom: 16px;
          border: 1px solid #e4e7ed;
          border-radius: 8px;
          overflow: hidden;
          transition: all 0.3s;
          
          &.field-selected {
            border-color: #409EFF;
            box-shadow: 0 0 0 2px rgba(64, 158, 255, 0.2);
          }
          
          .field-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background-color: #f5f7fa;
            cursor: pointer;
            
            .field-title {
              display: flex;
              align-items: center;
              
              .field-icon {
                margin-right: 8px;
              }
            }
          }
          
          .field-editor {
            padding: 16px;
            border-bottom: 1px solid #e4e7ed;
            
            .field-property {
              margin-bottom: 16px;
              
              .property-label {
                font-size: 14px;
                margin-bottom: 8px;
                color: #606266;
              }
            }
            
            .field-required {
              display: flex;
              justify-content: space-between;
              align-items: center;
            }
            
            .field-options {
              .options-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 8px;
                
                .add-option-btn {
                  padding: 0;
                  color: #409EFF;
                  
                  .el-icon {
                    margin-right: 4px;
                  }
                }
              }
              
              .option-item {
                display: flex;
                align-items: center;
                margin-bottom: 8px;
                
                .drag-handle {
                  margin-right: 8px;
                  color: #909399;
                  cursor: move;
                }
                
                .el-input {
                  flex: 1;
                }
                
                .remove-option-btn {
                  padding: 0;
                  margin-left: 8px;
                  color: #909399;
                  
                  &:hover {
                    color: #f56c6c;
                  }
                }
              }
            }
          }
          
          .field-preview {
            padding: 16px;
            
            .checkbox-item {
              margin-bottom: 8px;
            }
          }
        }
        
        .add-field-buttons {
          display: flex;
          flex-wrap: wrap;
          gap: 8px;
          margin-top: 16px;
        }
      }
    }
  }
  </style>