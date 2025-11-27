<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue';
import CustomStickyElement from '@/Components/CustomStickyElement.vue';
import Hero from '@/Pages/RealEstate/Reservation/Partials/Hero.vue';
import PropertyCard from '@/Pages/RealEstate/Reservation/Partials/PropertyCard.vue';
import ReservationForm from '@/Pages/RealEstate/Reservation/Partials/ReservationForm.vue';
import { Property, RealEstateSetting } from '@/Types/index';
import { faStar, faStarHalfAlt } from '@fortawesome/free-solid-svg-icons';
import { useForm } from '@inertiajs/vue3';
import {
    BIconArrowRight,
    BIconBracesAsterisk,
    BIconTag,
} from 'bootstrap-icons-vue';
import dayjs from 'dayjs';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';
import { computed, ref, watch } from 'vue';
import { useScreens } from 'vue-screen-utils';

dayjs.extend(utc);
dayjs.extend(timezone);

interface PageProps {
    property: Property;
    settings: RealEstateSetting;
    blockedDates: any;
    startDate: string | null;
    endDate: string | null;
    nights: number;
    guests: number;
}

const props = defineProps<PageProps>();

const datepickerRef = ref<any>(null);
const taxeRef = ref<any>(null);
const feesRef = ref<any>(null);

const form = useForm({
    period: {
        start: props.startDate ? new Date(props.startDate) : new Date(),
        end: props.endDate
            ? new Date(props.endDate)
            : dayjs().add(5, 'day').toDate(),
    },
    guests: props.guests ?? 1,
    amount: 0,
});

const { mapCurrent } = useScreens({
    xs: '0px',
    sm: '640px',
    md: '768px',
    lg: '1024px',
});
const columns = mapCurrent({ lg: 2 }, 1);
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

const nights = computed(() => {
    if (form.period.start && form.period.end) {
        return dayjs(form.period.end).diff(dayjs(form.period.start), 'day') + 1;
    }
    return 0;
});

const euro = (amount: number): string =>
    new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);

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
const totalNights = computed(() => props.property.price * nights.value);
const totalTaxes = computed(() => {
    const { price } = props.property;
    const { vat, tourist_tax } = props.settings;
    return (price * vat) / 100 + (price * tourist_tax) / 100;
});

const charges = computed(() => ({
    touristTax: (totalNights.value * props.settings.tourist_tax) / 100,
    serviceFees: (totalNights.value * props.settings.service_fees) / 100,
    consumable: (totalNights.value * props.settings.consumable) / 100,
}));

const total = computed(() => {
    if (!props.property.price || !props.settings) return 0;
    return (
        totalNights.value +
        (props.property.cleaning_fees || 0) +
        charges.value.consumable +
        charges.value.serviceFees +
        charges.value.touristTax
    );
});

const toggle = (event: Event) => {
    if (
        datepickerRef.value &&
        typeof datepickerRef.value.toggle === 'function'
    ) {
        datepickerRef.value.toggle(event);
    }
};
const taxToggle = (event: Event) => {
    if (taxeRef.value && typeof taxeRef.value.toggle === 'function') {
        taxeRef.value.toggle(event);
    }
};
const feesToggle = (event: Event) => {
    if (feesRef.value && typeof feesRef.value.toggle === 'function') {
        feesRef.value.toggle(event);
    }
};

const handleDayClick = (day: any) => {
    if (!form.period) {
        form.period = { start: new Date(), end: new Date() };
    }
    if (!form.period.start) {
        form.period.start = day.date;
    } else if (!form.period.end) {
        form.period.end = day.date;
    } else {
        form.period.start = day.date;
        form.period.end = dayjs(day.date).add(1, 'day').toDate();
    }
};

watch(
    () => form.period,
    (newVal) => {
        if (newVal.start && newVal.end) {
            calendarAttributes.value[0].dates = newVal;
        }
    },
    { deep: true },
);
watch(total, (newValue) => {
    form.amount = newValue;
});
</script>

<template>
    <AppHead :title="property.title">
        <meta
            head-key="description"
            name="description"
            :content="property.description"
        />
    </AppHead>

    <main class="pb-5">
        <Hero :property="property" />

        <b-container class="pt-4">
            <b-row class="g-4">
                <b-col xl="8">
                    <ReservationForm
                        :period="form.period"
                        :guests="form.guests"
                        :amount="total"
                        :fees="{
                            price: euro(property.price),
                            touristTax: euro(charges.touristTax),
                            consumable: euro(charges.consumable),
                            serviceFees: euro(charges.serviceFees),
                            cleaningFees: euro(property.cleaning_fees),
                        }"
                    />
                </b-col>

                <aside class="col-xl-4">
                    <div class="vstack gap-4">
                        <CustomStickyElement
                            id="price-overview"
                            data-sticky
                            data-margin-top="100"
                            data-sticky-for="1199"
                        >
                            <PropertyCard :property="property" />

                            <b-card no-body class="card-body mt-2 border">
                                <div
                                    class="d-sm-flex justify-content-sm-between align-items-center mb-3"
                                >
                                    <div>
                                        <span>Prix à partir de</span>
                                        <b-card-title class="mb-0">
                                            {{ euro(property.price) }}
                                            <span
                                                class="fs-5 mb-0"
                                                style="
                                                    font-size: 12px !important;
                                                "
                                            >
                                                par nuit
                                            </span>
                                        </b-card-title>
                                    </div>
                                    <div>
                                        <ul class="list-inline mb-2">
                                            <li
                                                class="list-inline-item h6 fw-light mb-0 me-1 me-2"
                                            >
                                                <BIconArrowRight class="me-1" />
                                                {{
                                                    property.ratings?.average.toFixed(
                                                        1,
                                                    )
                                                }}
                                            </li>
                                            <template
                                                v-for="n in displayStars.full"
                                                :key="'full-' + n"
                                            >
                                                <li
                                                    class="list-inline-item small me-0"
                                                >
                                                    <font-awesome-icon
                                                        :icon="faStar"
                                                        class="text-warning me-1"
                                                    />
                                                </li>
                                            </template>
                                            <li
                                                v-if="displayStars.half"
                                                class="list-inline-item me-0"
                                            >
                                                <font-awesome-icon
                                                    :icon="faStarHalfAlt"
                                                    class="text-warning me-1"
                                                />
                                            </li>
                                            <template
                                                v-for="n in displayStars.empty"
                                                :key="'empty-' + n"
                                            >
                                                <li
                                                    class="list-inline-item me-0"
                                                >
                                                    <font-awesome-icon
                                                        :icon="faStar"
                                                        class="me-1 text-gray-300"
                                                    />
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Sélection des dates -->
                                <div class="d-flex mb-1 w-full">
                                    <div
                                        class="me-2 w-full rounded border p-2"
                                        @click="toggle"
                                    >
                                        <small class="fw-semibold m-0 p-0"
                                            >Arrivée</small
                                        >
                                        <p class="fw-medium m-0 p-0">
                                            {{
                                                dayjs(form.period.start).format(
                                                    'DD/MM/YYYY',
                                                )
                                            }}
                                        </p>
                                    </div>
                                    <div
                                        class="w-full rounded border p-2"
                                        @click="toggle"
                                    >
                                        <small class="fw-semibold m-0 p-0"
                                            >Départ</small
                                        >
                                        <p class="fw-medium m-0 p-0">
                                            {{
                                                dayjs(form.period.end).format(
                                                    'DD/MM/YYYY',
                                                )
                                            }}
                                        </p>
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

                                <!-- Affichage des taxes détaillées dans le Popover -->

                                <Popover ref="feesRef">
                                    <div class="w-full">
                                        <ul
                                            class="list-inline mb-2 mt-4 w-full"
                                        >
                                            <li
                                                class="d-flex justify-content-between"
                                            >
                                                <span
                                                    class="me-5"
                                                    style="margin-right: 10px"
                                                >
                                                    Frais de ménage :
                                                </span>
                                                {{
                                                    euro(property.cleaning_fees)
                                                }}
                                            </li>
                                            <li
                                                class="d-flex justify-content-between"
                                            >
                                                <span
                                                    class="me-5"
                                                    style="margin-right: 10px"
                                                >
                                                    Consommable :
                                                </span>
                                                +
                                                {{ euro(charges.consumable) }}
                                            </li>
                                        </ul>
                                    </div>
                                </Popover>
                                <!-- Récapitulatif des frais -->
                                <ul class="list-inline d-grid mb-2 mt-3 gap-1">
                                    <!-- Location : (prix par nuit) x (nombre de nuits) -->
                                    <li class="d-flex justify-content-between">
                                        <span>
                                            <BIconTag class="me-1" />
                                            {{ euro(property.price) }} x
                                            {{ nights }} nuit(s) :
                                        </span>
                                        {{ euro(totalNights) }}
                                    </li>
                                    <!-- Taxes totales : TVA + Taxe de séjour sur l'ensemble des nuits -->
                                    <li class="d-flex justify-content-between">
                                        <span>
                                            <BIconTag class="me-1" /> Taxe de
                                            séjour :
                                        </span>
                                        + {{ euro(charges.touristTax) }}
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span>
                                            <BIconTag class="me-1" /> Frais de
                                            service :
                                        </span>
                                        + {{ euro(charges.serviceFees) }}
                                    </li>
                                    <li
                                        class="d-flex justify-content-between border-bottom pb-2"
                                    >
                                        <u>
                                            <span
                                                style="cursor: pointer"
                                                @click="feesToggle"
                                            >
                                                <BIconTag class="me-1" /> Autres
                                                frais :
                                            </span>
                                        </u>
                                        +
                                        {{
                                            euro(
                                                property.cleaning_fees +
                                                    charges.consumable,
                                            )
                                        }}
                                    </li>
                                    <!-- Total à payer : location + taxes + frais de ménage + consommable -->
                                    <li
                                        class="d-flex justify-content-between fw-bold pt-1"
                                    >
                                        <span>
                                            <BIconBracesAsterisk class="me-1" />
                                            Total :
                                        </span>
                                        {{ euro(total) }}
                                    </li>
                                </ul>
                            </b-card>
                        </CustomStickyElement>
                    </div>
                </aside>
            </b-row>
        </b-container>
    </main>
</template>

<style scoped></style>
