import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    { path: '/', name: 'dashboard', component: () => import('./pages/Dashboard.vue'), meta: { title: 'Dashboard' } },

    { path: '/events', name: 'events.index', component: () => import('./pages/events/EventIndex.vue'), meta: { title: 'Events' } },
    { path: '/events/create', name: 'events.create', component: () => import('./pages/events/EventForm.vue'), meta: { title: 'New event' } },
    { path: '/events/:id/edit', name: 'events.edit', component: () => import('./pages/events/EventForm.vue'), props: true, meta: { title: 'Edit event' } },

    { path: '/posts', name: 'posts.index', component: () => import('./pages/posts/PostIndex.vue'), meta: { title: 'Blog posts' } },
    { path: '/posts/create', name: 'posts.create', component: () => import('./pages/posts/PostForm.vue'), meta: { title: 'New post' } },
    { path: '/posts/:id/edit', name: 'posts.edit', component: () => import('./pages/posts/PostForm.vue'), props: true, meta: { title: 'Edit post' } },

    { path: '/users', name: 'users.index', component: () => import('./pages/users/UserIndex.vue'), meta: { title: 'Users' } },
    { path: '/users/create', name: 'users.create', component: () => import('./pages/users/UserForm.vue'), meta: { title: 'New user' } },
    { path: '/users/:id/edit', name: 'users.edit', component: () => import('./pages/users/UserForm.vue'), props: true, meta: { title: 'Edit user' } },

    { path: '/:pathMatch(.*)*', name: 'not-found', component: () => import('./pages/NotFound.vue'), meta: { title: 'Not found' } },
];

const router = createRouter({
    history: createWebHistory('/admin'),
    routes,
    scrollBehavior: () => ({ top: 0 }),
});

router.afterEach((to) => {
    document.title = `${to.meta.title ?? 'Admin'} · Admin`;
});

export default router;
