<script setup lang="ts">
import { ref, computed } from 'vue'
import { mdiChevronDown } from '@mdi/js'
import SvgIcon from '@jamescoyle/vue-icon'

interface Props {
    content: string
    maxChars?: number
}

const props = withDefaults(defineProps<Props>(), {
    maxChars: 200
})

const isExpanded = ref(false)

const truncatedContent = computed(() => {
    if (!props.content) return ''
    const text = props.content
    if (text.length <= props.maxChars) return text

    const truncateAt = props.content.lastIndexOf(' ', props.maxChars)
    return text.substring(0, truncateAt) + '...'
})

const shouldShowToggle = computed(() => {
    return props.content?.length > props.maxChars
})

const toggleCollapse = () => {
    isExpanded.value = !isExpanded.value
}
</script>

<template>
    <div class="expandable-content">
        <!-- Contenu tronqué -->
        <div v-if="!isExpanded" v-html="truncatedContent" />

        <!-- Contenu complet avec collapse -->
        <b-collapse v-model="isExpanded">
            <div v-html="content" />
        </b-collapse>

        <!-- Bouton toggle -->
        <a
            v-if="shouldShowToggle"
            role="button"
            class="p-0 mb-4 mt-2 btn-more d-flex align-items-center collapsed"
            @click="toggleCollapse"
        >
            Voir {{ isExpanded ? 'moins' : 'plus' }}
            <svg-icon
                type="mdi"
                :path="mdiChevronDown"
                :class="[
          'ms-2',
          { 'rotate-180': isExpanded }
        ]"
                size="18"
            />
        </a>
    </div>
</template>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
    transition: transform 0.35s ease;
}

.expandable-content :deep(p) {
    margin-bottom: 1rem;
}

.expandable-content :deep(p:last-child) {
    margin-bottom: 0;
}

.expandable-content :deep(.collapse-enter-active),
.expandable-content :deep(.collapse-leave-active) {
    transition: all 0.35s ease;
}

/* Style pour le bouton voir plus/moins */
.btn-link {
    color: var(--bs-body-color);
}

.btn-link:hover {
    color: var(--bs-primary);
}
</style>
