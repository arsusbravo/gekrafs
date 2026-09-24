<script setup>
const props = defineProps({
    meta: { type: Object, required: true },
});

const emit = defineEmits(['change']);
</script>

<template>
    <div v-if="props.meta.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-4 py-3 text-sm text-gray-600">
        <p>Showing {{ props.meta.from }}–{{ props.meta.to }} of {{ props.meta.total }}</p>
        <div class="flex gap-2">
            <button type="button" class="btn btn-secondary" :disabled="props.meta.current_page <= 1" @click="emit('change', props.meta.current_page - 1)">Previous</button>
            <button type="button" class="btn btn-secondary" :disabled="props.meta.current_page >= props.meta.last_page" @click="emit('change', props.meta.current_page + 1)">Next</button>
        </div>
    </div>
</template>
