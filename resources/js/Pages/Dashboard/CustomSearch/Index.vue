<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

defineOptions({ layout: DashboardLayout });
</script>

<template>
    <Head title="Mes demandes d'accompagnement" />
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle fa-2x me-3"></i>
        <div>
            <h5 class="mb-1">Demande prise en charge</h5>
            <p class="mb-0">
                Votre dossier est maintenant en cours de traitement par nos
                services.
            </p>
        </div>
    </div>
</template>

<style scoped></style>
