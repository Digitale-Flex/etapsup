<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

defineProps({ title: String })

</script>

<template>
    <Head :title="title ? `${title}` : 'Mareza'">
        <slot />
    </Head>
</template>

<style scoped>

</style>
