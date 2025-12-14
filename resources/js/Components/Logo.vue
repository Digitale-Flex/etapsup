<script setup lang="ts">
// Quick Win: Composant Logo EtapSup réutilisable
import { computed } from 'vue';

interface Props {
    size?: 'sm' | 'md' | 'lg' | 'xl';
    variant?: 'gradient' | 'white' | 'dark';
    showText?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    size: 'md',
    variant: 'gradient',
    showText: true,
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm': return 'h-8';
        case 'md': return 'h-10';
        case 'lg': return 'h-14';
        case 'xl': return 'h-20';
        default: return 'h-10';
    }
});

const textClasses = computed(() => {
    const base = 'font-bold tracking-tight';
    const size = props.size === 'sm' ? 'text-xl' : props.size === 'lg' ? 'text-4xl' : props.size === 'xl' ? 'text-5xl' : 'text-2xl';

    let color = '';
    if (props.variant === 'gradient') {
        color = 'bg-gradient-to-r from-[#1e3a8a] to-[#1e3a8a] bg-clip-text text-transparent';
    } else if (props.variant === 'white') {
        color = 'text-white';
    } else {
        color = 'text-gray-900';
    }

    return `${base} ${size} ${color}`;
});
</script>

<template>
    <div class="logo-etapsup flex items-center gap-3">
        <!-- Icon SVG Logo -->
        <div :class="sizeClasses" class="flex items-center">
            <svg viewBox="0 0 100 100" :class="sizeClasses" xmlns="http://www.w3.org/2000/svg">
                <!-- Gradient Definition -->
                <defs>
                    <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#1e3a8a;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#1e3a8a;stop-opacity:1" />
                    </linearGradient>
                </defs>

                <!-- E stylisé + escalier montant (symbolise progression étudiant) -->
                <g :fill="variant === 'white' ? 'white' : variant === 'dark' ? '#1a202c' : 'url(#logoGradient)'">
                    <!-- Lettre E moderne -->
                    <rect x="15" y="20" width="8" height="60" rx="4" />
                    <rect x="15" y="20" width="35" height="8" rx="4" />
                    <rect x="15" y="46" width="30" height="8" rx="4" />
                    <rect x="15" y="72" width="35" height="8" rx="4" />

                    <!-- Escalier ascendant (3 marches) symbolisant progression -->
                    <rect x="60" y="55" width="12" height="25" rx="2" />
                    <rect x="75" y="40" width="12" height="40" rx="2" />
                    <rect x="90" y="25" width="8" height="55" rx="2" />
                </g>
            </svg>
        </div>

        <!-- Texte EtapSup -->
        <span v-if="showText" :class="textClasses">
            EtapSup
        </span>
    </div>
</template>

<style scoped>
.logo-etapsup {
    user-select: none;
}

.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
