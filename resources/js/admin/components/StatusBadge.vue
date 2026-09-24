<script setup>
const props = defineProps({
    active: { type: Boolean, default: false },
    activeLabel: { type: String, default: 'Published' },
    inactiveLabel: { type: String, default: 'Draft' },
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
            props.active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700',
        ]"
    >
        {{ props.active ? props.activeLabel : props.inactiveLabel }}
    </span>
</template>
