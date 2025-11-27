<template>
    <Link
    :href="item.link?.name ? route(item.link.name, item.link.params || {}) : '#'"
    class="nav-link"
    :class="(item.link?.name && route().current(item.link.name) && 'active') || linkClass"
  >
        {{ item.label }}
    </Link>
</template>

<script setup lang="ts">
import {Link} from '@inertiajs/vue3'
import type { MenuItemType } from '@/Types/layout'

type SubMenus = {
    item: MenuItemType
    itemClass?: string
    linkClass?: string
    activeMenuItems?: Array<string>
}

defineProps<SubMenus>()
</script>
