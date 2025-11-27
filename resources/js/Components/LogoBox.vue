<template>
  <Link href="/" class="navbar-brand">
    <template v-if="!onlyLight">
      <img class="light-mode-item navbar-brand-item" :class="customClass" src="/images/front/logo.png" alt="logo"/>
      <img class="dark-mode-item navbar-brand-item" :class="customClass" src="/images/front/logo.png" alt="logo"/>
    </template>

    <img v-if="onlyLight" class="navbar-brand-item" src="/images/front/logo.png" alt="logo"/>
  </Link>
</template>

<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

defineProps({
  customClass: String,
  onlyLight: {
    type: Boolean,
    default: false
  },
})
</script>
