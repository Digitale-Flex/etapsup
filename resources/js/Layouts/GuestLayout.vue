<script setup lang="ts">
import { changeHTMLAttribute } from '@/Helpers/layout';
import { useLayoutStore } from '@/Stores/layout';
import { onMounted } from 'vue';
// EtapSup: Header et Footer réactivés
import AppHeader from "@/Layouts/Partials/AppHeader.vue";
import AppFooter from "@/Layouts/Partials/AppFooter.vue";

const useLayout = useLayoutStore();

onMounted(() => {
    changeHTMLAttribute('data-bs-theme', useLayout.theme);
});
</script>

<template>
    <div>
        <!-- EtapSup: Header global avec navigation -->
        <AppHeader />

        <main>
            <slot />
        </main>

        <!-- EtapSup: Footer global -->
        <AppFooter />
    </div>
</template>
