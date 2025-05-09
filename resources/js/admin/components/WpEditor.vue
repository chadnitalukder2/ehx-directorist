<template>
    <div class="wp_vue_editor_wrapper">
        <popover
            v-if="editorShortcodes.length"
            class="popover-wrapper"
            :class="{'popover-wrapper-plaintext': !hasWpEditor}"
            :data="editorShortcodes"
            buttonText='Add Shortcodes <i class="el-icon-arrow-down el-icon--right"></i>'
            @command="handleCommand"></popover>
        <textarea v-if="hasWpEditor && !no_tiny_mce" class="wp_vue_editor" :id="editor_id">{{ modelValue }}</textarea>
        <el-input 
            :rows="4" 
            class="wp_vue_editor wp_vue_editor_plain" 
            v-else 
            type="textarea" 
            v-model="plain_content" 
        />
    </div>
</template>

<script>
export default {
    name: 'wp_editor',
    props: {
        editor_id: {
            type: String,
            default() {
                return 'wp_editor_' + Date.now() + parseInt(Math.random() * 1000);
            }
        },
        modelValue: {
            type: String,
            default: ''
        },
        height: Number
    },
    data() {
        return {
            hasWpEditor: !!window.wp?.editor,
            plain_content: this.modelValue,
            cursorPos: this.modelValue.length,
            no_tiny_mce: false,
            editorShortcodes: [
                {
                    name: 'First Name',
                    command: '[first_name]',
                    description: 'First Name'
                },
                {
                    name: 'Last Name',
                    command: '[last_name]',
                    description: 'Last Name'
                },
                {
                    name: 'Email',
                    command: '[email]',
                    description: 'Email'
                },
                {
                    name: 'Phone',
                    command: '[phone]',
                    description: 'Phone'
                },
                {
                    name: 'Address',
                    command: '[address]',
                    description: 'Address'
                }
            ]
        };
    },
    watch: {
        plain_content() {
            if (this.no_tiny_mce) {
                this.$emit('update:modelValue', this.plain_content);
            }
        }
    },
    methods: {
        initEditor() {
            if (!window.tinymce) {
                this.no_tiny_mce = true;
                return;
            }
            this.no_tiny_mce = false;

            wp.editor.remove(this.editor_id);

            const that = this;
            wp.editor.initialize(this.editor_id, {
                mediaButtons: true,
                tinymce: {
                    height: that.height || 300,
                    plugins: 'image,media,fullscreen,lists,link',
                    toolbar1: 'bold,italic,bullist,numlist,link,blockquote,alignleft,aligncenter,alignright,strikethrough,forecolor,codeformat,undo,redo,image,media',
                    setup(editor) {
                        editor.on('init', () => {
                            // Add a small delay to prevent the RangeAt(0) error
                            setTimeout(() => {
                                tinyMCE.get(that.editor_id).setContent(that.modelValue || '');
                                tinyMCE.execCommand('mceRepaint');
                            }, 100);
                        });
                        editor.on('change', () => {
                            that.changeContentEvent();
                        });
                    }
                },
                quicktags: true
            });

            jQuery('#' + this.editor_id).on('change', () => {
                that.changeContentEvent();
            });
        },
        changeContentEvent() {
            const content = wp.editor.getContent(this.editor_id);
            this.$emit('update:modelValue', content);
        },
        handleCommand(command) {
            if (this.hasWpEditor) {
                tinymce.activeEditor.insertContent(command);
            } else {
                const part1 = this.plain_content.slice(0, this.cursorPos);
                const part2 = this.plain_content.slice(this.cursorPos, this.plain_content.length);
                this.plain_content = part1 + command + part2;
                this.cursorPos += command.length;
            }
        },
        updateCursorPos() {
            const cursorPos = jQuery('.wp_vue_editor_plain').prop('selectionStart');
            this.cursorPos = cursorPos;
        },
        reloadEditor() {
            wp.editor.remove(this.editor_id);
            jQuery('#' + this.editor_id).val('');
            this.initEditor();
        }
    },
    mounted() {
        if (this.hasWpEditor) {
            this.initEditor();
        }
    }
}
</script>

<style lang="scss" scoped>
.wp_vue_editor {
    width: 100%;
    min-height: 100px;
}

.wp_vue_editor.wp_vue_editor_plain.el-textarea {
    margin-top: 30px;
}
</style>