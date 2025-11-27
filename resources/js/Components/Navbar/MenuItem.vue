<template>
  <li>
    <MenuItemLink :item="item" :link-class="linkClass" />
  </li>
</template>

<script setup lang="ts">
import type { MenuItemType } from '@/Types/layout'
import MenuItemLink from '@/Components/Navbar/MenuItemLink.vue'

type SubMenus = {
  item: MenuItemType
  itemClass?: string
  linkClass?: string
  activeMenuItems?: Array<string>
}

defineProps<SubMenus>()
</script>
