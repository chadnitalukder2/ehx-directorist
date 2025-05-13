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

    tinymce.init({
      selector: `#${this.editorId}`,
      setup: (editor) => {
        this.editorInstance = editor;

        editor.on('init', () => {
          editor.setContent(this.modelValue || '');
        });

        editor.on('Change KeyUp', () => {
          const content = editor.getContent();
          if (content !== this.modelValue) {
            this.$emit('update:modelValue', content);
            this.$emit('change');
          }
        });
      }
    });

  },
  methods: {
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
<style>

</style>