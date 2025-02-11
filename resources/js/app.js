import 'flowbite';
import vuetify from "../plugins/vuetify";
import '@mdi/font/css/materialdesignicons.css'

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName =
    import.meta.env.VITE_APP_NAME || 'CSF';

createInertiaApp({
    title: (title) => title ? `${appName} | ${title}` : appName,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(vuetify)
            .mount(el);
    },
    progress: {
        color: '#06402B',
    },

});