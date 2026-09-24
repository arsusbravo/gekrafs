<script setup>
import { onBeforeUnmount, watch } from 'vue';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import { Placeholder } from '@tiptap/extensions';

const props = defineProps({
    id: { type: String, default: null },
    placeholder: { type: String, default: 'Start writing…' },
    invalid: { type: Boolean, default: false },
});

const model = defineModel({ type: String, default: '' });

const editor = useEditor({
    content: model.value,
    extensions: [
        StarterKit.configure({
            heading: { levels: [2, 3] },
            code: false,
            codeBlock: false,
            link: {
                openOnClick: false,
                defaultProtocol: 'https',
                HTMLAttributes: { rel: 'noopener noreferrer', target: null },
            },
        }),
        Placeholder.configure({ placeholder: props.placeholder }),
    ],
    editorProps: {
        attributes: {
            id: props.id ?? '',
            class: 'prose-gekrafs-editor min-h-72 px-4 py-3 focus:outline-none',
        },
    },
    onUpdate: ({ editor }) => {
        // An empty editor still contains "<p></p>"; send an empty string so "required" validation works.
        model.value = editor.isEmpty ? '' : editor.getHTML();
    },
});

// Content is loaded asynchronously on edit pages.
watch(model, (value) => {
    if (editor.value && value !== (editor.value.isEmpty ? '' : editor.value.getHTML())) {
        editor.value.commands.setContent(value || '', { emitUpdate: false });
    }
});

onBeforeUnmount(() => editor.value?.destroy());

function setLink() {
    const previous = editor.value.getAttributes('link').href ?? '';
    const url = window.prompt('Link URL (leave empty to remove the link)', previous);

    if (url === null) return;

    if (url.trim() === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }

    editor.value.chain().focus().extendMarkRange('link').setLink({ href: url.trim() }).run();
}

const groups = [
    [
        { label: 'Bold', icon: 'B', class: 'font-bold', action: (e) => e.chain().focus().toggleBold().run(), active: (e) => e.isActive('bold') },
        { label: 'Italic', icon: 'I', class: 'italic font-serif', action: (e) => e.chain().focus().toggleItalic().run(), active: (e) => e.isActive('italic') },
        { label: 'Underline', icon: 'U', class: 'underline', action: (e) => e.chain().focus().toggleUnderline().run(), active: (e) => e.isActive('underline') },
        { label: 'Strikethrough', icon: 'S', class: 'line-through', action: (e) => e.chain().focus().toggleStrike().run(), active: (e) => e.isActive('strike') },
    ],
    [
        { label: 'Heading', icon: 'H2', class: 'font-bold', action: (e) => e.chain().focus().toggleHeading({ level: 2 }).run(), active: (e) => e.isActive('heading', { level: 2 }) },
        { label: 'Subheading', icon: 'H3', class: 'font-bold', action: (e) => e.chain().focus().toggleHeading({ level: 3 }).run(), active: (e) => e.isActive('heading', { level: 3 }) },
    ],
    [
        { label: 'Bullet list', svg: 'M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z', action: (e) => e.chain().focus().toggleBulletList().run(), active: (e) => e.isActive('bulletList') },
        { label: 'Numbered list', svg: 'M8.242 5.992h12m-12 6.003h12m-12 5.999h12M4.117 7.495v-3.75H2.99m1.125 3.75H2.99m1.125 0H5.24m-1.92 2.577a1.125 1.125 0 111.591 1.59l-1.83 1.83h2.16M2.99 15.745h1.125a1.125 1.125 0 010 2.25H3.74m0-.002h.375a1.125 1.125 0 010 2.25H2.99', action: (e) => e.chain().focus().toggleOrderedList().run(), active: (e) => e.isActive('orderedList') },
        { label: 'Quote', svg: 'M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z', action: (e) => e.chain().focus().toggleBlockquote().run(), active: (e) => e.isActive('blockquote') },
        { label: 'Link', svg: 'M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244', action: () => setLink(), active: (e) => e.isActive('link') },
        { label: 'Divider line', svg: 'M3.75 12h16.5', action: (e) => e.chain().focus().setHorizontalRule().run() },
    ],
    [
        { label: 'Undo', svg: 'M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3', action: (e) => e.chain().focus().undo().run(), disabled: (e) => !e.can().undo() },
        { label: 'Redo', svg: 'M15 15l6-6m0 0l-6-6m6 6H9a6 6 0 000 12h3', action: (e) => e.chain().focus().redo().run(), disabled: (e) => !e.can().redo() },
        { label: 'Clear formatting', svg: 'M6 18L18 6M6 6l12 12', action: (e) => e.chain().focus().unsetAllMarks().clearNodes().run() },
    ],
];
</script>

<template>
    <div
        :class="[
            'rounded-lg border bg-white shadow-xs focus-within:ring-2',
            props.invalid ? 'border-red-400 focus-within:ring-red-500/20' : 'border-gray-300 focus-within:border-brand-500 focus-within:ring-brand-500/20',
        ]"
    >
        <div v-if="editor" class="sticky top-16 z-10 flex flex-wrap items-center gap-1 rounded-t-lg border-b border-gray-200 bg-gray-50 px-2 py-1.5">
            <template v-for="(group, index) in groups" :key="index">
                <span v-if="index > 0" class="mx-1 h-5 w-px bg-gray-300" />
                <button
                    v-for="button in group"
                    :key="button.label"
                    type="button"
                    :title="button.label"
                    :aria-label="button.label"
                    :aria-pressed="button.active ? button.active(editor) : undefined"
                    :disabled="button.disabled ? button.disabled(editor) : false"
                    :class="[
                        'flex h-8 min-w-8 items-center justify-center rounded-md px-1.5 text-sm transition disabled:cursor-not-allowed disabled:opacity-30',
                        button.active?.(editor) ? 'bg-brand-600 text-white' : 'text-gray-700 hover:bg-gray-200',
                    ]"
                    @click="button.action(editor)"
                >
                    <svg v-if="button.svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="button.svg" />
                    </svg>
                    <span v-else :class="button.class">{{ button.icon }}</span>
                </button>
            </template>
        </div>

        <EditorContent :editor="editor" />
    </div>
</template>

<style>
/* Placeholder text for the empty editor (Tiptap Placeholder extension). */
.tiptap p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    float: left;
    height: 0;
    pointer-events: none;
    color: var(--color-gray-400);
}
</style>
