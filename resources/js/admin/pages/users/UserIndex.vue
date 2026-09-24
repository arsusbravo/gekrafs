<script setup>
import Pagination from '../../components/Pagination.vue';
import StatusBadge from '../../components/StatusBadge.vue';
import { useResourceList } from '../../composables/useResourceList';
import { formatDateTime } from '../../utils';

const { items, meta, search, loading, load, destroy } = useResourceList('/users');
</script>

<template>
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <input v-model="search" type="search" placeholder="Search by name or email…" class="form-input sm:max-w-xs" />
            <RouterLink :to="{ name: 'users.create' }" class="btn btn-primary">New user</RouterLink>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Joined</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" :class="{ 'opacity-50': loading }">
                        <tr v-for="user in items" :key="user.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ user.name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ user.email }}</td>
                            <td class="px-4 py-3"><StatusBadge :active="user.is_admin" active-label="Admin" inactive-label="User" /></td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ formatDateTime(user.created_at?.slice(0, 10)) }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <RouterLink :to="{ name: 'users.edit', params: { id: user.id } }" class="mr-3 font-medium text-brand-600 hover:text-brand-700">Edit</RouterLink>
                                <button type="button" class="font-medium text-red-600 hover:text-red-700" @click="destroy(user, user.name)">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="!loading && !items.length">
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">No users found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :meta="meta" @change="load" />
        </div>
    </div>
</template>
