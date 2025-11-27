<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue';
import CustomStickyElement from '@/Components/CustomStickyElement.vue';
import Hero from '@/Pages/RealEstate/Reservation/Partials/Hero.vue';
import MonthlyReservationForm from '@/Pages/RealEstate/Reservation/Partials/MonthlyReservationForm.vue';
import PropertyCard from '@/Pages/RealEstate/Reservation/Partials/PropertyCard.vue';
import { Property, RealEstateSetting } from '@/Types/index';
import { faStar, faStarHalfAlt } from '@fortawesome/free-solid-svg-icons';
import { useForm, usePage } from '@inertiajs/vue3';
import {
    BIconArrowRight,
    BIconBracesAsterisk,
    BIconTag,
} from 'bootstrap-icons-vue';
import dayjs from 'dayjs';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';
import { computed, watchEffect } from 'vue';

dayjs.extend(utc);
dayjs.extend(timezone);

interface PageProps {
    property: Property;
    settings: RealEstateSetting;
    blockedDates: any;
    startDate: string | null;
    endDate: string | null;
    month: number;
    requireFiles: boolean;
}

const props = defineProps<PageProps>();
const user = usePage().props.auth?.user;

const euro = (amount: number): string =>
    new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);

const total = computed(
    () =>
        props.property.price * 2 +
        props.settings.application_fees,
);

const form = useForm({
    property_id: props.property.id,
    surname: user?.surname,
    name: user?.name,
    email: user?.email,
    phone: user?.phone,
    place_birth: user?.place_birth,
    date_birth: user?.date_birth
        ? dayjs(user?.date_birth).utc().toDate()
        : undefined,
    nationality: user?.nationality,
    status: '',
    address: '',
    period: {
        start: props.startDate ? new Date(props.startDate) : new Date(),
        end: props.endDate
            ? new Date(props.endDate)
            : dayjs().add(5, 'day').toDate(),
    },
    month: props.month ?? 2,
    reason: '',
    files: [],
    fees: {
        price: euro(props.property.price),
        total: euro(props.property.price * (props.month ?? 2)),
        caution: euro(props.property.price),
        firstMonthRent: euro(props.property.price),
        applicationFees: euro(props.settings.application_fees),
    },
    amount: total.value,
    requireFiles: props.requireFiles,
});

watchEffect(() => {
    if (form.period.start && form.month > 0) {
        const startDate = dayjs(form.period.start);
        form.period.end = startDate.add(form.month, 'month').toDate();
    }
});

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

const dateTransform = (date: Date | null): string =>
    date ? dayjs(date).format('DD/MM/YYYY') : '';
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
                    <MonthlyReservationForm :form="form" />
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
                                        <span>Loyer mensuel</span>
                                        <b-card-title class="mb-0">
                                            {{ euro(property.price) }}
                                            <span
                                                class="fs-5 mb-0"
                                                style="
                                                    font-size: 12px !important;
                                                "
                                            >
                                                par mois
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

                                <div class="d-flex mb-1 gap-1">
                                    <div>
                                        <FloatLabel variant="in">
                                            <DatePicker
                                                v-model="form.period.start"
                                                inputId="arrival"
                                                showIcon
                                                iconDisplay="input"
                                            />
                                            <label
                                                for="arrival"
                                                class="fw-semibold"
                                                >Arrivée</label
                                            >
                                        </FloatLabel>
                                    </div>

                                    <div>
                                        <IftaLabel variant="in">
                                            <input-text
                                                :value="
                                                    dateTransform(
                                                        form.period.end,
                                                    )
                                                "
                                                inputId="departure"
                                                iconDisplay="input"
                                                readonly
                                                class="w-full"
                                            />
                                            <label
                                                for="departure"
                                                class="fw-semibold"
                                                >Départ</label
                                            >
                                        </IftaLabel>
                                    </div>
                                </div>
                                <div>
                                    <FloatLabel variant="in">
                                        <InputNumber
                                            v-model="form.month"
                                            suffix=" mois"
                                            inputId="month"
                                            :min="2"
                                            showButtons
                                            fluid
                                        />
                                        <label for="month" class="fw-semibold">
                                            Pour combien de mois réservez-vous ?
                                        </label>
                                    </FloatLabel>
                                </div>

                                <ul class="list-inline d-grid mb-2 mt-3 gap-1">
                                    <li class="d-flex justify-content-between">
                                        <span>
                                            Loyer mensuel × {{ form.month }}
                                        </span>
                                        {{ euro(property.price * form.month) }}
                                    </li>
                                </ul>
                                <ul
                                    class="list-inline d-grid mb-2 mt-1 gap-1 border p-3"
                                >
                                    <li class="d-flex justify-content-between">
                                        <span>
                                            <BIconTag class="me-1" /> Caution (1
                                            mois de loyer) :
                                        </span>
                                        {{ euro(property.price) }}
                                    </li>
                                    <li
                                        class="d-flex justify-content-between mt-2 pb-2"
                                    >
                                        <span>
                                            <BIconTag class="me-1" /> 1<sup
                                                >er</sup
                                            >
                                            mois de loyer :
                                        </span>
                                        + {{ euro(property.price) }}
                                    </li>
                                    <li
                                        class="d-flex justify-content-between border-bottom pb-2"
                                    >
                                        <span>
                                            <BIconTag class="me-1" /> Frais de
                                            réservation :
                                        </span>
                                        +
                                        {{
                                            euro(
                                                settings?.application_fees || 0,
                                            )
                                        }}
                                    </li>
                                    <li
                                        class="d-flex justify-content-between fw-bold pt-1"
                                    >
                                        <span>
                                            <BIconBracesAsterisk class="me-1" />
                                            A payer :
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
