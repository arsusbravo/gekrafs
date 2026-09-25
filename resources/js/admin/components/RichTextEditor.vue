<script setup>
import { onBeforeUnmount, reactive, ref, watch } from 'vue';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import { Placeholder } from '@tiptap/extensions';
import api from '../api';
import { notify } from '../toast';
import { errorMessage } from '../utils';

const props = defineProps({
    id: { type: String, default: null },
    placeholder: { type: String, default: 'Start writing…' },
    invalid: { type: Boolean, default: false },
});

const model = defineModel({ type: String, default: '' });

const fileInput = ref(null);
const uploading = ref(0);

// "Insert image" dialog: upload a file or use a link.
const imageDialog = reactive({ open: false, tab: 'upload', url: '', alt: '', error: '', checking: false });

// Width presets for the selected image; null means the full width of the text.
const imageSizes = [
    { label: 'Small', width: 240 },
    { label: 'Medium', width: 420 },
    { label: 'Large', width: 640 },
    { label: 'Full width', width: null },
];

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
        Image.configure({
            allowBase64: false,
            resize: {
                enabled: true,
                directions: ['top-left', 'top-right', 'bottom-left', 'bottom-right'],
                minWidth: 80,
                minHeight: 40,
                alwaysPreserveAspectRatio: true,
            },
        }),
        Placeholder.configure({ placeholder: props.placeholder }),
    ],
    editorProps: {
        attributes: {
            id: props.id ?? '',
            class: 'prose-gekrafs-editor min-h-72 px-4 py-3 focus:outline-none',
        },
        // Dropped or pasted image files are uploaded instead of being embedded as data.
        handleDrop: (view, event, slice, moved) => {
            const files = imageFiles(event.dataTransfer?.files);

            if (moved || !files.length) {
                return false;
            }

            event.preventDefault();
            const position = view.posAtCoords({ left: event.clientX, top: event.clientY })?.pos;
            uploadImages(files, position);

            return true;
        },
        handlePaste: (view, event) => {
            const files = imageFiles(event.clipboardData?.files);

            if (!files.length) {
                return false;
            }

            event.preventDefault();
            uploadImages(files);

            return true;
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

function imageFiles(fileList) {
    return Array.from(fileList ?? []).filter((file) => file.type.startsWith('image/'));
}

async function uploadImages(files, position = null, alt = '') {
    for (const file of files) {
        uploading.value++;

        try {
            const form = new FormData();
            form.append('image', file);
            const { data } = await api.post('/images', form);
            const image = { type: 'image', attrs: { src: data.url, alt } };
            const chain = editor.value.chain().focus();

            (position === null ? chain.insertContent(image) : chain.insertContentAt(position, image)).run();

            // Further images from the same drop follow the first one (the cursor is now after it).
            position = null;
        } catch (error) {
            notify(`${file.name}: ${errorMessage(error)}`, 'error');
        } finally {
            uploading.value--;
        }
    }
}

function openImageDialog() {
    Object.assign(imageDialog, { open: true, tab: 'upload', url: '', alt: '', error: '', checking: false });
}

function closeImageDialog() {
    imageDialog.open = false;
    editor.value?.commands.focus();
}

function chooseImage() {
    fileInput.value?.click();
}

function onFileChosen(event) {
    const files = imageFiles(event.target.files);
    event.target.value = '';

    if (!files.length) {
        return;
    }

    const alt = imageDialog.alt.trim();
    imageDialog.open = false;
    uploadImages(files, null, alt);
}

function imageLoads(url) {
    return new Promise((resolve) => {
        const probe = new window.Image();
        probe.onload = () => resolve(probe.naturalWidth > 0);
        probe.onerror = () => resolve(false);
        probe.src = url;
    });
}

async function insertImageFromLink() {
    const url = imageDialog.url.trim();
    imageDialog.error = '';

    // The website only shows images from this site or from https links (see App\Support\RichText).
    if (!/^https:\/\/\S+$/i.test(url) && !url.startsWith('/')) {
        imageDialog.error = 'Use a link that starts with https://';
        return;
    }

    imageDialog.checking = true;
    const loads = await imageLoads(url);
    imageDialog.checking = false;

    if (!loads) {
        imageDialog.error = 'No image could be loaded from this link.';
        return;
    }

    editor.value.chain().focus().setImage({ src: url, alt: imageDialog.alt.trim() }).run();
    imageDialog.open = false;
}

function setImageAlt(alt) {
    editor.value.chain().updateAttributes('image', { alt }).run();
}

function setImageWidth(width) {
    const position = editor.value.state.selection.from;
    editor.value.chain().focus().updateAttributes('image', { width, height: null }).setNodeSelection(position).run();

    // The resizable image only restyles itself while dragging, so apply the preset directly.
    const img = editor.value.view.nodeDOM(position)?.querySelector?.('img');

    if (img) {
        img.style.width = width ? `${width}px` : '';
        img.style.height = '';
    }
}

function removeImage() {
    editor.value.chain().focus().deleteSelection().run();
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
        { label: 'Image', svg: 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z', action: () => openImageDialog(), disabled: () => uploading.value > 0 },
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

            <span v-if="uploading" class="ml-auto flex items-center gap-2 px-2 text-xs font-medium text-brand-700">
                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
                    <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                </svg>
                Uploading image…
            </span>
            <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/gif,image/webp" multiple class="hidden" @change="onFileChosen" />

            <!-- Settings for the selected image -->
            <div v-if="editor.isActive('image')" class="flex w-full flex-wrap items-center gap-2 border-t border-gray-200 pt-1.5 text-xs">
                <label class="flex min-w-56 flex-1 items-center gap-2">
                    <span class="font-medium text-gray-600">Alt text</span>
                    <input
                        type="text"
                        class="form-input py-1 text-xs"
                        placeholder="Describe the image for screen readers"
                        :value="editor.getAttributes('image').alt ?? ''"
                        @input="setImageAlt($event.target.value)"
                        @keydown.enter.prevent
                    />
                </label>
                <div class="flex items-center gap-1">
                    <span class="mr-1 font-medium text-gray-600">Size</span>
                    <button
                        v-for="size in imageSizes"
                        :key="size.label"
                        type="button"
                        :class="[
                            'rounded-md px-2 py-1 font-medium transition',
                            (editor.getAttributes('image').width ?? null) === size.width ? 'bg-brand-600 text-white' : 'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-100',
                        ]"
                        @click="setImageWidth(size.width)"
                    >
                        {{ size.label }}
                    </button>
                </div>
                <button type="button" class="rounded-md px-2 py-1 font-medium text-red-600 hover:bg-red-50" @click="removeImage">Remove</button>
                <span class="w-full text-gray-500">Tip: drag a corner of the image to resize it freely.</span>
            </div>
        </div>

        <EditorContent :editor="editor" />

        <!-- Insert image: upload or link -->
        <div v-if="imageDialog.open" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4" @click.self="closeImageDialog" @keydown.esc="closeImageDialog">
            <div class="w-full max-w-md rounded-xl bg-white shadow-xl" role="dialog" aria-modal="true" aria-labelledby="image-dialog-title">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                    <h2 id="image-dialog-title" class="font-semibold">Insert image</h2>
                    <button type="button" class="rounded-md p-1 text-gray-500 hover:bg-gray-100" aria-label="Close" @click="closeImageDialog">&times;</button>
                </div>

                <div class="flex gap-1 border-b border-gray-200 px-5 pt-3 text-sm">
                    <button
                        v-for="tab in [{ key: 'upload', label: 'Upload' }, { key: 'link', label: 'From link' }]"
                        :key="tab.key"
                        type="button"
                        :class="[
                            '-mb-px border-b-2 px-3 py-2 font-medium',
                            imageDialog.tab === tab.key ? 'border-brand-600 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-700',
                        ]"
                        @click="imageDialog.tab = tab.key; imageDialog.error = ''"
                    >
                        {{ tab.label }}
                    </button>
                </div>

                <div class="space-y-4 p-5">
                    <div v-if="imageDialog.tab === 'upload'">
                        <button type="button" class="flex w-full flex-col items-center gap-2 rounded-lg border-2 border-dashed border-gray-300 px-4 py-8 text-sm text-gray-600 hover:border-brand-400 hover:bg-brand-50" @click="chooseImage">
                            <span class="font-semibold text-brand-700">Choose image(s)…</span>
                            <span class="text-xs text-gray-500">JPG, PNG, GIF or WebP, up to 4 MB. You can also drag images into the text.</span>
                        </button>
                    </div>

                    <div v-else>
                        <label for="image-link" class="form-label">Image link</label>
                        <input id="image-link" v-model="imageDialog.url" type="url" class="form-input" placeholder="https://…" @keydown.enter.prevent="insertImageFromLink" />
                    </div>

                    <div>
                        <label for="image-alt" class="form-label">Alt text <span class="font-normal text-gray-400">(recommended)</span></label>
                        <input id="image-alt" v-model="imageDialog.alt" type="text" class="form-input" placeholder="Describe the image for screen readers" />
                    </div>

                    <p v-if="imageDialog.error" class="form-error">{{ imageDialog.error }}</p>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-200 px-5 py-3">
                    <button type="button" class="btn btn-secondary" @click="closeImageDialog">Cancel</button>
                    <button v-if="imageDialog.tab === 'link'" type="button" class="btn btn-primary" :disabled="imageDialog.checking || !imageDialog.url" @click="insertImageFromLink">
                        {{ imageDialog.checking ? 'Checking…' : 'Insert image' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

