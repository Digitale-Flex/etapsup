<template>
    <b-collapse
        class="navbar-collapse"
        id="navbar-collapse"
        style="margin: 0 20px; padding: 0 10px"
    >
        <b-navbar-nav v-if="startBookingMenu" class="navbar-nav-scroll me-auto">
            <li class="nav-item">
                <a
                    class="nav-link"
                    href="#"
                    v-b-toggle.collapse="'listingMenu'"
                >
                    <span class="me-auto items-center gap-1">
                        <font-awesome-icon :icon="faSuitcaseRolling" />
                        Travelers
                        <font-awesome-icon :icon="faAngleDown" size="sm" />
                    </span>
                </a>

                <b-collapse id="listingMenu">
                    <template
                        v-for="item in bookingHomeMenuItems"
                        :key="item.key"
                    >
                        <li class="mb-1">
                            <Link
                                :to="{ name: item.link?.name }"
                                class="bg-light-hover d-flex align-items-center rounded p-2"
                                :class="{
                                    active: item.link?.name ? route().current(item.link.name) : false,
                                }"
                            >
                                <div
                                    class="icon-md bg-primary text-primary flex-shrink-0 rounded bg-opacity-10"
                                >
                                    <component :is="item.icon" />
                                </div>
                                <div class="ms-2">
                                    <h6 class="mb-0">{{ item.label }}</h6>
                                    <p class="small text-body mb-0">
                                        Small description here
                                    </p>
                                </div>
                            </Link>
                        </li>
                    </template>
                </b-collapse>
            </li>
        </b-navbar-nav>

        <b-navbar-nav class="navbar-nav-scroll me-auto" :class="menuClass">
            <template v-for="item in menuItems" :key="item.key">
                <MenuItemWithChildren
                    v-if="item.children"
                    :item="item"
                    :level="1"
                />
                <MenuItem v-else :item="item" />
            </template>
        </b-navbar-nav>
    </b-collapse>
</template>

<script setup lang="ts">
import MenuItem from '@/Components/Navbar/mobile-menu/MobileMenuItem.vue';
import MenuItemWithChildren from '@/Components/Navbar/mobile-menu/MobileMenuItemWithChildren.vue';
import { getAppMenuItems } from '@/Helpers/menu';
import {
    faAngleDown,
    faSuitcaseRolling,
} from '@fortawesome/free-solid-svg-icons';

import { bookingHomeMenuItems } from '@assets/data/menu-items';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Link } from '@inertiajs/vue3';

type AppMenuProps = {
    showExtraPages?: boolean;
    startBookingMenu?: boolean;
    menuClass?: string;
};

defineProps<AppMenuProps>();

const menuItems = getAppMenuItems();
</script>
