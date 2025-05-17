<template>
  <div>
    <textarea :id="editorId"></textarea>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue', 'change'],
  data() {
    return {
      editorId: 'vue-editor-' + this._uid,
      editorInstance: null
    };
  },
  mounted() {
    if (window.tinymce) {
      this.initEditor();
    } else {
      const check = setInterval(() => {
        if (window.tinymce) {
          clearInterval(check);
          this.initEditor();
        }
      }, 100);
    }
  },
  methods: {
    initEditor() {
      tinymce.init({
        selector: `#${this.editorId}`,
        menubar: false,
        toolbar: false, // no formatting options
        plugins: 'paste',
        branding: false,
        statusbar: false,
        forced_root_block: false,
        valid_elements: '', // allow no elements
        invalid_elements: '*', // disallow all tags

        paste_as_text: true,
        paste_remove_styles: true,
        paste_remove_spans: true,
        paste_strip_class_attributes: 'all',

        setup: (editor) => {
          this.editorInstance = editor;

          editor.on('init', () => {
            const plain = this.stripTags(this.modelValue || '');
            editor.setContent(plain);
          });

          editor.on('Change KeyUp Paste', () => {
            const rawContent = editor.getContent();
            const plainText = this.stripTags(rawContent);
            if (plainText !== this.modelValue) {
              this.$emit('update:modelValue', plainText);
              this.$emit('change');
            }
          });
        }
      });
    },
    stripTags(html) {
      const div = document.createElement('div');
      div.innerHTML = html;
      return div.textContent || div.innerText || '';
    },
    clearEditor() {
      if (this.editorInstance) {
        this.editorInstance.setContent('');
        this.$emit('update:modelValue', '');
        this.$emit('change');
      }
    }
  },
  watch: {
    modelValue(newVal) {
      if (this.editorInstance && this.editorInstance.getContent() !== newVal) {
        this.editorInstance.setContent(this.stripTags(newVal || ''));
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

<style scoped>
textarea {
  width: 100%;
  min-height: 200px;
}
#tinymce{
  color: #606266;
}
</style>
