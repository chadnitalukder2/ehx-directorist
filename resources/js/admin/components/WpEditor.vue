<template>
    <div>
      <textarea :id="editorId"></textarea>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      value: {
        type: String,
        default: ''
      }
    },
    data() {
      return {
        editorId: 'vue-editor-' + this._uid,
        editorInstance: null
      };
    },
    mounted() {
      tinymce.init({
        selector: `#${this.editorId}`,
        setup: (editor) => {
          this.editorInstance = editor;
  
          // Set initial content
          editor.on('init', () => {
            editor.setContent(this.value || '');
          });
  
          // Emit on change
          editor.on('Change KeyUp', () => {
            const content = editor.getContent();
            if (content !== this.value) {
              this.$emit('input-update', content);
            }
          });
        }
      });
    },
    watch: {
      value(newVal) {
        // External change — update editor
        if (this.editorInstance && this.editorInstance.getContent() !== newVal) {
          this.editorInstance.setContent(newVal || '');
        }
      }
    },
    beforeDestroy() {
      if (this.editorInstance) {
        this.editorInstance.remove();
      }
    }
  };
  </script>
  