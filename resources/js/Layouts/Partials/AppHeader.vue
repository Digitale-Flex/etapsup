<script setup lang="ts">
import CustomDropDown from '@/Components/CustomDropDown.vue';
import LogoBox from '@/Components/LogoBox.vue';
import AppMenu from '@/Components/Navbar/AppMenu.vue';
import MobileMenu from '@/Components/Navbar/mobile-menu/MobileMenu.vue';
import { Link, router } from '@inertiajs/vue3';

import UserMenu from '@/Layouts/Partials/UserMenu.vue';
import {
    BIconBell,
    BIconLightningCharge,
    BIconSearch,
} from 'bootstrap-icons-vue';
import { computed, onMounted, onUnmounted, ref } from 'vue';

let isSticky = ref<boolean>(false);
const isMobileMenuOpen = ref<boolean>(false);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};

// Fermer le menu mobile quand on clique ailleurs
const handleClickOutside = (event: MouseEvent) => {
    if (!isMobileMenuOpen.value) return;

    const target = event.target as HTMLElement;
    const header = document.querySelector('header.navbar-light');
    const navbar = document.getElementById('navbar-collapse');

    // Ne pas fermer si on clique sur le toggle button ou dans le menu
    if (target.closest('.navbar-toggler')) return;
    if (navbar?.contains(target)) return;

    closeMobileMenu();
};

onMounted(() => {
    window.addEventListener('scroll', () => {
        isSticky.value = window.scrollY >= 400;
    });

    // Écouter les clics pour fermer le menu mobile
    document.addEventListener('click', handleClickOutside);

    // Fermer le menu lors de la navigation Inertia
    router.on('start', () => {
        closeMobileMenu();
    });
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const isMobileMenu = computed(() => {
    return window.innerWidth <= 640;
});
</script>

<template>
    <header
        class="navbar-light header-sticky"
        :class="{ 'header-sticky-on': isSticky }"
    >
        <nav class="navbar navbar-expand-xl">
            <b-container>
                <LogoBox />

                <!-- Responsive navbar toggler -->
                <button
                    v-if="isMobileMenu"
                    class="navbar-toggler ms-sm-0 p-sm-2 ms-auto p-0"
                    type="button"
                    @click="toggleMobileMenu"
                >
                    <span class="navbar-toggler-animation py-1">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    <span class="d-none d-sm-inline-block small ms-1"
                        >Menu</span
                    >
                </button>

                <template v-if="isMobileMenu">
                    <MobileMenu v-model:visible="isMobileMenuOpen" show-extra-pages />
                    <AppMenu show-extra-pages />
                </template>

                <b-nav
                    class="align-items-center list-unstyled ms-xl-auto flex-row"
                >
                    <!-- Notification -->
                    <CustomDropDown
                        is="li"
                        custom-class="nav-item ms-0 ms-md-3 d-none"
                    >
                        <a
                            class="nav-notification btn btn-light mb-0 p-0"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            data-bs-auto-close="outside"
                        >
                            <BIconBell class="fa-fw" />
                        </a>
                        <span class="notif-badge animation-blink"></span>

                        <div
                            class="dropdown-menu dropdown-animation dropdown-menu-end dropdown-menu-size-md p-0 shadow-lg"
                        >
                            <b-card class="bg-transparent" body-class="p-0">
                                <b-card-header
                                    class="d-flex justify-content-between align-items-center border-bottom bg-transparent"
                                >
                                    <h6 class="m-0">
                                        Notifications
                                        <span
                                            class="badge bg-danger text-danger ms-2 bg-opacity-10"
                                            >4 new</span
                                        >
                                    </h6>
                                    <a class="small" href="#">Clear all</a>
                                </b-card-header>

                                <b-card-footer
                                    class="border-top bg-transparent text-center"
                                >
                                    <a
                                        href="#"
                                        class="btn btn-sm btn-link mb-0 p-0"
                                        >See all incoming activity</a
                                    >
                                </b-card-footer>
                            </b-card>
                        </div>
                    </CustomDropDown>
                    <!-- EtapSup: Liens navigation principale -->
                    <li class="nav-item d-none d-sm-block ms-3">
                        <Link
                            :href="route('establishments.index')"
                            class="btn mb-0"
                            :class="
                                route().current('establishments.*')
                                    ? 'btn-primary'
                                    : 'btn-primary-soft'
                            "
                        >
                            <BIconSearch class="mb-1 me-1" />
                            Établissements
                        </Link>
                    </li>
                    <li class="nav-item d-none d-sm-block ms-2">
                        <Link
                            :href="route('custom-search.index')"
                            class="btn mb-0 btn-accompagnement"
                            :class="{ 'active': route().current('custom-search.*') }"
                        >
                            <BIconLightningCharge class="mb-1 me-1" />
                            Accompagnement
                        </Link>
                    </li>
                    <!-- Profile -->
                    <UserMenu v-if="$page.props.auth?.user" />

                    <!-- Bouton connexion si non connecté -->
                    <Link
                        v-else
                        :href="route('login')"
                        class="btn btn-outline-primary btn-sm ms-3"
                        >Se connecter</Link
                    >

                </b-nav>
            </b-container>
        </nav>
    </header>
</template>

<style scoped>
/* EtapSup: Bouton Accompagnement - Bleu #2 */
.btn-accompagnement {
    background-color: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid transparent;
    transition: all 0.3s ease;
}

.btn-accompagnement:hover,
.btn-accompagnement.active {
    background-color: #3b82f6;
    color: white;
}

/* EtapSup: Centrer le logo sur mobile */
@media (max-width: 640px) {
    :deep(.navbar-brand) {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    .navbar-toggler {
        position: relative;
        z-index: 10;
    }
}
</style>
