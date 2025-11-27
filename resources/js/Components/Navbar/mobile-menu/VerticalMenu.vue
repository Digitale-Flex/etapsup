<template>
  <div class="navbar-collapse" id="navbar-collapse">
    <b-navbar-nav v-if="startBookingMenu" class="navbar-nav-scroll me-auto">
      <li class="nav-item">
        <a class="nav-link" href="#" v-b-toggle.collapse="'listingMenu'">
          <span class="me-auto items-center gap-1">
            <font-awesome-icon :icon="faSuitcaseRolling"/> Travelers
            <font-awesome-icon :icon="faAngleDown" size="sm" class="float-end"/>
          </span>
        </a>

        <b-collapse id="listingMenu">
          <template v-for="item in bookingHomeMenuItems" :key="item.key">
            <li class="mb-1">
              <router-link
                  :to="{ name: item.link?.name }"
                  class="bg-light-hover d-flex align-items-center rounded p-2"
                  :class="{ active: item.link?.name === currentRouteName }"
              >
                <div class="icon-md bg-primary bg-opacity-10 rounded text-primary flex-shrink-0">
                  <component :is="item.icon"/>
                </div>
                <div class="ms-2">
                  <h6 class="mb-0">{{ item.label }}</h6>
                  <p class="small mb-0 text-body">Small description here</p>
                </div>
              </router-link>
            </li>
          </template>
        </b-collapse>
      </li>
    </b-navbar-nav>

    <b-navbar-nav class="navbar-nav-scroll me-auto" :class="menuClass">
      <template v-for="item in menuItems" :key="item.key">
        <MenuItemWithChildren v-if="item.children" :item="item" :level="1"/>
        <MenuItem v-else :item="item"/>
      </template>

      <li class="nav-item" v-if="showExtraPages">
        <a class="nav-link" href="#" v-b-toggle.collapse="'advanceMenu'">
          <font-awesome-icon :icon="faEllipsis"/>
        </a>
        <b-collapse id="advanceMenu">
          <li class="mx-3">
            <a class="nav-link" href="https://support.webestica.com/" target="_blank">
              <BIconLifePreserver class="text-warning fa-fw me-2"/>
              Support
            </a>
          </li>
          <li class="mx-3">
            <router-link to="/docs/index.html" class="nav-link" target="_blank">
              <BIconCardText class="text-danger fa-fw me-2"/>
              Documentation
            </router-link>
          </li>
          <li class="mx-3">
            <hr class="dropdown-divider"/>
          </li>
          <li class="mx-3">
            <a
                class="nav-link"
                href="https://themes.getbootstrap.com/store/webestica/"
                target="_blank"
            >
              <BIconCloudDownloadFill class="text-success fa-fw me-2"/>
              Buy Booking!
            </a>
          </li>
          <li class="mx-3">
            <hr class="dropdown-divider"/>
          </li>
          <li class="mx-3">
            <router-link to="/docs/icons.html" class="nav-link" target="_blank">
              <BIconPuzzleFill class="text-orange fa-fw me-2"/>
              Components
            </router-link>
          </li>
        </b-collapse>
      </li>
    </b-navbar-nav>
  </div>
</template>

<script setup lang="ts">
import MenuItemWithChildren from '@/Components/Navbar/mobile-menu/MobileMenuItemWithChildren.vue'
import MenuItem from '@/Components/Navbar/mobile-menu/MobileMenuItem.vue'
import {getAppMenuItems} from '@/Helpers/menu'
import {faAngleDown, faEllipsis, faSuitcaseRolling} from '@fortawesome/free-solid-svg-icons'
import {
  BIconCardText,
  BIconCloudDownloadFill,
  BIconLifePreserver,
  BIconPuzzleFill
} from 'bootstrap-icons-vue'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import {bookingHomeMenuItems} from '@/assets/data/menu-items'
import { usePage } from '@inertiajs/vue3'

type AppMenuProps = {
  showExtraPages?: boolean
  startBookingMenu?: boolean
  menuClass?: string
}

defineProps<AppMenuProps>()

const menuItems = getAppMenuItems()

const page = usePage()
const currentRouteName = page.component
</script>
