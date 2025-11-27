<script setup lang="ts">
import CustomDropDown from '@/Components/CustomDropDown.vue';
import LogoBox from '@/Components/LogoBox.vue';
import AppMenu from '@/Components/Navbar/AppMenu.vue';
import MobileMenu from '@/Components/Navbar/mobile-menu/MobileMenu.vue';
import { Link } from '@inertiajs/vue3';

import UserMenu from '@/Layouts/Partials/UserMenu.vue';
import {
    BIconBell,
    BIconLightningCharge,
    BIconSearch,
} from 'bootstrap-icons-vue';
import { computed, onMounted, ref } from 'vue';

let isSticky = ref<boolean>(false);

onMounted(() => {
    window.addEventListener('scroll', () => {
        isSticky.value = window.scrollY >= 400;
    });
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
                    v-b-toggle="'navbar-collapse'"
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
                    <MobileMenu show-extra-pages />
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
                    <!-- Refonte: Action 01 - Éléments Mareza commentés pour autonomie page événementielle -->
                    <!--
                    <li class="nav-item d-none d-sm-block mx-5 ms-3">
                        <Link
                            :href="route('custom-search.index')"
                            class="btn mb-0"
                            :class="
                                route().current('custom-search.index')
                                    ? 'btn-info'
                                    : 'btn-info-soft'
                            "
                        >
                            <BIconSearch class="mb-1" />
                            Recherche personnalisée</Link
                        >
                    </li>
                    <li class="nav-item d-none d-sm-block mx-5 ms-3">
                        <Link
                            :href="route('certificate.home')"
                            class="btn mb-0"
                            :class="
                                route().current('certificate.home')
                                    ? 'btn-primary'
                                    : 'btn-primary-soft'
                            "
                        >
                            <BIconLightningCharge class="mb-1" />
                            <span>Attestation de logement</span>
                        </Link>
                    </li>
                    -->
                    <!-- Profile -->
                    <UserMenu v-if="$page.props.auth?.user" />

                    <!--
                    <Link
                        v-else
                        :href="route('login')"
                        class="btn btn-outline-primary"
                        >Se connecter</Link
                    -->

                </b-nav>
            </b-container>
        </nav>
    </header>
</template>

<style scoped></style>
