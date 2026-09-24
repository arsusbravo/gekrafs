<script setup>
import { toasts, dismiss } from '../toast';
</script>

<template>
    <div class="pointer-events-none fixed right-4 bottom-4 z-50 flex w-full max-w-sm flex-col gap-2">
        <TransitionGroup
            enter-from-class="translate-y-2 opacity-0"
            enter-active-class="transition duration-200"
            leave-to-class="opacity-0"
            leave-active-class="transition duration-150"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                :class="[
                    'pointer-events-auto flex items-start justify-between gap-3 rounded-lg px-4 py-3 text-sm shadow-lg',
                    toast.type === 'error' ? 'bg-red-600 text-white' : 'bg-gray-900 text-white',
                ]"
            >
                <span>{{ toast.message }}</span>
                <button type="button" class="opacity-70 hover:opacity-100" @click="dismiss(toast.id)">&times;</button>
            </div>
        </TransitionGroup>
    </div>
</template>
