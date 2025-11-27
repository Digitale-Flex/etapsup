<script setup lang="ts">
import { Property } from '@/Types/index';
import { BIconGeoAlt } from 'bootstrap-icons-vue';

defineProps<{
    property: Property;
}>();
</script>

<template>
    <section class="pt-sm-5 py-0">
        <b-container class="position-relative">
            <b-row class="mb-3">
                <b-col cols="12">
                    <div class="d-lg-flex justify-content-lg-between mb-1">
                        <div class="mb-lg-0 mb-2">
                            <h1 class="fs-2">{{ property.title }}</h1>
                            <p class="fw-bold mb-0">
                                <BIconGeoAlt class="me-1" />
                                {{ property.address }}
                            </p>
                        </div>
                    </div>
                </b-col>
            </b-row>
        </b-container>
    </section>
</template>

<style scoped></style>
