<template>
  <b-collapse class="navbar-collapse">
    <b-navbar-nav v-if="startBookingMenu" class="navbar-nav-scroll me-auto">
      <b-nav-item-dropdown class="" toggle-class="ps-md-3 m-0" menu-class="z-3" no-caret>
        <template v-slot:button-content>
          <a
              href="#"
              id="listingMenu"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
          >
            <span class="me-auto items-center gap-1">
              <font-awesome-icon :icon="faSuitcaseRolling"/> Travelers
              <font-awesome-icon :icon="faAngleDown" size="sm"/>
            </span>
          </a>
        </template>

        <template v-for="item in bookingHomeMenuItems" :key="item.key">
          <li class="mb-1">
            <Link
                :href="item.link?.name ? route(item.link.name) : '#'"
                class="dropdown-item bg-light-hover d-flex align-items-center rounded p-2"
                :class="{ active: item.link?.name ? route().current(item.link.name) : false }"
            >
              <div class="icon-md bg-primary bg-opacity-10 rounded text-primary flex-shrink-0">
                <component :is="item.icon" class="fs-6"/>
              </div>
              <div class="ms-2">
                <h6 class="mb-0">{{ item.label }}</h6>
                <p class="small mb-0 text-body">Small description here</p>
              </div>
            </Link>
          </li>
        </template>
      </b-nav-item-dropdown>
    </b-navbar-nav>

    <ul class="navbar-nav navbar-nav-scroll me-auto d-none" :class="menuClass">
      <template v-for="item in menuItems" :key="item.key">
        <MenuItemWithChildren
            v-if="item.children"
            :item="item"
            :level="0"
            item-class="nav-item"
            link-class="nav-link arrow-none d-flex align-items-center gap-1 justify-content-between"
        />
        <MenuItem v-else :item="item" link-class="nav-link"/>
      </template>

      <li class="nav-item dropdown" v-if="showExtraPages">
        <a
            class="nav-link"
            href="#"
            id="advanceMenu"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
          <font-awesome-icon :icon="faEllipsis"/>
        </a>
      </li>
    </ul>
  </b-collapse>
</template>

<script setup lang="ts">
import {faEllipsis, faSuitcaseRolling, faAngleDown} from '@fortawesome/free-solid-svg-icons'


import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import {getAppMenuItems} from '@/Helpers/menu'
import MenuItemWithChildren from '@/Components/Navbar/MenuItemWithChildren.vue'
import MenuItem from '@/Components/Navbar/MenuItem.vue'
import {bookingHomeMenuItems} from '@assets/data/menu-items'
import { Link } from '@inertiajs/vue3'

type AppMenuProps = {
  showExtraPages?: boolean
  startBookingMenu?: boolean
  menuClass?: string
}

defineProps<AppMenuProps>()

const menuItems = getAppMenuItems()
</script>
