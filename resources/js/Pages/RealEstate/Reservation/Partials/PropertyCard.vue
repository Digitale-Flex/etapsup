<script setup lang="ts">
import { Property } from '@/Types/index';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface PropPage {
    property: Property;
}
const props = defineProps<PropPage>();

const displayStars = computed(() => {
    const average = props.property.ratings?.average ?? 0;
    const fullStars = Math.floor(average);
    const hasHalfStar = average % 1 >= 0.5;
    return {
        full: fullStars,
        half: hasHalfStar,
        empty: 5 - fullStars - (hasHalfStar ? 1 : 0),
    };
});
</script>

<template>
    <BCard no-body class="p-2 shadow">
        <BRow class="g-0">
            <BCol md="3">
                <BImg :src="property.thumb" rounded />
            </BCol>
            <BCol md="9">
                <BCardBody class="px-2 py-0">
                    <b-card-title tag="h6">
                        <Link
                            :href="
                                route('properties.show', {
                                    property: property.slug,
                                })
                            "
                            prefetch
                        >
                            {{ property.title }}
                        </Link>
                    </b-card-title>
                    <BCardText>
                        <small>{{ property.address }}</small>
                    </BCardText>
                </BCardBody>
            </BCol>
        </BRow>
    </BCard>
</template>

<style scoped></style>
