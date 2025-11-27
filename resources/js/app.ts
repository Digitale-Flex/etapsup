//import '../css/app.css';
import 'aos/dist/aos.css';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';
import 'bootstrap/dist/css/bootstrap.css';
import 'bs-stepper/dist/css/bs-stepper.min.css';
import 'glightbox/dist/css/glightbox.min.css';
import 'tiny-slider/dist/tiny-slider.css';
import '../assets/scss/style.scss';
import './bootstrap';
// import '../css/app.css';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import FilePond from '@/Plugins/FilePond';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { createInertiaApp } from '@inertiajs/vue3';
import Aura from '@primevue/themes/aura';
import { createBootstrap } from 'bootstrap-vue-next';
import 'clockwork-browser/toolbar';
import { OhVueIcon } from 'oh-vue-icons';
import { createPinia } from 'pinia';
import { fr } from 'primelocale/fr.json';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';
import { Calendar, DatePicker, setupCalendar } from 'v-calendar';
import 'v-calendar/style.css';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { useCustomRegle } from './Plugins/regle.config';

import 'primeicons/primeicons.css';

const pinia = createPinia();
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const page = pages[`./Pages/${name}.vue`] as any;
        if (page?.default) {
            page.default.layout = page.default.layout || GuestLayout;
        }
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(pinia)
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                locale: fr,
                theme: {
                    preset: Aura,
                    unstyled: true,
                    options: {
                        darkModeSelector: 'light',
                    },
                },
            })
            .use(ToastService)
            .use(setupCalendar, {})
            .component('VDatePicker', DatePicker)
            .component('VCalendar', Calendar)
            .component('FilePond', FilePond as any)
            .component('font-awesome-icon', FontAwesomeIcon)
            .component('v-icon', OhVueIcon)
            .provide('useRegle', useCustomRegle)
            .use(createBootstrap())
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
