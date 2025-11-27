<template>
  <Link
    class="dropdown-item"
    :class="(item.link?.name && route().current(item.link.name) && 'active') || linkClass"
    :href="item.link?.name ? route(item.link.name, item.link.params || {}) : '#'"
  >
    {{ item.label }}
  </Link>
</template>

<script setup lang="ts">
import type { MenuItemType } from '@/Types/layout'
import { Link } from '@inertiajs/vue3'

type SubMenus = {
  item: MenuItemType
  itemClass?: string
  linkClass?: string
  activeMenuItems?: Array<string>
}

defineProps<SubMenus>()
</script>
