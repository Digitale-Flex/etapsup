<script setup lang="ts">
import { faSignOutAlt } from '@fortawesome/free-solid-svg-icons';
import { Link, usePage } from '@inertiajs/vue3';
import {
    BIconHouseCheck,
    BIconListCheck,
    BIconPencilSquare,
    BIconPerson,
} from 'bootstrap-icons-vue';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const menu = [
    {
        label: 'Mes demandes',
        link: 'dashboard.certificates.index',
        icon: BIconListCheck,
    },
    {
        label: 'Mes réservations',
        link: 'dashboard.reservations.index',
        icon: BIconHouseCheck,
    },
    {
        label: 'Mon profil',
        link: 'dashboard.profile',
        icon: BIconPerson,
    },
];
</script>

<template>
    <b-col lg="4" xl="3">
        <b-card no-body class="bg-light w-100">
            <div class="position-absolute end-0 top-0 p-3">
                <a
                    href="#"
                    class="text-primary-hover"
                    v-b-tooltip="'Edit profile'"
                >
                    <BIconPencilSquare />
                </a>
            </div>

            <b-card-body class="p-3">
                <div class="mb-3 text-center">
                    <div class="avatar avatar-xl mb-2">
                        <img
                            class="avatar-img rounded-circle border border-2 border-white"
                            src="https://avatar.iran.liara.run/public/36"
                            alt=""
                        />
                    </div>
                    <h6 class="mb-0">{{ user?.full_name }}</h6>
                    <a href="#" class="text-reset text-primary-hover small">{{
                        user?.email
                    }}</a>
                    <hr />
                </div>

                <ul
                    class="nav nav-pills-primary-soft flex-column"
                    id="nav-sidebar"
                >
                    <li v-for="(item, i) in menu" :key="i" class="nav-item">
                        <Link
                            class="nav-link"
                            :href="route(item.link)"
                            :class="{
                                active: route().current(item.link),
                            }"
                        >
                            <component :is="item.icon" class="fa-fw me-2" />
                            {{ item.label }}
                        </Link>
                    </li>

                    <li class="nav-item">
                        <Link
                            :href="route('logout')"
                            method="post"
                            class="nav-link text-danger bg-danger-soft-hover"
                        >
                            <font-awesome-icon
                                :icon="faSignOutAlt"
                                class="fa-fw me-2"
                            />
                            Se déconnecter
                        </Link>
                    </li>
                </ul>
            </b-card-body>
        </b-card>
    </b-col>
</template>
