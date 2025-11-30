<script setup lang="ts">
import {
    faFacebook,
    faInstagram,
    faLinkedinIn,
} from '@fortawesome/free-brands-svg-icons';
import { Link } from '@inertiajs/vue3';
import { BIconEnvelope, BIconTelephone } from 'bootstrap-icons-vue';
</script>

<template>
    <!-- UI-Fix-1.1: Adaptation footer EtapSup -->
    <footer class="pt-5" style="background-color: #1e3a8a;">
        <b-container>
            <b-row class="g-4">
                <b-col lg="4">
                    <Link href="/" class="text-3xl font-extrabold text-white">
                        ETAPSUP
                    </Link>
                    <p class="text-white my-3 opacity-75">
                        « Votre passerelle vers les études supérieures à l'étranger. »
                    </p>
                    <p class="mb-2">
                        <a
                            href="mailto:contact@etapsup.com"
                            class="text-white opacity-75 text-decoration-none"
                            style="transition: opacity 0.2s;"
                            onmouseover="this.style.opacity='1'"
                            onmouseout="this.style.opacity='0.75'"
                        >
                            <BIconEnvelope class="me-2" />
                            contact@etapsup.com
                        </a>
                    </p>
                    <p class="mb-0">
                        <a
                            href="tel:+33123456789"
                            class="text-white opacity-75 text-decoration-none"
                            style="transition: opacity 0.2s;"
                            onmouseover="this.style.opacity='1'"
                            onmouseout="this.style.opacity='0.75'"
                        >
                            <BIconTelephone class="me-2" />
                            +33 1 23 45 67 89
                        </a>
                    </p>
                </b-col>

                <b-col lg="8" class="ms-auto">
                    <b-row class="g-4 d-flex justify-content-end align-items-center">
                        <div class="text-end">
                            <span class="text-white font-extrabold" style="font-size: 3rem; letter-spacing: 2px;">
                                ETAPSUP
                            </span>
                            <p class="text-white opacity-75 mt-2">Plateforme d'orientation étudiante</p>
                        </div>
                    </b-row>
                </b-col>
            </b-row>

            <b-row class="g-4 justify-content-between mt-md-2 mt-0">
                <b-col sm="7" md="6" lg="4">
                    <h5 class="mb-2 text-white">Paiement et sécurité</h5>
                    <ul class="list-inline mb-0 mt-3">
                        <li class="list-inline-item">
                            <a href="#"
                                ><img
                                    src="/images/front/element/paypal.svg"
                                    class="h-30px"
                                    alt=""
                            /></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#"
                                ><img
                                    src="/images/front/element/visa.svg"
                                    class="h-30px"
                                    alt=""
                            /></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#"
                                ><img
                                    src="/images/front/element/mastercard.svg"
                                    class="h-30px"
                                    alt=""
                            /></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#"
                                ><img
                                    src="/images/front/element/expresscard.svg"
                                    class="h-30px"
                                    alt=""
                            /></a>
                        </li>
                    </ul>
                </b-col>

                <!-- UI-Fix-1.1: Réseaux sociaux EtapSup -->
                <b-col sm="5" md="6" lg="3" class="text-sm-end">
                    <h5 class="mb-2 text-white">Suivez-nous</h5>
                    <ul
                        class="list-inline hstack justify-content-end mb-0 mt-3 gap-1"
                    >
                        <li class="list-inline-item">
                            <a
                                href="#"
                                class="btn btn-sm mb-0 px-2"
                                style="background-color: #dc2626;"
                                target="_blank"
                            >
                                <font-awesome-icon
                                    :icon="faFacebook"
                                    class="fab fa-fw text-white"
                                />
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a
                                href="#"
                                class="btn btn-sm mb-0 px-2 shadow"
                                style="background-color: #dc2626;"
                                target="_blank"
                            >
                                <font-awesome-icon
                                    :icon="faInstagram"
                                    class="fab fa-fw text-white"
                                />
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a
                                href="#"
                                class="btn btn-sm mb-0 px-2 shadow"
                                style="background-color: #dc2626;"
                                target="_blank"
                            >
                                <font-awesome-icon
                                    :icon="faLinkedinIn"
                                    class="fab fa-fw text-white"
                                />
                            </a>
                        </li>
                    </ul>
                </b-col>
            </b-row>

            <!-- UI-Fix-1.1: Séparateur blanc sur fond bleu -->
            <hr class="mb-0 mt-4" style="border-color: rgba(255, 255, 255, 0.2);" />
        </b-container>
    </footer>
</template>

<style scoped>
.logo-yod-invest {
    height: 110px;
    width: auto;
}
</style>
