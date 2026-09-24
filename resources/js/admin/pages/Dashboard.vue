<script setup>
import { onMounted, ref } from 'vue';
import api from '../api';
import StatusBadge from '../components/StatusBadge.vue';
import { formatDateTime } from '../utils';

const data = ref(null);

onMounted(async () => {
    ({ data: data.value } = await api.get('/dashboard'));
});
</script>

<template>
    <div v-if="!data" class="text-sm text-gray-500">Loading…</div>

    <div v-else class="space-y-8">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <p class="text-sm text-gray-500">Upcoming events</p>
                <p class="mt-1 text-3xl font-semibold">{{ data.stats.upcoming_events }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ data.stats.events }} total</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <p class="text-sm text-gray-500">Published posts</p>
                <p class="mt-1 text-3xl font-semibold">{{ data.stats.published_posts }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ data.stats.posts }} total</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <p class="text-sm text-gray-500">Users</p>
                <p class="mt-1 text-3xl font-semibold">{{ data.stats.users }}</p>
            </div>
            <div class="flex flex-col justify-center gap-2 rounded-xl border border-gray-200 bg-white p-5">
                <RouterLink :to="{ name: 'events.create' }" class="btn btn-primary">New event</RouterLink>
                <RouterLink :to="{ name: 'posts.create' }" class="btn btn-secondary">New post</RouterLink>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-xl border border-gray-200 bg-white">
                <header class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                    <h2 class="font-semibold">Next events</h2>
                    <RouterLink :to="{ name: 'events.index' }" class="text-sm font-medium text-brand-600 hover:text-brand-700">View all</RouterLink>
                </header>
                <ul class="divide-y divide-gray-100">
                    <li v-for="event in data.next_events" :key="event.id">
                        <RouterLink :to="{ name: 'events.edit', params: { id: event.id } }" class="flex items-center justify-between gap-4 px-5 py-3 hover:bg-gray-50">
                            <div class="min-w-0">
                                <p class="truncate font-medium">{{ event.title }}</p>
                                <p class="text-sm text-gray-500">{{ formatDateTime(event.starts_at) }}<span v-if="event.location"> · {{ event.location }}</span></p>
                            </div>
                            <StatusBadge :active="event.is_published" />
                        </RouterLink>
                    </li>
                    <li v-if="!data.next_events.length" class="px-5 py-6 text-sm text-gray-500">No upcoming events.</li>
                </ul>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white">
                <header class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                    <h2 class="font-semibold">Latest posts</h2>
                    <RouterLink :to="{ name: 'posts.index' }" class="text-sm font-medium text-brand-600 hover:text-brand-700">View all</RouterLink>
                </header>
                <ul class="divide-y divide-gray-100">
                    <li v-for="post in data.latest_posts" :key="post.id">
                        <RouterLink :to="{ name: 'posts.edit', params: { id: post.id } }" class="flex items-center justify-between gap-4 px-5 py-3 hover:bg-gray-50">
                            <div class="min-w-0">
                                <p class="truncate font-medium">{{ post.title }}</p>
                                <p class="text-sm text-gray-500">{{ post.published_at ? formatDateTime(post.published_at) : 'Not scheduled' }}</p>
                            </div>
                            <StatusBadge :active="post.is_published" />
                        </RouterLink>
                    </li>
                    <li v-if="!data.latest_posts.length" class="px-5 py-6 text-sm text-gray-500">No posts yet.</li>
                </ul>
            </section>
        </div>
    </div>
</template>
