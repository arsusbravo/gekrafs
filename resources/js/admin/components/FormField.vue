<script setup>
const props = defineProps({
    label: { type: String, required: true },
    id: { type: String, required: true },
    error: { type: [Array, String], default: null },
    hint: { type: String, default: null },
});
</script>

<template>
    <div>
        <label :for="props.id" class="form-label">{{ props.label }}</label>
        <slot />
        <p v-if="props.hint && !props.error" class="mt-1 text-xs text-gray-500">{{ props.hint }}</p>
        <p v-if="props.error" class="form-error">{{ Array.isArray(props.error) ? props.error[0] : props.error }}</p>
    </div>
</template>
