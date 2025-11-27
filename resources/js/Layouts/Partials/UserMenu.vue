<script setup lang="ts">
import CustomDropDown from '@/Components/CustomDropDown.vue';
import { useLayoutStore } from '@/Stores/layout';
import type { ThemeModeType } from '@/Types/layout';
import { Link } from '@inertiajs/vue3';
import {
    BIconBookmarkCheck,
    BIconCircleHalf,
    BIconGear,
    BIconHouseCheck,
    BIconMoonStars,
    BIconPower,
    BIconSun,
} from 'bootstrap-icons-vue';

const themeModes: ThemeModeType[] = [
    {
        icon: BIconSun,
        theme: 'light',
    },
    {
        icon: BIconMoonStars,
        theme: 'dark',
    },
    {
        icon: BIconCircleHalf,
        theme: 'auto',
    },
];

const useLayout = useLayoutStore();
</script>

<template>
    <CustomDropDown is="li" custom-class="nav-item ms-3">
        <a
            class="avatar avatar-sm p-0"
            href="#"
            role="button"
            data-bs-auto-close="outside"
            data-bs-display="static"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            <img
                class="avatar-img rounded-2"
                :src="$page.props.auth?.user.photo"
                alt="avatar"
            />
        </a>

        <ul
            class="dropdown-menu dropdown-animation dropdown-menu-end pt-3 shadow"
            aria-labelledby="profileDropdown"
        >
            <li class="px-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="avatar me-3">
                        <img
                            class="avatar-img rounded-circle shadow"
                            :src="$page.props.auth?.user.photo"
                            alt="avatar"
                        />
                    </div>
                    <div>
                        <a class="h6 mt-sm-0 mt-2" href="#">{{
                            $page.props.auth?.user.name
                        }}</a>
                        <p class="small m-0">
                            {{ $page.props.auth?.user.email }}
                        </p>
                    </div>
                </div>
            </li>

            <li>
                <hr class="dropdown-divider" />
            </li>

            <li>
                <Link
                    :href="route('dashboard.certificates.index')"
                    class="dropdown-item"
                >
                    <BIconBookmarkCheck class="fa-fw me-2" />
                    Mes demandes
                </Link>
            </li>
            <li>
                <Link
                    :href="route('dashboard.reservations.index')"
                    class="dropdown-item"
                >
                    <BIconHouseCheck class="fa-fw me-2" />
                    Mes réservations
                </Link>
            </li>
            <li>
                <Link :href="route('dashboard.profile')" class="dropdown-item">
                    <BIconGear class="fa-fw me-2" />
                    Mon profil
                </Link>
            </li>
            <li>
                <Link
                    :href="route('logout')"
                    method="post"
                    class="dropdown-item bg-danger-soft-hover"
                >
                    <BIconPower class="fa-fw me-2" />
                    Se déconnecter
                </Link>
            </li>

            <li class="d-none">
                <hr class="dropdown-divider" />
            </li>

            <!-- Dark mode options -->
            <li class="d-none">
                <div
                    class="nav-pills-primary-soft theme-icon-active d-flex justify-content-between align-items-center p-2 pb-0"
                >
                    <span>Mode:</span>

                    <button
                        v-for="mode in themeModes"
                        :key="mode.theme"
                        type="button"
                        class="btn btn-link nav-link text-primary-hover mb-0 p-0"
                        :class="{
                            active: mode.theme === useLayout.theme,
                        }"
                        @click="useLayout.setTheme(mode.theme)"
                        v-b-tooltip.hover
                        :title="mode.theme"
                    >
                        <component :is="mode.icon" />
                    </button>
                </div>
            </li>
        </ul>
    </CustomDropDown>
</template>

<style scoped></style>
