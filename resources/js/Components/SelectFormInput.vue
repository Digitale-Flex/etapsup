<template>
    <label class="form-label" :class="labelClass" v-if="label">{{
        label
    }}</label>
    <b-form-select
        :id="id"
        :options="options"
        class="js-choice"
        :class="customClass"
        :multiple="multiple"
        value-field="id"
        text-field="name"
        @change="updateValue"
        v-bind="$attrs"
    />
</template>

<script setup lang="ts">
import Choices from 'choices.js';
import {onMounted, onUnmounted, ref, watch} from 'vue';

interface Option {
    id: string | number;
    name: string;
}

interface SelectFormInput {
    label?: string;
    type?: string;
    placeholder?: string;
    customClass?: string;
    labelClass?: string;
    id: string;
    multiple?: boolean;
    modelValue?: string;
    options?: Option[];
    choiceOptions?: object;
    valueField?: string;
    textField?: string;
}

const props = withDefaults(defineProps<SelectFormInput>(), {
    valueField: 'id',
    textField: 'name',
    multiple: false,
    options: () => []
});

const emit = defineEmits(['update:modelValue']);
const choicesInstance = ref<Choices | null>(null);

const initChoices = () => {
    const ele = document.getElementById(props.id);
    if (ele) {
        choicesInstance.value = new Choices(ele, {
            ...props.choiceOptions,
            placeholder: true,
            placeholderValue: props.placeholder,
            allowHTML: true,
            shouldSort: false,
            silent: true,
        });

        // Écouter l'événement change de Choices.js
        ele.addEventListener('change', function(event: Event) {
            const select = event.target as HTMLSelectElement;
            const value = select.value;
            emit('update:modelValue', value);
        });

        // Écouter aussi l'événement Choices spécifique
        choicesInstance.value.passedElement.element.addEventListener('change', function(event: Event) {
            const select = event.target as HTMLSelectElement;
            const value = select.value;
            emit('update:modelValue', value);
        });
    }
};

const updateValue = (event: Event) => {
    const value = (event.target as HTMLSelectElement).value;
    emit('update:modelValue', value);
};

// Mettre à jour les options quand elles changent
watch(() => props.options, (newOptions) => {
    if (choicesInstance.value) {
        choicesInstance.value.clearChoices();
        choicesInstance.value.setChoices(newOptions.map(option => ({
            value: option.id,
            label: option.name
        })));
    }
}, { deep: true });

// Mettre à jour la valeur quand modelValue change
watch(() => props.modelValue, (newValue) => {
    if (choicesInstance.value && newValue !== undefined) {
        choicesInstance.value.setChoiceByValue(newValue);
    }
});

// Nettoyer l'instance lors de la destruction du composant
onUnmounted(() => {
    if (choicesInstance.value) {
        choicesInstance.value.destroy();
        choicesInstance.value = null;
    }
});

onMounted(() => {
    initChoices();
});
</script>
