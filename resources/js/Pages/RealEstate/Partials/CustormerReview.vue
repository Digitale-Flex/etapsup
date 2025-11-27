<script setup lang="ts">
import { Property } from '@/Types/index';
import { faStar, faStarHalfAlt } from '@fortawesome/free-solid-svg-icons';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { BIconArrowRight } from 'bootstrap-icons-vue';
import dayjs from 'dayjs';
import 'dayjs/locale/fr';
import localizedFormat from 'dayjs/plugin/localizedFormat';
import { computed, ref } from 'vue';

dayjs.extend(localizedFormat);
dayjs.locale('fr');

interface Props {
    property: Property;
    canReview: boolean;
    pagination: {
        current_page: number;
        per_page: number;
        total: number;
        has_more: boolean;
    };
}

const props = defineProps<Props>();
const comments = ref(props.property.comments || []);
const currentPage = ref(props.pagination.current_page);
const loading = ref(false);
const hasMore = ref(props.pagination.has_more);

const loadMore = async () => {
    if (loading.value || !hasMore.value) return;

    loading.value = true;
    try {
        const nextPage = currentPage.value + 1;
        const response = await axios.get(
            route('properties.comments', props.property.slug),
            {
                params: { page: nextPage },
            },
        );
console.log(response.data.comments);
        if (response.data.comments) {
            comments.value = [...comments.value, ...response.data.comments];
            hasMore.value = response.data.has_more;
            currentPage.value = nextPage;
        }
    } catch (error) {
        console.error('Erreur lors du chargement des commentaires:', error);
    } finally {
        loading.value = false;
    }
};

const form = useForm({
    rating: 0,
    comment: '',
});

const ratingPercentages = computed(() => {
    if (!props.property.ratings) return [];

    return [5, 4, 3, 2, 1].map((stars) => ({
        stars,
        value: props.property.ratings.distribution,
        percentage: 0, // Mock percentage since distribution is a string
    }));
});

// Pour afficher les étoiles dynamiquement basées sur la note moyenne
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

const formatDate = (date: string) => {
    return dayjs(date).format('DD MMM YYYY');
};

const submit = () => {
    form.post(route('property.review', props.property.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <b-card no-body class="bg-transparent">
        <b-card-header class="border-bottom bg-transparent px-0 pt-0">
            <b-card-title tag="h3" class="mb-0">Avis des clients</b-card-title>
        </b-card-header>
        <b-card-body class="p-0 pt-4">
            <b-card no-body class="bg-light mb-4 p-4">
                <b-row class="g-4 align-items-center">
                    <b-col md="4">
                        <div class="text-center">
                            <h2 class="mb-0">
                                {{ property.ratings?.average.toFixed(1) }}
                            </h2>
                            <p class="mb-2">
                                Basé sur {{ property.ratings?.count }} avis
                            </p>
                            <ul class="list-inline mb-0">
                                <template
                                    v-for="n in displayStars.full"
                                    :key="`full-${n}`"
                                >
                                    <li class="list-inline-item me-0">
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
                                    :key="`empty-${n}`"
                                >
                                    <li class="list-inline-item me-0">
                                        <font-awesome-icon
                                            :icon="faStar"
                                            class="me-1 text-gray-300"
                                        />
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </b-col>

                    <b-col md="8">
                        <b-card-body class="p-0">
                            <b-row class="gx-3 g-2 align-items-center">
                                <template
                                    v-for="rating in ratingPercentages"
                                    :key="rating.stars"
                                >
                                    <div class="flex items-center gap-2">
                                        <span class="w-12 text-sm"
                                            >{{ rating.stars }} étoiles</span
                                        >
                                        <b-col>
                                            <div
                                                class="progress progress-sm bg-warning bg-opacity-15"
                                            >
                                                <div
                                                    class="progress-bar bg-warning"
                                                    role="progressbar"
                                                    :style="{
                                                        width:
                                                            rating.percentage +
                                                            '%',
                                                    }"
                                                    :aria-valuenow="
                                                        rating.percentage
                                                    "
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"
                                                ></div>
                                            </div>
                                        </b-col>
                                        <span class="w-16 text-end text-sm">
                                            {{ Math.round(rating.percentage) }}%
                                        </span>
                                    </div>
                                </template>
                            </b-row>
                        </b-card-body>
                    </b-col>
                </b-row>
            </b-card>

            <div
                v-if="(form.errors as any)?.general"
                class="bg-danger rounded-2 mb-3 bg-opacity-10 p-3"
            >
                <p class="text-danger mb-0">
                    {{ (form.errors as any)?.general }}
                </p>
            </div>

            <form v-if="canReview" class="mb-5" @submit.prevent="submit">
                <div class="form-control-bg-light mb-3">
                    <Rating
                        v-model="form.rating"
                        :invalid="Boolean(form.errors.rating)"
                        required
                    />
                    <Message
                        v-if="form.errors.rating"
                        severity="error"
                        size="small"
                        variant="simple"
                    >
                        {{ form.errors.rating }}
                    </Message>
                </div>
                <div class="form-control-bg-light mb-3">
                    <Textarea
                        v-model="form.comment"
                        class="form-control-bg-light bg-light w-full"
                        placeholder="Votre avis"
                        rows="3"
                        :invalid="Boolean(form.errors.comment)"
                        required
                        minlength="3"
                    />
                    <Message
                        v-if="form.errors.comment"
                        severity="error"
                        size="small"
                        variant="simple"
                    >
                        {{ form.errors.comment }}
                    </Message>
                </div>

                <b-button
                    variant="primary"
                    type="submit"
                    class="btn-lg"
                    :loading="form.processing"
                >
                    Poster l'avis
                    <BIconArrowRight class="fa-fw ms-2" />
                </b-button>
            </form>

            <template v-for="(comment, i) in comments" :key="i">
                <div class="d-md-flex my-4">
                    <div class="avatar avatar-lg me-3 flex-shrink-0">
                        <img
                            class="avatar-img rounded-circle"
                            :src="comment.author.avatar"
                            alt="avatar"
                        />
                    </div>
                    <div class="w-full">
                        <div
                            class="d-flex justify-content-between mt-md-0 mt-1"
                        >
                            <div>
                                <h6 class="mb-0 me-3">
                                    {{ comment.author.name }}
                                </h6>
                                <ul class="nav nav-divider small mb-2">
                                    <li class="nav-item">
                                        Publié le
                                        {{ formatDate(comment.created_at) }}
                                    </li>
                                </ul>
                            </div>
                            <div class="icon-md text-bg-warning fs-6 rounded">
                                {{ comment.score }}
                            </div>
                        </div>

                        <div class="mb-2">{{ comment.content }}</div>
                    </div>
                </div>
                <hr />
            </template>

            <div v-if="hasMore" class="text-center">
                <b-button
                    @click="loadMore"
                    :disabled="loading"
                    :loading="loading"
                    variant="primary"
                >
                    Charger plus
                </b-button>
            </div>
        </b-card-body>
    </b-card>
</template>

<style scoped></style>
