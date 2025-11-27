<script setup lang="ts">
import { useResizeObserver } from '@vueuse/core';
import { computed, onMounted, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        text: string;
        maxHeight?: string | number;
        maxLines?: number;
        expanded?: boolean;
        location?: 'start' | 'middle' | 'end';
        ellipsis?: string;
        autoResize?: boolean;
        showTooltip?: boolean;
    }>(),
    {
        expanded: false,
        location: 'end',
        ellipsis: '…',
        autoResize: false,
        showTooltip: false,
    },
);

const emit = defineEmits<{
    (e: 'clamp-change', data: boolean): void;
    (e: 'update:expanded', data: boolean): void;
}>();

const textClampRef = ref<HTMLElement | null>(null);
const contentRef = ref<HTMLElement | null>(null);
const textRef = ref<HTMLElement | null>(null);
const localExpanded = ref(props.expanded);
const offset = ref(props.text.length);
const isClamped = ref(false);

const realMaxHeight = computed(() => {
    if (localExpanded.value) return undefined;
    if (!props.maxHeight) return undefined;
    return typeof props.maxHeight === 'number'
        ? `${props.maxHeight}px`
        : props.maxHeight;
});

const stripHtml = (html: string): string => {
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
};
const realText = computed(() =>
    isClamped.value ? clampedText.value : stripHtml(props.text),
);

const clampedText = computed(() => {
    const cleanText = stripHtml(props.text);

    if (props.location === 'start') {
        return props.ellipsis + (cleanText.slice(-offset.value) || '').trim();
    }
    if (props.location === 'middle') {
        const split = Math.floor(offset.value / 2);
        return `${(cleanText.slice(0, split) || '').trim()}${props.ellipsis}${(cleanText.slice(-split) || '').trim()}`;
    }
    return (cleanText.slice(0, offset.value) || '').trim() + props.ellipsis;
});

const applyChange = () => {
    if (textRef.value) textRef.value.textContent = realText.value;
};

const isOverflow = () => {
    if (!props.maxLines && !props.maxHeight) return false;
    if (!textClampRef.value) return false;
    if (props.maxLines && getLines() > props.maxLines) return true;
    return !!(
        props.maxHeight &&
        textClampRef.value.scrollHeight > textClampRef.value.offsetHeight
    );
};

const getLines = () => {
    if (!contentRef.value) return 0;
    return Object.keys(
        Array.from(contentRef.value.getClientRects()).reduce(
            (prev, { top, bottom }) => {
                prev[`${top}/${bottom}`] = true;
                return prev;
            },
            {} as Record<string, boolean>,
        ),
    ).length;
};

const update = () => {
    if (localExpanded.value) return;
    applyChange();
    if (isOverflow() || isClamped.value) {
        search();
    }
    isClamped.value = offset.value < props.text.length;
};

const search = (from = 0, to = offset.value) => {
    if (to - from <= 3) {
        stepToFit();
        return;
    }
    const target = Math.floor((to + from) / 2);
    clampAt(target);
    if (isOverflow()) {
        search(from, target);
    } else {
        search(target, to);
    }
};

const clampAt = (newOffset: number) => {
    offset.value = newOffset;
    applyChange();
};

const stepToFit = () => {
    fill();
    clamp();
};

const fill = () => {
    while (
        (!isOverflow() || getLines() < 2) &&
        offset.value < props.text.length
    ) {
        moveEdge(1);
    }
};

const clamp = () => {
    while (isOverflow() && getLines() > 1 && offset.value > 0) {
        moveEdge(-1);
    }
};

const moveEdge = (steps: number) => {
    clampAt(offset.value + steps);
};

const expand = () => {
    localExpanded.value = true;
};
const collapse = () => {
    localExpanded.value = false;
};
const toggle = () => {
    localExpanded.value = !localExpanded.value;
};

watch(
    () => isClamped.value,
    (val) => {
        emit('clamp-change', val);
    },
);

watch(
    () => props.expanded,
    (val) => {
        localExpanded.value = val;
    },
);

watch(
    () => localExpanded.value,
    (val) => {
        if (val) {
            clampAt(props.text.length);
        } else {
            update();
        }
        if (props.expanded !== val) {
            emit('update:expanded', val);
        }
    },
);

watch(
    () => [props.maxLines, props.maxHeight, props.ellipsis, props.location],
    update,
);

watch(
    () => props.text,
    () => {
        const cleanText = stripHtml(props.text);
        offset.value = cleanText.length;
        update();
    },
    { immediate: true },
);

onMounted(() => {
    update();
    if (props.autoResize && textClampRef.value) {
        useResizeObserver(textClampRef.value, update);
    }
});
</script>

<template>
    <div
        ref="textClampRef"
        class="text-clamp"
        :style="{
            overflow: 'hidden',
            maxHeight: realMaxHeight,
        }"
    >
        <span ref="contentRef">
            <slot
                name="before"
                :expand="expand"
                :collapse="collapse"
                :toggle="toggle"
                :clamped="isClamped"
                :expanded="localExpanded"
            />
            <span ref="textRef" :aria-label="text">{{ realText }}</span>
            <slot
                name="after"
                :expand="expand"
                :collapse="collapse"
                :toggle="toggle"
                :clamped="isClamped"
                :expanded="localExpanded"
            />
        </span>
    </div>
</template>
