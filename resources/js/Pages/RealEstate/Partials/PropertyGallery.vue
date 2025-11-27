<script setup lang="ts">
import CustomGLightbox from '@/Components/CustomGLightbox.vue';
import { BIconFullscreen } from 'bootstrap-icons-vue';
import { computed } from 'vue';

import type { MediaImage } from '@/Types/index';

interface Props {
    images: MediaImage[];
}

const props = defineProps<Props>();

// Trier les images par order et obtenir les URLs
const sortedImages = computed(() => {
    return props.images
        .sort((a, b) => a.order - b.order)
        .map((img) => img.original_url);
});

// Obtenir les 4 premières images pour l'affichage en grille
const displayImages = computed(() => {
    return sortedImages.value.slice(0, 4);
});

// Nombre total d'images restantes
const remainingImagesCount = computed(() => {
    return Math.max(0, sortedImages.value.length - 4);
});
</script>

<template>
    <section class="card-grid pt-0">
        <b-container>
            <b-row class="g-2">
                <b-col md="6">
                    <CustomGLightbox :link="displayImages[0]">
                        <b-card
                            no-body
                            class="card-grid-lg card-element-hover card-overlay-hover overflow-hidden"
                            :style="{
                                backgroundImage: `url(${displayImages[0]})`,
                                backgroundPosition: 'center left',
                                backgroundSize: 'cover',
                            }"
                        >
                            <div
                                class="hover-element position-absolute w-100 h-100"
                            >
                                <BIconFullscreen
                                    class="h4 position-absolute top-50 start-50 translate-middle bg-dark rounded-1 lh-1 p-2 text-white"
                                />
                            </div>
                        </b-card>
                    </CustomGLightbox>
                </b-col>

                <b-col md="6">
                    <b-row class="g-2">
                        <b-col cols="12" v-if="displayImages[1]">
                            <CustomGLightbox :link="displayImages[1]">
                                <b-card
                                    no-body
                                    class="card-grid-sm card-element-hover card-overlay-hover overflow-hidden"
                                    :style="{
                                        backgroundImage: `url(${displayImages[1]})`,
                                        backgroundPosition: 'center left',
                                        backgroundSize: 'cover',
                                    }"
                                >
                                    <div
                                        class="hover-element position-absolute w-100 h-100"
                                    >
                                        <BIconFullscreen
                                            class="h4 position-absolute top-50 start-50 translate-middle bg-dark rounded-1 lh-1 p-2 text-white"
                                        />
                                    </div>
                                </b-card>
                            </CustomGLightbox>
                        </b-col>

                        <b-col md="6" v-if="displayImages[2]">
                            <CustomGLightbox :link="displayImages[2]">
                                <b-card
                                    no-body
                                    class="card-grid-sm card-element-hover card-overlay-hover overflow-hidden"
                                    :style="{
                                        backgroundImage: `url(${displayImages[2]})`,
                                        backgroundPosition: 'center left',
                                        backgroundSize: 'cover',
                                    }"
                                >
                                    <div
                                        class="hover-element position-absolute w-100 h-100"
                                    >
                                        <BIconFullscreen
                                            class="h4 position-absolute top-50 start-50 translate-middle bg-dark rounded-1 lh-1 p-2 text-white"
                                        />
                                    </div>
                                </b-card>
                            </CustomGLightbox>
                        </b-col>

                        <b-col md="6" v-if="displayImages[3]">
                            <b-card
                                no-body
                                class="card-grid-sm overflow-hidden"
                                :style="{
                                    backgroundImage: `url(${displayImages[3]})`,
                                    backgroundPosition: 'center left',
                                    backgroundSize: 'cover',
                                }"
                            >
                                <div class="bg-overlay bg-dark opacity-7"></div>
                                <CustomGLightbox
                                    v-for="(image, index) in sortedImages"
                                    :key="index"
                                    :link="image"
                                    :className="
                                        index === 3
                                            ? 'stretched-link z-index-9'
                                            : ''
                                    "
                                ></CustomGLightbox>
                                <div
                                    v-if="remainingImagesCount > 0"
                                    class="card-img-overlay d-flex h-100 w-100"
                                >
                                    <b-card-title
                                        tag="h6"
                                        class="fw-light text-decoration-underline m-auto"
                                    >
                                        <span class="text-white"
                                            >+{{
                                                remainingImagesCount
                                            }}
                                            photos</span
                                        >
                                    </b-card-title>
                                </div>
                            </b-card>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
        </b-container>
    </section>
</template>

<style scoped></style>
