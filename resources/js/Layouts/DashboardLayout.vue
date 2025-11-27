<script setup lang="ts">
import AppHeader from "@/Layouts/Partials/AppHeader.vue";
import {faSlidersH} from '@fortawesome/free-solid-svg-icons'
import {computed, onMounted, ref} from "vue";
import AppFooter from "@/Layouts/Partials/AppFooter.vue";
import BackToTop from "@/Components/BackToTop.vue";
import SideBar from "@/Layouts/Partials/SideBar.vue";

const openMobileMenu = ref<boolean>(false)
const isMobileMenu = computed(() => {
    return window.innerWidth <= 1200
})

onMounted(() => {
    document.body.classList.add('dashboard')
})
</script>

<template>
    <div>
        <AppHeader/>

        <main>
            <section class="pt-3">
                <b-container>
                    <b-row>
                        <side-bar />

                        <b-col lg="8" xl="9">
                            <div class="d-grid mb-0 d-lg-none w-100">
                                <b-button class="mb-4" variant="primary" @click="openMobileMenu = !openMobileMenu">
                                    <font-awesome-icon :icon="faSlidersH"/>
                                    Menu
                                </b-button>
                            </div>

                            <slot/>
                            <Toast />
                        </b-col>
                    </b-row>
                </b-container>
            </section>
        </main>

        <AppFooter/>
        <back-to-top />
    </div>
</template>

<style scoped>

</style>
