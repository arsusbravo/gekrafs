<script setup>
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import ToastList from './components/ToastList.vue';

const props = defineProps({
    appName: { type: String, required: true },
    user: { type: Object, required: true },
    logoutUrl: { type: String, required: true },
    siteUrl: { type: String, required: true },
});

const route = useRoute();
const sidebarOpen = ref(false);
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

const navigation = [
    { name: 'Dashboard', to: { name: 'dashboard' }, match: 'dashboard', icon: 'M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25' },
    { name: 'Events', to: { name: 'events.index' }, match: 'events.', icon: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5' },
    { name: 'Blog posts', to: { name: 'posts.index' }, match: 'posts.', icon: 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z' },
    { name: 'Users', to: { name: 'users.index' }, match: 'users.', icon: 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z' },
];

const isActive = (item) => String(route.name ?? '').startsWith(item.match);

watch(() => route.fullPath, () => (sidebarOpen.value = false));
</script>

<template>
    <div class="flex min-h-full">
        <!-- Mobile backdrop -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden" @click="sidebarOpen = false" />

        <aside
            :class="[
                'fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-brand-950 text-brand-100 transition-transform lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <div class="flex h-16 items-center gap-2 px-6">
                <img src="/storage/images/logo.png" alt="" class="h-9 w-9" />
                <span class="font-display text-2xl tracking-wider text-white">{{ props.appName }}</span>
                <span class="rounded bg-brand-600 px-1.5 py-0.5 text-xs font-medium text-white">Admin</span>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-4">
                <RouterLink
                    v-for="item in navigation"
                    :key="item.name"
                    :to="item.to"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                        isActive(item) ? 'bg-brand-600 text-white' : 'hover:bg-white/10 hover:text-white',
                    ]"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                    </svg>
                    {{ item.name }}
                </RouterLink>
            </nav>

            <div class="border-t border-white/10 p-4">
                <p class="truncate text-sm font-medium text-white">{{ props.user.name }}</p>
                <p class="truncate text-xs text-brand-300">{{ props.user.email }}</p>
                <div class="mt-3 flex gap-2">
                    <a :href="props.siteUrl" class="flex-1 rounded-lg border border-white/20 px-3 py-1.5 text-center text-xs font-medium hover:bg-white/10">View site</a>
                    <form :action="props.logoutUrl" method="POST" class="flex-1">
                        <input type="hidden" name="_token" :value="csrfToken" />
                        <button type="submit" class="w-full rounded-lg border border-white/20 px-3 py-1.5 text-xs font-medium hover:bg-white/10">Log out</button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
            <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-gray-200 bg-white px-4 sm:px-6">
                <button type="button" class="rounded-md p-2 text-gray-600 hover:bg-gray-100 lg:hidden" aria-label="Open menu" @click="sidebarOpen = true">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h1 class="text-lg font-semibold">{{ route.meta.title }}</h1>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <RouterView :key="route.fullPath" />
            </main>
        </div>

        <ToastList />
    </div>
</template>
