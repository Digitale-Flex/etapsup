<script setup lang="ts">
import { formatCurrency } from '@/utils';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface State {
    label: string;
    color: string;
    value: string;
}
interface StateAction {
    label: string;
    type: 'modal' | 'download';
}
interface FormData {
    file: string;
    note: string;
}
interface StateInfo {
    title: string;
    description: string;
    action?: StateAction;
}

const props = defineProps<{
    id: string;
    state: State;
    file: string;
    paid: number;
}>();

const emit = defineEmits<{
    'download-certificate': [];
}>();

const isModalOpen = ref(false);

const stateInfo = computed<StateInfo | null>(() => {
    const states: Record<string, StateInfo> = {
        payment_pending: {
            title: `Paiement requis : ${props.paid ? formatCurrency(props.paid) : '399€'}`,
            description:
                "Votre dossier ne sera traité qu'après réception du paiement. Veuillez effectuer votre paiement pour continuer.",
            action: {
                label: 'Joindre un justificatif',
                type: 'modal',
            },
        },
        payment_verification: {
            title: 'Paiement en vérification',
            description:
                'Le statut de votre dossier changera apres verification du paiement.',
        },
        payment_invalide: {
            title: 'Paiement non reçu',
            description:
                'Il y a eu un problème avec votre paiement. Veuillez réessayer.',
            action: {
                label: 'Joindre un nouveau justificatif',
                type: 'modal',
            },
        },
        certificate_generated: {
            title: 'Attestation de logement',
            description: 'Votre attestation de logement est disponible.',
            action: {
                label: 'Télécharger mon attestation',
                type: 'download',
            },
        },
        payment_validated: {
            title: 'Paiement reçu',
            description: 'Votre dossier est en cours de traitement.',
        },
    };

    return states[props.state.value] || null;
});

// UI-Fix-2.2: Remplacer success (vert) par primary (bleu EtapSup)
const alertClass = computed(() => {
    const colorMap: Record<string, string> = {
        'text-warning': 'warning',
        'text-info': 'info',
        'text-danger': 'danger',
        'text-success': 'primary', // Remplace vert par bleu EtapSup
    };
    return `alert-${colorMap[props.state.color] || 'info'}`;
});

const handleAction = () => {
    const action = stateInfo.value?.action;
    if (!action) return;

    if (action.type === 'modal') {
        isModalOpen.value = true;
    } else if (action.type === 'download') {
        window.open(props.file, '_blank');
    }
};

const pondRef = ref(null);
const isFileLoading = ref<boolean>(false);
const form = useForm<FormData>({
    note: '',
    file: '',
});
const resetClose = () => {
    form.reset();
    form.clearErrors();
    isModalOpen.value = false;
};

const handleStartLoad = () => {
    isFileLoading.value = true;
    form.file = '';
};
const handleProcessFile = (error: any, file: any) => {
    if (!error) {
        form.file = (pondRef.value as any)?.getFile()?.getFileEncodeBase64String();
    }
    isFileLoading.value = false;
};
const handleRemoveFile = () => {
    form.file = '';
};

const handleSubmit = () => {
    form.post(route('dashboard.later.upload-proofs', props.id), {
        onSuccess: () => {
            resetClose();
        },
    });
};

const handleCancel = () => {
    isModalOpen.value = false;
};
</script>

<template>
    <div>
        <div
            v-if="stateInfo"
            class="alert mb-4"
            role="alert"
            :class="alertClass"
        >
            <div
                class="alert-heading d-flex justify-content-between align-items-center"
            >
                <h6 class="mb-0">{{ stateInfo.title }}</h6>
                <b-button
                    v-if="stateInfo.action"
                    variant="primary"
                    size="sm"
                    class="ms-2"
                    @click="handleAction"
                >
                    {{ stateInfo.action.label }}
                </b-button>
            </div>
            <p class="mb-0 mt-2">{{ stateInfo.description }}</p>
        </div>

        <b-modal
            v-model="isModalOpen"
            title="Justificatif de paiement"
            centered
            @hidden="handleCancel"
        >
            <b-form>
                <div class="d-flex flex-column">
                    <file-pond
                        ref="pondRef"
                        max-total-file-size="10MB"
                        label-max-total-file-size-exceeded="Taille totale maximale dépassée"
                        label-max-total-file-size="La taille totale maximale du fichier est de {filesize}"
                        allow-file-encode="true"
                        accepted-file-types="application/pdf,image/*"
                        label-idle="Glissez & déposez votre fichier ou <span class='filepond--label-action'>Parcourir</span>"
                        credits=""
                        @addfilestart="handleStartLoad"
                        @addfile="handleProcessFile"
                        @removefile="handleRemoveFile"
                    />
                    <Message
                        v-if="form.errors.file"
                        severity="error"
                        size="small"
                        variant="simple"
                    >
                        {{ form.errors.file }}
                    </Message>
                </div>

                <div class="d-flex flex-column mb-4 mt-3">
                    <label for="note">Note complémentaire *</label>
                    <Textarea
                        id="note"
                        v-model="form.note"
                        :invalid="Boolean(form.errors.note)"
                        :disabled="form.processing"
                        name="note"
                        rows="3"
                    />
                    <Message
                        v-if="form.errors.note"
                        severity="error"
                        size="small"
                        variant="simple"
                    >
                        {{ form.errors.note }}
                    </Message>
                </div>
            </b-form>

            <template #footer>
                <div class="w-100 d-flex justify-content-end gap-5">
                    <b-button
                        variant="outline-secondary"
                        :disabled="form.processing"
                        @click="handleCancel"
                    >
                        Annuler
                    </b-button>
                    <b-button
                        variant="primary"
                        :loading="form.processing || isFileLoading"
                        :disabled="
                            form.processing || !form.isDirty || isFileLoading
                        "
                        @click="handleSubmit"
                    >
                        Envoyer
                    </b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
