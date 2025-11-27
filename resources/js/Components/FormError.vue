<script setup>
import { computed } from 'vue';

const props = defineProps({
    errors: {
        type: [String, Object],
        default: '',
    },
});

const isString = computed(() => typeof props.errors === 'string');

const hasErrors = computed(() => {
    if (isString.value) {
        return props.errors.trim().length > 0;
    }
    return Object.keys(props.errors).length > 0;
});
</script>
<template>
    <div v-if="hasErrors" class="invalid-feedback d-block">
        <!-- Si errors est une chaîne de caractères -->
        <template v-if="isString">
            {{ errors }}
        </template>
        <!-- Sinon, on parcourt les erreurs dans l'objet -->
        <template v-else>
            <ul class="mb-0">
                <li v-for="(message, key) in errors" :key="key">
                    {{ message }}
                </li>
            </ul>
        </template>
    </div>
</template>
