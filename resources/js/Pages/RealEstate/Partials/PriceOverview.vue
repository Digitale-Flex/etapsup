<script setup lang="ts">
import CustomStickyElement from '@/Components/CustomStickyElement.vue';
import { Property, RealEstateSetting } from '@/Types/index';
import { faStar, faStarHalfAlt } from '@fortawesome/free-solid-svg-icons';
import { Link, useForm } from '@inertiajs/vue3';
import {
    BIconArrowRight,
    BIconBracesAsterisk,
    BIconTag,
} from 'bootstrap-icons-vue';
import dayjs from 'dayjs';
import 'dayjs/locale/fr';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';
import { memoize } from 'lodash-es';
import type { Calendar } from 'v-calendar';
import 'v-calendar/style.css';
import { computed, ref, watchEffect } from 'vue';
import { useScreens } from 'vue-screen-utils';

// Configuration de dayjs
dayjs.locale('fr');
dayjs.extend(utc);
dayjs.extend(timezone);

// Utilisation de vue-screen-utils pour déterminer le nombre de colonnes du calendrier
const { mapCurrent } = useScreens({
    xs: '0px',
    sm: '640px',
    md: '768px',
    lg: '1024px',
});
const columns = mapCurrent({ lg: 2 }, 1);

// Déclaration des props et déstructuration
interface CProps {
    property: Property;
    settings: RealEstateSetting;
}

const props = defineProps<CProps>();
const { property, settings } = props;

// Fonction utilitaire de formatage en Euro (mémoïsée pour la performance)
const euro = memoize((amount: number): string =>
    new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount),
);

// Helper pour le formatage des dates
const formatDate = (date: Date | null): string =>
    date ? dayjs(date).format('DD/MM/YYYY') : '';

// Calcul de l'affichage des étoiles selon la note moyenne
const displayStars = computed(() => {
    const average = property.ratings?.average ?? 0;
    const fullStars = Math.floor(average);
    const hasHalfStar = average % 1 >= 0.5;
    return {
        full: fullStars,
        half: hasHalfStar,
        empty: 5 - fullStars - (hasHalfStar ? 1 : 0),
    };
});

// Dates par défaut pour le calendrier
const defaultStartDate = dayjs().startOf('day');
const defaultEndDate = dayjs().add(5, 'day').startOf('day');

// Utilisation de useForm pour le formulaire de réservation
interface FormData {
    period: { start: Date | null; end: Date | null };
    guests: number;
}

const form = useForm<FormData>({
    period: {
        start: defaultStartDate.toDate(),
        end: defaultEndDate.toDate(),
    },
    guests: 0,
});

// Calcul du nombre de nuits
const nights = computed(() => {
    if (!form.period.start || !form.period.end) return 0;
    return dayjs(form.period.end).diff(form.period.start, 'day') + 1;
});

const totalNights = computed(() => property.price * nights.value);

const charges = computed(() => ({
    touristTax: (totalNights.value * props.settings.tourist_tax) / 100,
    serviceFees: (totalNights.value * props.settings.service_fees) / 100,
    consumable: (totalNights.value * props.settings.consumable) / 100,
}));

const taxDetails = computed(() => {
    if (!property.price || !settings) return null;

    const touristTax = (totalNights.value * settings.tourist_tax) / 100;

    return {
        perNight: {
            touristTax: touristTax, // Taxe de séjour par nuit (pour l'affichage)
        },
        total: touristTax,
    };
});

const total = computed(() => {
    if (!property.price || !settings) return 0;
    return (
        totalNights.value +
        (property.cleaning_fees || 0) +
        charges.value.consumable +
        charges.value.serviceFees +
        charges.value.touristTax
    );
});

// Affichage des dates formatées
const arrivalDate = computed(() => formatDate(form.period.start));
const departureDate = computed(() => formatDate(form.period.end));

// Références pour le Popover et le calendrier
const datepickerRef = ref<{ toggle: (e: Event) => void }>();
const taxeRef = ref<{ toggle: (e: Event) => void }>();
const feesRef = ref<{ toggle: (e: Event) => void }>();
const calendarRef = ref<InstanceType<typeof Calendar>>();

// Attributs du calendrier pour surligner la plage sélectionnée
const calendarAttributes = ref([
    {
        key: 'selectedRange',
        highlight: {
            color: 'blue',
            fillMode: 'light',
        },
        dates: form.period,
    },
]);

// Fonction pour basculer l'affichage du datepicker
const toggle = (event: Event) => datepickerRef.value?.toggle(event);
const taxToggle = (event: Event) => taxeRef.value?.toggle(event);
const feesToggle = (event: Event) => feesRef.value?.toggle(event);

// Gestion du clic sur un jour dans le calendrier
const handleDayClick = (day: { date: Date }) => {
    const period = form.period;

    if (!period.start || (period.start && period.end)) {
        form.period = { start: day.date, end: null };
    } else {
        form.period.end = day.date;
    }
};

// Vérification des props
watchEffect(() => {
    if (!settings) {
        console.error('Settings manquants');
    }
});
</script>

<template>
    <CustomStickyElement
        id="price-overview"
        data-sticky
        data-margin-top="100"
        data-sticky-for="1199"
    >
        <div class="alert alert-primary text-center">
            Catégorie :
            <b
                >{{ property.category?.label }}
                <span v-if="property.subCategory">
                    | {{ property.subCategory?.label }}</span
                >
            </b>
        </div>
        <b-card no-body class="card-body border">
            <div
                class="d-sm-flex justify-content-sm-between align-items-center mb-3"
            >
                <div>
                    <span>Prix à partir de</span>
                    <b-card-title class="mb-0">
                        {{ euro(property.price) }}
                        <span
                            class="fs-5 mb-0"
                            style="font-size: 12px !important"
                        >
                            par nuit
                        </span>
                    </b-card-title>
                </div>
                <div>
                    <ul class="list-inline mb-2">
                        <li class="list-inline-item h6 fw-light mb-0 me-1 me-2">
                            <BIconArrowRight class="me-1" />
                            {{ property.ratings?.average.toFixed(1) }}
                        </li>
                        <template v-for="n in 5" :key="n">
                            <li class="list-inline-item small me-0">
                                <font-awesome-icon
                                    :icon="
                                        n <= displayStars.full
                                            ? faStar
                                            : n === displayStars.full + 1 &&
                                                displayStars.half
                                              ? faStarHalfAlt
                                              : faStar
                                    "
                                    :class="[
                                        'me-1',
                                        n <= displayStars.full ||
                                        (n === displayStars.full + 1 &&
                                            displayStars.half)
                                            ? 'text-warning'
                                            : 'text-gray-300',
                                    ]"
                                />
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Sélection des dates -->
            <div class="d-flex mb-1 w-full">
                <div
                    class="date-box me-2 w-full rounded border p-2"
                    @click="toggle"
                >
                    <small class="fw-semibold m-0 p-0">Arrivée</small>
                    <p class="fw-medium m-0 p-0">{{ arrivalDate }}</p>
                </div>
                <div class="date-box w-full rounded border p-2" @click="toggle">
                    <small class="fw-semibold m-0 p-0">Départ</small>
                    <p class="fw-medium m-0 p-0">{{ departureDate }}</p>
                </div>
            </div>
            <div>
                <Popover ref="datepickerRef">
                    <v-calendar
                        ref="calendarRef"
                        expanded
                        :columns="columns"
                        v-model.range="form.period"
                        :attributes="calendarAttributes"
                        is-range
                        @dayclick="handleDayClick"
                    />
                </Popover>
                <div>
                    <InputNumber
                        v-model="form.guests"
                        suffix=" invité(s)"
                        inputId="guest"
                        showButtons
                        fluid
                    />
                </div>
            </div>

            <Popover ref="feesRef">
                <div class="w-full">
                    <ul class="list-inline mb-2 mt-4 w-full">
                        <li class="d-flex justify-content-between">
                            <span class="me-5" style="margin-right: 10px"
                                >Frais de ménage :</span
                            >
                            {{ euro(property.cleaning_fees) }}
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="me-5" style="margin-right: 10px"
                                >Consommable :</span
                            >
                            + {{ euro(charges.consumable) }}
                        </li>
                    </ul>
                </div>
            </Popover>

            <!-- Récapitulatif des frais -->
            <ul class="list-inline d-grid mb-2 mt-3 gap-1">
                <li class="d-flex justify-content-between">
                    <span>
                        <BIconTag class="me-1" />
                        {{ euro(property.price) }} x {{ nights }} nuit(s) :
                    </span>
                    {{ euro(totalNights) }}
                </li>
                <li class="d-flex justify-content-between">
                    <span> <BIconTag class="me-1" /> Taxe de séjour : </span>
                    + {{ euro(charges.touristTax) }}
                </li>
                <li class="d-flex justify-content-between">
                    <span> <BIconTag class="me-1" /> Frais de service : </span>
                    + {{ euro(charges.serviceFees) }}
                </li>
                <li class="d-flex justify-content-between border-bottom pb-2">
                    <u>
                        <span style="cursor: pointer" @click="feesToggle">
                            <BIconTag class="me-1" /> Autres frais :
                        </span>
                    </u>
                    +
                    {{ euro(property.cleaning_fees + charges.consumable) }}
                </li>
                <li class="d-flex justify-content-between fw-bold pt-1">
                    <span> <BIconBracesAsterisk class="me-1" /> Total : </span>
                    {{ euro(total) }}
                </li>
            </ul>

            <div class="d-grid mt-3">
                <Link
                    :href="
                        route('reservations.edit', {
                            reservation: property.slug,
                            nights: nights,
                            guests: form.guests,
                            start_date: form.period.start
                                ? dayjs(form.period.start).format('YYYY-MM-DD')
                                : null,
                            end_date: form.period.end
                                ? dayjs(form.period.end).format('YYYY-MM-DD')
                                : null,
                        })
                    "
                    prefetch
                    class="btn btn-lg btn-primary-soft mb-0"
                >
                    Procéder à la réservation
                </Link>
            </div>
        </b-card>
    </CustomStickyElement>
</template>

<style scoped>
.w-full {
    width: 100% !important;
}
</style>
