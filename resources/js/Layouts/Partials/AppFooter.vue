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
    <footer class="bg-dark pt-5">
        <b-container>
            <b-row class="g-4">
                <b-col lg="4">
                    <Link href="/" class="text-3xl font-extrabold">
                        YOD INVEST
                    </Link>
                    <p class="text-body-secondary my-3">
                        « Réalisez votre projet immobilier. »
                    </p>
                    <p class="mb-2">
                        <a
                            href="#"
                            class="text-body-secondary text-primary-hover"
                        >
                            <BIconTelephone class="me-2" />
                            09 82 70 10 71
                        </a>
                    </p>
                    <p class="mb-0">
                        <a
                            href="#"
                            class="text-body-secondary text-primary-hover"
                        >
                            <BIconEnvelope class="me-2" />
                            contact@yod-invest.fr
                        </a>
                    </p>
                </b-col>

                <b-col lg="8" class="ms-auto">
                    <b-row class="g-4 d-flex justify-content-end">
                        <img
                            src="/images/logo-yod.jpg"
                            class="img-thumbnail img-responsive logo-yod-invest"
                        />
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

                <b-col sm="5" md="6" lg="3" class="text-sm-end">
                    <h5 class="mb-2 text-white">Suivez-nous</h5>
                    <ul
                        class="list-inline hstack justify-content-end mb-0 mt-3 gap-1"
                    >
                        <li class="list-inline-item">
                            <a
                                href="https://www.facebook.com/people/YOD-INVEST/100091850224201/"
                                class="btn btn-sm bg-facebook mb-0 px-2"
                                target="_blank"
                            >
                                <font-awesome-icon
                                    :icon="faFacebook"
                                    class="fab fa-fw"
                                />
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a
                                href="https://www.instagram.com/yod_invest/"
                                class="btn btn-sm bg-instagram mb-0 px-2 shadow"
                                target="_blank"
                            >
                                <font-awesome-icon
                                    :icon="faInstagram"
                                    class="fab fa-fw"
                                />
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a
                                href="https://www.linkedin.com/company/yod-invest-officiel/posts/?feedView=all"
                                class="btn btn-sm bg-linkedin mb-0 px-2 shadow"
                                target="_blank"
                            >
                                <font-awesome-icon
                                    :icon="faLinkedinIn"
                                    class="fab fa-fw"
                                />
                            </a>
                        </li>
                    </ul>
                </b-col>
            </b-row>

            <hr class="mb-0 mt-4" />

            <!-- <b-row>
               <b-container>
                 <div
                     class="d-lg-flex justify-content-between align-items-center py-3 text-center text-lg-start"
                 >
                   <div class="text-body-secondary text-primary-hover">
                     Copyrights ©{{ currentYear }} Booking. Build by
                     <a :href="developedByLink" class="text-body-secondary">{{ developedBy }}</a>.
                   </div>
                   <div class="nav mt-2 mt-lg-0">
                     <ul class="list-inline text-primary-hover mx-auto mb-0">
                       <li class="list-inline-item me-0">
                         <a class="nav-link text-body-secondary py-1" href="#">Privacy policy</a>
                       </li>
                       <li class="list-inline-item me-0">
                         <a class="nav-link text-body-secondary py-1" href="#">Terms and conditions</a>
                       </li>
                       <li class="list-inline-item me-0">
                         <a class="nav-link text-body-secondary py-1 pe-0" href="#">Refund policy</a>
                       </li>
                     </ul>
                   </div>
                 </div>
               </b-container>
             </b-row>-->
        </b-container>
    </footer>
</template>

<style scoped>
.logo-yod-invest {
    height: 110px;
    width: auto;
}
</style>
