<script setup lang="ts">
import { changeHTMLAttribute } from '@/Helpers/layout';
import { useLayoutStore } from '@/Stores/layout';
import { onMounted } from 'vue';

const useLayout = useLayoutStore();

onMounted(() => {
    changeHTMLAttribute('data-bs-theme', useLayout.theme);
});
</script>

<template>
    <div>
        <!-- Layout minimal pour page événementielle sans header/footer YOD INVEST -->
        <main>
            <slot />
        </main>
    </div>
</template>