import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3'; // pastikan paket ini terpasang (sekarang di "dependencies")
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Log event Inertia untuk membantu diagnosis /dashboard
router.on('error', (errors) => console.error('[Inertia error]', errors));
router.on('invalid', (visit) => console.warn('[Inertia invalid visit]', visit));

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
