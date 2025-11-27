<script setup lang="ts">
import {
    EnhancedPaginationLink,
    PaginationLink,
    PaginationMeta,
} from '@/Types/index';
import { faAngleLeft, faAngleRight } from '@fortawesome/free-solid-svg-icons';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    pagination: PaginationMeta;
    filters?: Record<string, any>;
}>();

const showPagination = computed(() => props.pagination.last_page > 1);

const links = computed<EnhancedPaginationLink[]>(() => {
    const { current_page, last_page, links } = props.pagination;
    if (!links?.length) return [];

    const enhanceLink = (link: PaginationLink): EnhancedPaginationLink => {
        if (link.label.includes('Précédent')) {
            return {
                ...link,
                isIcon: true,
                icon: faAngleLeft,
                isPrevious: true,
                label: '',
            };
        }

        if (link.label.includes('Suivant')) {
            return {
                ...link,
                isIcon: true,
                icon: faAngleRight,
                isNext: true,
                label: '',
            };
        }

        return { ...link, isIcon: false };
    };

    const isVisibleLink = (link: EnhancedPaginationLink): boolean => {
        if (link.label === '...') return false;
        if (link.isPrevious && current_page === 1) return false;
        if (link.isNext && current_page === last_page) return false;

        const page = parseInt(link.label);
        if (isNaN(page)) return true;

        return (
            page === 1 ||
            page === last_page ||
            Math.abs(current_page - page) <= 1
        );
    };

    const addEllipsis = (
        filteredLinks: EnhancedPaginationLink[],
    ): EnhancedPaginationLink[] => {
        const result: EnhancedPaginationLink[] = [];
        let previousPage = 0;

        for (const link of filteredLinks) {
            if (!link.isIcon) {
                const currentPage = parseInt(link.label);
                if (
                    !isNaN(currentPage) &&
                    previousPage !== 0 &&
                    currentPage - previousPage > 1
                ) {
                    result.push({
                        url: null,
                        label: '...',
                        active: false,
                        isIcon: false,
                    });
                }
                if (!isNaN(currentPage)) {
                    previousPage = currentPage;
                }
            }
            result.push(link);
        }

        return result;
    };

    return addEllipsis(links.map(enhanceLink).filter(isVisibleLink));
});
</script>

<template>
    <nav
        v-if="showPagination"
        class="d-flex justify-content-center"
        aria-label="Pagination"
    >
        <ul
            class="pagination pagination-primary-soft d-inline-block d-md-flex mb-0 rounded"
        >
            <li
                v-for="(link, index) in links"
                :key="`page-${index}`"
                class="page-item mb-0"
                :class="{ active: link.active }"
            >
                <Link
                    :href="link.url ?? '#'"
                    class="page-link"
                    :class="{ disabled: !link.url }"
                    tabindex="-1"
                    preserve-scroll
                    preserve-state
                >
                    <font-awesome-icon
                        v-if="link.isIcon && link.icon"
                        :icon="link.icon"
                    />
                    <template v-else>
                        {{ link.label }}
                    </template>
                </Link>
            </li>
        </ul>
    </nav>
</template>
