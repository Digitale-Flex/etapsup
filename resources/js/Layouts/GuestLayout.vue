<script setup lang="ts">
import { changeHTMLAttribute } from '@/Helpers/layout';
import { useLayoutStore } from '@/Stores/layout';
import { onMounted } from 'vue';
// Refonte: Masquer AppHeader et AppFooter (Mareza/YOD INVEST)
// import AppHeader from "@/Layouts/Partials/AppHeader.vue";
// import AppFooter from "@/Layouts/Partials/AppFooter.vue";

const useLayout = useLayoutStore();

onMounted(() => {
    changeHTMLAttribute('data-bs-theme', useLayout.theme);
});
</script>

<template>
    <div>
        <!-- Refonte: Header masqué temporairement -->
        <!-- <AppHeader /> -->

        <main>
            <slot />
        </main>

        <!-- Refonte: Footer masqué temporairement -->
        <!-- <AppFooter /> -->
    </div>
</template>
