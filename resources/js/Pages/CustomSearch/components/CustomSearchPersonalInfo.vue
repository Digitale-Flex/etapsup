<script setup lang="ts">
import { useCustomSearchStore } from '@/Stores/customSearch';
import { Country, Partner, RentalDeposit, User } from '@/Types/index';
import { BIconPerson } from 'bootstrap-icons-vue';
import { storeToRefs } from 'pinia';

interface Props {
    user: User;
    countries: Country[];
    partners: Partner[];
    rentalDeposits: RentalDeposit[];
}

const props = defineProps<Props>();
const store = useCustomSearchStore();

const { r$ } = storeToRefs(store);

/* onMounted(() => {
    r$.value.$value = {
        name: props.user.name,
        surname: props.user.surname,
        email: props.user.email,
        phone: props.user.phone,
        place_birth: props.user.place_birth,
        date_birth: props.user.date_birth,
        nationality: props.user.nationality,
        passport_number: props.user.passport_number,
        country_birth_id: props.user.country?.id,
    };
}); */
</script>

<template>
    <b-card no-body class="shadow">
        <b-card-header class="border-bottom">
            <b-card-title class="mb-0">
                <BIconPerson class="mb-1" />
                Informations personnelles
            </b-card-title>
        </b-card-header>

        <b-card-body class="p-4">
            <div class="row g-4">
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="surname">Nom *</label>
                        <input-text
                            id="surname"
                            v-model="r$.$value.surname"
                            :invalid="r$.$fields.surname.$error"
                            :disabled="store.processing"
                            name="surname"
                            minLength="3"
                        />
                        <Message
                            v-if="r$.$fields.surname.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.surname.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="name">Prénom *</label>
                        <input-text
                            id="name"
                            v-model="r$.$value.name"
                            :invalid="r$.$fields.name.$error"
                            :disabled="store.processing"
                            name="name"
                            minLength="3"
                        />
                        <Message
                            v-if="r$.$fields.name.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.name.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="email">Adresse email *</label>
                        <input-text
                            v-model="r$.$value.email"
                            :invalid="r$.$fields.email.$error"
                            :disabled="store.processing"
                            minLength="3"
                            readonly
                        />
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="phone">Téléphone (Whatsapp)*</label>
                        <input-text
                            id="phone"
                            v-model="r$.$value.phone"
                            :invalid="r$.$fields.phone.$error"
                            :disabled="store.processing"
                            minLength="10"
                        />
                        <Message
                            v-if="r$.$fields.phone.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.phone.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="date_birth">Date de naissance *</label>
                        <Calendar
                            input-id="date_birth"
                            v-model="r$.$value.date_birth"
                            :disabled="store.processing"
                            :invalid="r$.$fields.date_birth.$error"
                            dateFormat="dd/mm/yy"
                            showIcon
                            iconDisplay="input"
                        />
                        <Message
                            v-if="r$.$fields.date_birth.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.date_birth.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="place_birth">Lieu de naissance *</label>
                        <input-text
                            id="place_birth"
                            v-model="r$.$value.place_birth"
                            :invalid="r$.$fields.place_birth.$error"
                            :disabled="store.processing"
                            minLength="10"
                        />
                        <Message
                            v-if="r$.$fields.place_birth.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.place_birth.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="country_birth_id"
                            >Pays de naissance *</label
                        >
                        <Select
                            input-id="country_birth_id"
                            v-model="r$.$value.country_birth_id"
                            :options="countries"
                            :disabled="store.processing"
                            :invalid="r$.$fields.country_birth_id.$error"
                            filter
                            option-label="name"
                            option-value="id"
                            placeholder="Pays de naissance"
                        />
                        <Message
                            v-if="r$.$fields.country_birth_id.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.country_birth_id.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="place_birth">Nationalité *</label>
                        <input-text
                            id="nationality"
                            v-model="r$.$value.nationality"
                            :invalid="r$.$fields.nationality.$error"
                            :disabled="store.processing"
                            minLength="10"
                        />
                        <Message
                            v-if="r$.$fields.nationality.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.nationality.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
                <b-col sm="6" md="4" lg="3">
                    <div class="d-flex flex-column">
                        <label for="passport_number">N° de passport *</label>
                        <input-text
                            id="passport_number"
                            v-model="r$.$value.passport_number"
                            :invalid="r$.$fields.passport_number.$error"
                            :disabled="store.processing"
                            minLength="10"
                        />
                        <Message
                            v-if="r$.$fields.passport_number.$error"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ r$.$fields.passport_number.$errors[0] }}
                        </Message>
                    </div>
                </b-col>
            </div>
        </b-card-body>
    </b-card>
</template>

<style scoped></style>
