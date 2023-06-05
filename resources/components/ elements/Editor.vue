<template>
    <div class="editor">
        <div
            v-if="editor"
            class="header">
            <button
                :disabled="!editor.can().chain().focus().toggleBold().run()"
                :class="{ 'is-active': editor.isActive('bold') }"
                @click="editor.chain().focus().toggleBold().run()">
                <FormatBoldIcon :size="18" />
            </button>
            <button
                :disabled="!editor.can().chain().focus().toggleItalic().run()"
                :class="{ 'is-active': editor.isActive('italic') }"
                @click="editor.chain().focus().toggleItalic().run()">
                <FormatItalicIcon :size="18" />
            </button>
            <button
                :disabled="!editor.can().chain().focus().toggleStrike().run()"
                :class="{ 'is-active': editor.isActive('strike') }"
                @click="editor.chain().focus().toggleStrike().run()">
                <FormatStrikethroughVariantIcon :size="18" />
            </button>
            <button
                :disabled="!editor.can().chain().focus().toggleHeading({ level: 1 }).run()"
                :class="{ 'is-active':editor.isActive('heading', { level: 1 }) }"
                @click="editor.chain().focus().toggleHeading({ level: 1 }).run()">
                <FormatHeader1Icon :size="18" />
            </button>
            <button
                :disabled="!editor.can().chain().focus().toggleHeading({ level: 2 }).run()"
                :class="{ 'is-active': editor.isActive('heading', { level: 2 })}"
                @click="editor.chain().focus().toggleHeading({ level: 2 }).run()">
                <FormatHeader2Icon :size="18" />
            </button>
            <button
                :disabled="!editor.can().chain().focus().toggleHeading({ level: 3 }).run()"
                :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }"
                @click="editor.chain().focus().toggleHeading({ level: 3 }).run()">
                <FormatHeader3Icon :size="18" />
            </button>
            <button
                :class="{ 'is-active': editor.isActive('bulletList') }"
                @click="editor.chain().focus().toggleBulletList().run()">
                <FormatListBulletedSquareIcon :size="18" />
            </button>
            <button
                :class="{ 'is-active': editor.isActive('orderedList') }"
                @click="editor.chain().focus().toggleOrderedList().run()">
                <FormatListNumberedIcon :size="18" />
            </button>
        </div>
        <div
            data-simplebar
            class="hd_editor">
            <EditorContent
                :editor="editor" />
        </div>
    </div>
</template>

<script>
import FormatBoldIcon from 'vue-material-design-icons/FormatBold.vue'
import FormatItalicIcon from 'vue-material-design-icons/FormatItalic.vue'
import FormatStrikethroughVariantIcon from 'vue-material-design-icons/FormatStrikethroughVariant.vue'
import FormatHeader1Icon from 'vue-material-design-icons/FormatHeader1.vue'
import FormatHeader2Icon from 'vue-material-design-icons/FormatHeader2.vue'
import FormatHeader3Icon from 'vue-material-design-icons/FormatHeader3.vue'
import FormatListBulletedSquareIcon from 'vue-material-design-icons/FormatListBulletedSquare.vue'
import FormatListNumberedIcon from 'vue-material-design-icons/FormatListNumbered.vue'
import StarterKit from '@tiptap/starter-kit'
import { Editor, EditorContent } from '@tiptap/vue-3'

export default {
    name: 'Editor',
    components: {
        EditorContent,
        FormatBoldIcon,
        FormatItalicIcon,
        FormatStrikethroughVariantIcon,
        FormatHeader1Icon,
        FormatHeader2Icon,
        FormatHeader3Icon,
        FormatListBulletedSquareIcon,
        FormatListNumberedIcon
    },
    emits: [ 'on-update' ],
    data() {
        return {
            editor: null,
            value: ''
        }
    },
    created() {
        const that = this
        this.editor = new Editor({
            onUpdate({ editor }) {
                that.$emit('on-update', editor.getHTML())
            },
            extensions: [
                StarterKit
            ],
            content: this.value
        })
    },
    beforeUnmount() {
        this.editor.destroy()
    }
}
</script>

<style lang="scss" scoped>
.header {
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    padding: calc(var(--padding-box) / 2);
    background: var(--bs-border-color);

    button {
        padding: 4px;
        background: none;
        border: none;
        border-radius: var(--border-radius);
        margin-right: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition-duration);

        .material-design-icon {
            position: relative;
            top: -1px
        }

        &:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        &.is-active {
            background: var(--bs-purple-hover);
            color: var(--bs-white)
        }

        &:last-child {
            margin-right: 0;
        }
    }
}

.hd_editor {
    height: 200px;
    padding: var(--padding-box);
    border: 1px solid var(--bs-border-color);
    border-radius: 0 0 var(--border-radius) var(--border-radius);

    ::v-deep(.ProseMirror) {
        outline: none;


        > * + * {
            margin-top: 0.55em;
        }
    }
}
</style>
