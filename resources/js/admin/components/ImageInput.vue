<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';

const props = defineProps({
    currentUrl: { type: String, default: null },
    error: { type: [Array, String], default: null },
});

const file = defineModel('file', { default: null });
const remove = defineModel('remove', { default: false });

const previewUrl = ref(null);
const input = ref(null);

const shownUrl = computed(() => previewUrl.value ?? (remove.value ? null : props.currentUrl));

function onChange(event) {
    const selected = event.target.files[0] ?? null;
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = selected ? URL.createObjectURL(selected) : null;
    file.value = selected;
    remove.value = false;
}

function clear() {
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = null;
    file.value = null;
    remove.value = !!props.currentUrl;
    if (input.value) input.value.value = '';
}

onBeforeUnmount(() => previewUrl.value && URL.revokeObjectURL(previewUrl.value));
</script>

<template>
    <div>
        <span class="form-label">Image</span>
        <div v-if="shownUrl" class="mb-3">
            <img :src="shownUrl" alt="" class="max-h-48 rounded-lg border border-gray-200 object-cover" />
            <button type="button" class="mt-2 text-sm font-medium text-red-600 hover:text-red-700" @click="clear">Remove image</button>
        </div>
        <input
            ref="input"
            type="file"
            accept="image/*"
            class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100"
            @change="onChange"
        />
        <p v-if="props.error" class="form-error">{{ Array.isArray(props.error) ? props.error[0] : props.error }}</p>
    </div>
</template>
