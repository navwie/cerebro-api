import '@fortawesome/fontawesome-free/css/all.min.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { createPinia } from 'pinia';
import App from './App.vue';

createInertiaApp({
    resolve: async name => {
        const page = await import(`./Pages/${name}.vue`);
        return page.default;
    },
    setup({ el, app, props, plugin }) {
        const inertiaApp = createApp({
            render: () => h(App, props, () => h(app, props)),
        });

        inertiaApp.use(createPinia());
        inertiaApp.use(plugin);
        inertiaApp.mount(el);
    },
});
