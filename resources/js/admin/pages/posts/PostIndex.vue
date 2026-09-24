<script setup>
import Pagination from '../../components/Pagination.vue';
import StatusBadge from '../../components/StatusBadge.vue';
import { useResourceList } from '../../composables/useResourceList';
import { formatDateTime } from '../../utils';

const { items, meta, search, loading, load, destroy } = useResourceList('/posts');
</script>

<template>
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <input v-model="search" type="search" placeholder="Search posts…" class="form-input sm:max-w-xs" />
            <RouterLink :to="{ name: 'posts.create' }" class="btn btn-primary">New post</RouterLink>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Author</th>
                            <th class="px-4 py-3">Publish date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" :class="{ 'opacity-50': loading }">
                        <tr v-for="post in items" :key="post.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">
                                <RouterLink :to="{ name: 'posts.edit', params: { id: post.id } }" class="hover:text-brand-700">{{ post.title }}</RouterLink>
                                <p v-if="post.event" class="mt-0.5 text-xs font-normal text-gray-500">Report of: {{ post.event.title }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ post.user?.name ?? '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ formatDateTime(post.published_at) }}</td>
                            <td class="px-4 py-3">
                                <StatusBadge :active="post.is_published" :inactive-label="post.published_at ? 'Scheduled' : 'Draft'" />
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a v-if="post.is_published" :href="`/blog/${post.slug}`" target="_blank" class="mr-3 text-gray-500 hover:text-gray-700">View</a>
                                <RouterLink :to="{ name: 'posts.edit', params: { id: post.id } }" class="mr-3 font-medium text-brand-600 hover:text-brand-700">Edit</RouterLink>
                                <button type="button" class="font-medium text-red-600 hover:text-red-700" @click="destroy(post, post.title)">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="!loading && !items.length">
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">No posts found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :meta="meta" @change="load" />
        </div>
    </div>
</template>
