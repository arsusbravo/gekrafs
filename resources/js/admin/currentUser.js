import { reactive } from 'vue';

/**
 * The logged-in user, provided by the server in the admin page (data-user).
 */
const el = document.getElementById('admin-app');

export const currentUser = reactive(JSON.parse(el?.dataset.user ?? '{}'));

export const isAdmin = () => currentUser.is_admin === true;
