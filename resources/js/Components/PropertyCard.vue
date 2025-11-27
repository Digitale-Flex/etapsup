<script setup lang="ts">
import TextClamp from '@/Components/TextClamp.vue';
import { Property } from '@/Types';
import { faMapPin } from '@fortawesome/free-solid-svg-icons';
import { Link } from '@inertiajs/vue3';
import SvgIcon from '@jamescoyle/vue-icon';
import {
    mdiBathtubOutline,
    mdiBedOutline,
    mdiCountertopOutline,
    mdiSilverware,
    mdiSofaSingleOutline,
} from '@mdi/js';
import { computed } from 'vue';

interface Props {
    property: Property;
}
const props = defineProps<Props>();

const formattedPrice = computed(() => {
    if (!props.property.price) return null;
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(props.property.price);
});

// Optimisation: créer un tableau des équipements pour éviter la répétition
const amenities = computed(() =>
    [
        {
            value: props.property.living_room,
            icon: mdiSofaSingleOutline,
            class: 'text-orange',
        },
        {
            value: props.property.room,
            icon: mdiBedOutline,
            class: 'text-info',
        },
        {
            value: props.property.kitchen,
            icon: mdiCountertopOutline,
            class: 'text-primary',
        },
        {
            value: props.property.bathroom,
            icon: mdiBathtubOutline,
            class: 'text-orange',
        },
        {
            value: props.property.dining_room,
            icon: mdiSilverware,
            class: 'text-orange',
        },
    ].filter((amenity) => amenity.value),
);
</script>

<template>
    <b-card no-body class="card-hover-shadow h-100 pb-0">
        <div class="position-relative">
            <img :src="property.thumb" class="card-img-top" alt="Photo" />
            <div class="card-img-overlay d-flex flex-column z-index-1 p-4">
                <div>
                    <span class="badge text-bg-danger d-none me-1">{{
                        property.city.name
                    }}</span>
                    <span class="badge text-bg-dark">{{
                        property.propertyType.label
                    }}</span>
                </div>
                <div class="w-100 mt-auto">
                    <span class="badge text-bg-white fs-6">{{
                        property.city.name
                    }}</span>
                </div>
            </div>
        </div>

        <b-card-body class="px-3">
            <b-card-title tag="h5" class="mb-0">
                <Link
                    :href="
                        route('properties.show', { property: property.slug })
                    "
                    :preserve-scroll="true"
                    :preserve-state="true"
                    class="stretched-link"
                >
                    <TextClamp
                        :text="property.title"
                        auto-resize
                        :max-lines="1"
                    />
                </Link>
            </b-card-title>
            <span class="small"
                ><font-awesome-icon :icon="faMapPin" class="me-2" />{{
                    property.city.name + ', ' + property.city.region.name
                }}</span
            >

            <ul class="nav nav-divider mb-0 mt-3">
                <li
                    v-for="(amenity, index) in amenities"
                    :key="index"
                    class="nav-item h6 fw-normal mb-0"
                >
                    <svg-icon
                        type="mdi"
                        :path="amenity.icon"
                        :class="[amenity.class, 'mb-1 me-2']"
                    />
                    {{ amenity.value }}
                </li>
            </ul>
        </b-card-body>

        <b-card-footer class="pt-0">
            <div
                class="d-sm-flex justify-content-sm-between align-items-center flex-wrap"
            >
                <div class="hstack gap-2">
                    <h5 class="fw-normal text-success mb-0">
                        {{ formattedPrice }}
                    </h5>
                </div>
                <div class="mt-sm-0 mt-2">
                    <Link
                        :href="
                            route('properties.show', {
                                property: property.slug,
                            })
                        "
                        :preserve-scroll="true"
                        :preserve-state="true"
                        class="btn btn-sm btn-primary mb-0"
                        >Voir les details</Link
                    >
                </div>
            </div>
        </b-card-footer>
    </b-card>
</template>

<style scoped>
.position-relative .card-img-top {
    height: 209px;
    object-fit: cover;
}
</style>
