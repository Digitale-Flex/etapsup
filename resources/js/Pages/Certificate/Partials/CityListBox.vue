<script setup lang="ts">
import { City } from '@/Types/index';

defineProps<{
    cities?: City[];
}>();

const formatBudget = (amount: number): string => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};
</script>

<template>
    <Listbox
        :options="cities"
        filter
        striped
        :virtual-scroller-options="{ itemSize: 38 }"
        empty-filter-message="Aucune ville trouv茅e"
        empty-message=""
        list-style="height: 1200px"
        option-label="name"
        option-value="id"
        class="w-full"
    >
        <template #option="{ option }">
            <div class="d-flex justify-content-between w-full">
                <div>{{ option.name }}</div>
                <div>{{ formatBudget(option.budget) }}</div>
            </div>
        </template>
    </Listbox>
</template>

<style scoped></style>
