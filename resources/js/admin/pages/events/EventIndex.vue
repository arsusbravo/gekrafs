<script setup>
import Pagination from '../../components/Pagination.vue';
import StatusBadge from '../../components/StatusBadge.vue';
import { useResourceList } from '../../composables/useResourceList';
import { formatDateTime } from '../../utils';

const { items, meta, search, loading, load, destroy } = useResourceList('/events');
</script>

<template>
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <input v-model="search" type="search" placeholder="Search events…" class="form-input sm:max-w-xs" />
            <RouterLink :to="{ name: 'events.create' }" class="btn btn-primary">New event</RouterLink>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Starts</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" :class="{ 'opacity-50': loading }">
                        <tr v-for="event in items" :key="event.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">
                                <RouterLink :to="{ name: 'events.edit', params: { id: event.id } }" class="hover:text-brand-700">{{ event.title }}</RouterLink>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ formatDateTime(event.starts_at) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ event.location || '—' }}</td>
                            <td class="px-4 py-3"><StatusBadge :active="event.is_published" /></td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a :href="`/events/${event.slug}`" target="_blank" class="mr-3 text-gray-500 hover:text-gray-700">View</a>
                                <RouterLink :to="{ name: 'events.edit', params: { id: event.id } }" class="mr-3 font-medium text-brand-600 hover:text-brand-700">Edit</RouterLink>
                                <button type="button" class="font-medium text-red-600 hover:text-red-700" @click="destroy(event, event.title)">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="!loading && !items.length">
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">No events found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :meta="meta" @change="load" />
        </div>
    </div>
</template>
