<script setup lang="ts">
import { faSignOutAlt } from '@fortawesome/free-solid-svg-icons';
import { Link } from '@inertiajs/vue3';
import {
    BIconHouseCheck,
    BIconListCheck,
    BIconPerson,
} from 'bootstrap-icons-vue';
// UI-Fix-3.1: Menu harmonisé selon spécifications EtapSup
const menu = [
    {
        label: 'Mon tableau de bord',
        link: 'dashboard',
        icon: BIconHouseCheck,
        visible: true,
    },
    {
        label: 'Mes candidatures', // Renommé de "Mes réservations"
        link: 'dashboard.reservations.index',
        icon: BIconListCheck,
        visible: true,
    },
    {
        label: 'Mon profil',
        link: 'dashboard.profile',
        icon: BIconPerson,
        visible: true,
    },
    // "Mes demandes" masqué mais conservé pour compatibilité
    {
        label: 'Mes demandes',
        link: 'dashboard.certificates.index',
        icon: BIconListCheck,
        visible: false, // Masqué
    },
];
</script>

<template>
    <b-col lg="4" xl="3">
        <b-card no-body class="bg-light w-100">
            <b-card-body class="p-3">
                <!-- UI-Fix-3.1: Filtrer éléments visibles -->
                <ul
                    class="nav nav-pills-primary-soft flex-column"
                    id="nav-sidebar"
                >
                    <li v-for="(item, i) in menu.filter(m => m.visible)" :key="i" class="nav-item">
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
