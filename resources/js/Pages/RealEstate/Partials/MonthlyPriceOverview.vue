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
import 'v-calendar/style.css';
import { computed, watchEffect } from 'vue';

dayjs.locale('fr');
dayjs.extend(utc);
dayjs.extend(timezone);

interface CProps {
    property: Property;
    settings: RealEstateSetting;
}
interface FormData {
    period: { start: Date | null; end: Date | null };
    month: number;
}

const props = defineProps<CProps>();
const { property, settings } = props;

const euro = memoize((amount: number): string =>
    new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount),
);

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

const form = useForm<FormData>({
    period: {
        start: dayjs().add(3, 'day').toDate(),
        end: dayjs().add(3, 'day').add(2, 'month').toDate(),
    },
    month: 2,
});

watchEffect(() => {
    if (form.period.start && form.month > 0) {
        const startDate = dayjs(form.period.start);
        form.period.end = startDate.add(form.month, 'month').toDate();
    } else {
        form.period.end = null;
    }
});

const total = computed(
    () =>
        property.price * 2 + settings.application_fees,
);

const dateTransform = (date: Date | null): string =>
    date ? dayjs(date).format('DD/MM/YYYY') : '';
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
                    <span>Loyer mensuel</span>
                    <b-card-title class="mb-0">
                        {{ euro(property.price) }}
                        <span
                            class="fs-5 mb-0"
                            style="font-size: 12px !important"
                        >
                            par mois
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
            <div class="d-flex mb-1 w-full gap-1">
                <FloatLabel variant="in">
                    <DatePicker
                        v-model="form.period.start"
                        inputId="arrival"
                        showIcon
                        iconDisplay="input"
                    />
                    <label for="arrival" class="fw-semibold">Arrivée</label>
                </FloatLabel>
                <IftaLabel variant="in" class="label-always-top">
                    <input-text
                        :value="dateTransform(form.period.end)"
                        inputId="departure"
                        iconDisplay="input"
                    />
                    <label for="departure" class="fw-semibold">Départ</label>
                </IftaLabel>
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
                    <label for="departure" class="fw-semibold"
                        >Pour combien de mois réservez-vous ?</label
                    >
                </FloatLabel>
            </div>

            <!-- Récapitulatif des frais -->
            <ul class="list-inline d-grid mb-2 mt-3 gap-1 pl-5">
                <li class="d-flex justify-content-between">
                    <span> Loyer mensuel × {{ form.month }} </span>
                    {{ euro(property.price * form.month) }}
                </li>
            </ul>
            <ul class="list-inline d-grid mb-2 mt-1 gap-1 border p-3">
                <li class="d-flex justify-content-between">
                    <span>
                        <BIconTag class="me-1" /> Caution (1 mois de loyer) :
                    </span>
                    {{ euro(property.price) }}
                </li>
                <li class="d-flex justify-content-between mt-2 pb-2">
                    <span>
                        <BIconTag class="me-1" /> 1<sup>er</sup> mois de loyer :
                    </span>
                    + {{ euro(property.price) }}
                </li>
                <li class="d-flex justify-content-between border-bottom pb-2">
                    <span>
                        <BIconTag class="me-1" /> Frais de réservation :
                    </span>
                    + {{ euro(settings?.application_fees || 0) }}
                </li>
                <li class="d-flex justify-content-between fw-bold pt-1">
                    <span> <BIconBracesAsterisk class="me-1" /> Total : </span>
                    {{ euro(total) }}
                </li>
            </ul>

            <div class="d-grid mt-3">
                <Link
                    :href="
                        route('monthlies.show', {
                            monthly: property.slug,
                            month: form.month,
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

<style scoped></style>
