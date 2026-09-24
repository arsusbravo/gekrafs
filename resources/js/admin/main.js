import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

const el = document.getElementById('admin-app');

createApp(App, {
    appName: el.dataset.appName,
    user: JSON.parse(el.dataset.user),
    logoutUrl: el.dataset.logoutUrl,
    siteUrl: el.dataset.siteUrl,
})
    .use(router)
    .mount(el);
