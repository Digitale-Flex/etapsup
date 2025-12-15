<script setup lang="ts">
import { useCustomSearchStore } from '@/Stores/customSearch';
import { Category, City, Layout, PropertyType } from '@/Types/index';
import {
    BIconHouseAdd,
    BIconHouseCheck,
    BIconPinMap,
    BIconTag,
} from 'bootstrap-icons-vue';
import { storeToRefs } from 'pinia';

interface Props {
    types: PropertyType[];
    categories: Category[];
    layouts: Layout[];
    cities?: City[];
}

defineProps<Props>();

const store = useCustomSearchStore();
const { r$ } = storeToRefs(store);
</script>

<template>
    <b-row class="mt-n3 mt-sm-n4 relative mb-3">
        <b-col cols="11" class="mx-auto">
            <div class="bg-mode rounded-3 p-4 shadow">
                <div class="form-control-bg-transparent bg-mode rounded-3">
                    <b-row class="g-4 align-items-center">
                        <b-col xl="12">
                            <b-row class="g-4">
                                <b-col sm="6" lg="3">
                                    <label class="h6 fw-normal mb-0">
                                        <BIconHouseCheck
                                            class="text-primary me-1"
                                        />
                                        Établissement
                                    </label>
                                    <div
                                        class="form-control-transparent form-fs-lg mt-2"
                                    >
                                        <MultiSelect
                                            v-model="
                                                r$.$value.property_type_ids
                                            "
                                            :options="types"
                                            :max-selected-labels="2"
                                            :invalid="
                                                r$.$fields.property_type_ids
                                                    .$invalid
                                            "
                                            display="chip"
                                            option-label="label"
                                            option-value="id"
                                            class="w-full"
                                            placeholder="Type d'établissement"
                                        />
                                        <Message
                                            v-show="
                                                r$.$fields.property_type_ids
                                                    .$invalid
                                            "
                                            severity="error"
                                            size="small"
                                            variant="simple"
                                        >
                                            {{
                                                r$.$fields.property_type_ids
                                                    .$errors[0]
                                            }}
                                        </Message>
                                    </div>
                                </b-col>
                                <b-col sm="6" lg="3">
                                    <label class="h6 fw-normal mb-0">
                                        <BIconTag class="text-primary me-1" />
                                        Domaine d'études
                                    </label>
                                    <div
                                        class="form-control-transparent form-fs-lg mt-2"
                                    >
                                        <Select
                                            v-model="r$.$value.category_id"
                                            :options="categories"
                                            :invalid="
                                                r$.$fields.category_id.$invalid
                                            "
                                            option-label="label"
                                            option-value="id"
                                            showClear
                                            class="w-full"
                                            placeholder="Domaine d'études"
                                        />
                                        <Message
                                            v-show="
                                                r$.$fields.category_id.$invalid
                                            "
                                            severity="error"
                                            size="small"
                                            variant="simple"
                                        >
                                            {{
                                                r$.$fields.category_id
                                                    .$errors[0]
                                            }}
                                        </Message>
                                    </div>
                                </b-col>
                                <b-col sm="6" lg="3">
                                    <label class="h6 fw-normal mb-0">
                                        <BIconHouseAdd
                                            class="text-primary me-1"
                                        />
                                        Niveau d'études
                                    </label>
                                    <div
                                        class="form-control-transparent form-fs-lg mt-2"
                                    >
                                        <MultiSelect
                                            v-model="r$.$value.layout_ids"
                                            :options="layouts"
                                            :invalid="
                                                r$.$fields.layout_ids.$invalid
                                            "
                                            :max-selected-labels="2"
                                            display="chip"
                                            option-label="label"
                                            option-value="id"
                                            class="w-full"
                                            placeholder="Licence, Master, Doctorat..."
                                        />
                                        <Message
                                            v-show="
                                                r$.$fields.layout_ids.$invalid
                                            "
                                            severity="error"
                                            size="small"
                                            variant="simple"
                                        >
                                            {{
                                                r$.$fields.layout_ids.$errors[0]
                                            }}
                                        </Message>
                                    </div>
                                </b-col>
                                <b-col sm="6" lg="3">
                                    <label class="h6 fw-normal mb-0">
                                        <BIconPinMap
                                            class="text-primary me-1"
                                        />
                                        Ville</label
                                    >
                                    <div
                                        class="form-control-transparent form-fs-lg mt-2"
                                    >
                                        <Select
                                            v-model="r$.$value.city_id"
                                            :options="cities"
                                            :invalid="
                                                r$.$fields.city_id.$invalid
                                            "
                                            :virtualScrollerOptions="{ itemSize: 30 }"
                                            filter
                                            showClear
                                            option-label="name"
                                            option-value="id"
                                            class="w-full"
                                            placeholder="Dans quelle ville ?"
                                        />
                                        <Message
                                            v-show="r$.$fields.city_id.$invalid"
                                            severity="error"
                                            size="small"
                                            variant="simple"
                                        >
                                            {{ r$.$fields.city_id.$errors[0] }}
                                        </Message>
                                    </div>
                                </b-col>
                            </b-row>
                        </b-col>
                    </b-row>
                </div>
            </div>
        </b-col>
    </b-row>
</template>

<style scoped>
.relative {
    position: relative;
}
</style>
