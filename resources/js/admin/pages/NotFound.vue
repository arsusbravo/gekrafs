<template>
    <div class="py-20 text-center">
        <p class="text-5xl font-bold text-gray-300">404</p>
        <p class="mt-4 text-gray-600">This page could not be found.</p>
        <RouterLink :to="{ name: 'dashboard' }" class="btn btn-primary mt-6">Back to dashboard</RouterLink>
    </div>
</template>
