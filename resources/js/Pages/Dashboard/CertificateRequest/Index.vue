<script setup lang="ts">
import Pagination from '@/Components/Pagination.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { CertificateRequest, PaginationMeta } from '@/Types/index';
import { faFilePdf } from '@fortawesome/free-solid-svg-icons';
import { Deferred, Head, Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import 'dayjs/locale/fr';

interface CertificateRequestsPaginated {
    data: CertificateRequest[];
    meta: PaginationMeta;
}

defineOptions({ layout: DashboardLayout });

const props = defineProps<{
    certificateRequests: CertificateRequestsPaginated;
}>();

dayjs.locale('fr');

const formatDate = (date: string): string => {
    return dayjs(date).format('D MMMM, YYYY');
};

const formatBudget = (amount: number): string => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};

interface DetailItem {
    label: string;
    value: string | null;
}

const getCardDetails = (item: CertificateRequest): DetailItem[] => [
    {
        label: 'Budget',
        value: formatBudget(item.budget),
    },
    {
        label: 'Début de location',
        value: formatDate(item.rentalStart),
    },
    {
        label: 'Type de location',
        value: item.genre?.name ?? null,
    },
];
</script>

<template>
    <Head title="Mes demandes d'attestations" />
    <div>
        <b-card no-body class="border bg-transparent">
            <b-card-header class="border-bottom bg-transparent">
                <h4 class="card-header-title">Mes demandes d'attestations</h4>
            </b-card-header>

            <b-card-body>
                <Deferred data="certificateRequests">
                    <template #fallback>
                        <div class="text-center font-semibold">
                            Chargement...
                        </div>
                    </template>
                    <div>
                        <div
                            v-if="!certificateRequests.data.length"
                            class="py-4 text-center"
                        >
                            <p class="text-info mb-0">
                                Vous n'avez pas encore effectué de demande
                                d'attestation.
                            </p>
                        </div>

                        <template v-else>
                            <b-card
                                v-for="(item, i) in certificateRequests.data"
                                :key="i"
                                no-body
                                class="mb-4 border"
                            >
                                <b-card-header
                                    class="border-bottom d-md-flex justify-content-md-between align-items-center"
                                >
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="icon-lg bg-light rounded-circle flex-shrink-0"
                                        >
                                            <font-awesome-icon
                                                :icon="faFilePdf"
                                            />
                                        </div>
                                        <div class="ms-2">
                                            <b-card-title tag="h6" class="mb-0">
                                                {{ item.city?.name }} -
                                                {{ item.city?.region.name }}
                                            </b-card-title>
                                            <ul class="nav nav-divider small">
                                                <li class="nav-item">
                                                    Demande ID:
                                                    {{ item.reference }}
                                                </li>
                                                <li class="nav-item">
                                                    {{
                                                        item.rentalDeposit?.name
                                                    }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="mt-md-0 mt-2">
                                        <Link
                                            :href="
                                                route(
                                                    'dashboard.certificates.show',
                                                    item.id,
                                                )
                                            "
                                            class="btn btn-primary-soft mb-0"
                                            preserve-scroll
                                        >
                                            Consulter
                                        </Link>
                                        <p
                                            class="text-md-end mb-0"
                                            :class="`text-${item.state?.color}`"
                                        >
                                            {{ item.state?.label }}
                                        </p>
                                    </div>
                                </b-card-header>

                                <b-card-body>
                                    <b-row class="g-3">
                                        <b-col
                                            v-for="(
                                                detail, detailIndex
                                            ) in getCardDetails(item)"
                                            :key="`${item.id}-${detailIndex}`"
                                            sm="6"
                                            md="4"
                                        >
                                            <span>{{ detail.label }}</span>
                                            <h6 class="mb-0">
                                                {{ detail.value }}
                                            </h6>
                                        </b-col>
                                    </b-row>
                                </b-card-body>
                            </b-card>
                        </template>

                        <Pagination :pagination="certificateRequests.meta" />
                    </div>
                </Deferred>
            </b-card-body>
        </b-card>
    </div>
</template>

<style scoped></style>
