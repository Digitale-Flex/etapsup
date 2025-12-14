<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';

interface Props {
    applicationId: string;
    existingDocuments?: UploadedDocument[];
}

interface UploadedDocument {
    id: number;
    document_type: string;
    file_name: string;
    file_size: number;
    created_at: string;
}

const props = defineProps<Props>();
const emit = defineEmits(['documents-updated']);

const uploading = ref(false);
const uploadProgress = ref(0);
const documents = ref<UploadedDocument[]>(props.existingDocuments || []);
const errorMessage = ref<string | null>(null);

const documentTypes = [
    { value: 'CV', label: 'Curriculum Vitae (CV)', required: true },
    { value: 'Diplôme', label: 'Dernier diplôme obtenu', required: true },
    { value: 'Relevé notes', label: 'Relevé de notes', required: true },
    { value: 'Lettre motivation', label: 'Lettre de motivation', required: false },
    { value: 'Passeport', label: 'Copie du passeport', required: false },
    { value: 'Photo', label: 'Photo d\'identité', required: false },
];

const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 Mo
const ALLOWED_TYPES = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

const requiredDocuments = computed(() => documentTypes.filter(d => d.required));
const optionalDocuments = computed(() => documentTypes.filter(d => !d.required));

const missingRequiredDocs = computed(() => {
    return requiredDocuments.value.filter(reqDoc => {
        return !documents.value.some(doc => doc.document_type === reqDoc.value);
    });
});

const canProceed = computed(() => missingRequiredDocs.value.length === 0);

const selectFile = async (documentType: string) => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.pdf,.jpg,.jpeg,.png';

    input.onchange = async (e: any) => {
        const file = e.target.files[0];
        if (!file) return;

        // Debug: afficher infos fichier
        console.log('📁 Fichier sélectionné:', {
            name: file.name,
            type: file.type,
            size: file.size,
            sizeKo: Math.round(file.size / 1024) + ' Ko',
            maxAllowed: MAX_FILE_SIZE,
            typeAllowed: ALLOWED_TYPES.includes(file.type)
        });

        // Validation client-side
        if (!ALLOWED_TYPES.includes(file.type)) {
            errorMessage.value = `Format non autorisé (${file.type}). Utilisez PDF, JPG ou PNG.`;
            return;
        }

        if (file.size > MAX_FILE_SIZE) {
            errorMessage.value = `Fichier trop volumineux (${Math.round(file.size / 1024)} Ko). Maximum 5 Mo.`;
            return;
        }

        await uploadFile(file, documentType);
    };

    input.click();
};

const uploadFile = async (file: File, documentType: string) => {
    uploading.value = true;
    uploadProgress.value = 0;
    errorMessage.value = null;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('document_type', documentType);
    formData.append('application_id', props.applicationId);

    try {
        // Utiliser route web (session auth) au lieu de route API (Sanctum)
        const response = await axios.post('/applications/documents', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: (progressEvent) => {
                if (progressEvent.total) {
                    uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                }
            },
        });

        documents.value.push(response.data.document);
        emit('documents-updated', documents.value);

    } catch (error: any) {
        errorMessage.value = error.response?.data?.message || 'Erreur lors de l\'upload';
    } finally {
        uploading.value = false;
        uploadProgress.value = 0;
    }
};

const removeDocument = async (documentId: number) => {
    if (!confirm('Voulez-vous vraiment supprimer ce document ?')) return;

    try {
        // Utiliser route web (session auth) au lieu de route API (Sanctum)
        await axios.delete(`/applications/documents/${documentId}`);
        documents.value = documents.value.filter(d => d.id !== documentId);
        emit('documents-updated', documents.value);
    } catch (error: any) {
        errorMessage.value = error.response?.data?.message || 'Erreur lors de la suppression';
    }
};

const formatFileSize = (bytes: number): string => {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
};

defineExpose({ canProceed, missingRequiredDocs });
</script>

<template>
    <div class="document-uploader">
        <BAlert v-if="errorMessage" variant="danger" :model-value="true" dismissible @dismissed="errorMessage = null">
            {{ errorMessage }}
        </BAlert>

        <!-- Documents obligatoires -->
        <div class="document-section mb-4">
            <h6 class="section-subtitle">
                <i class="bi bi-file-earmark-check me-2"></i>
                Documents obligatoires
            </h6>

            <div class="documents-list">
                <div
                    v-for="docType in requiredDocuments"
                    :key="docType.value"
                    class="document-item"
                >
                    <div class="doc-info">
                        <i class="bi bi-file-earmark-pdf text-primary me-2"></i>
                        <span class="doc-label">{{ docType.label }}</span>
                        <span class="required-badge">*</span>
                    </div>

                    <template v-if="documents.find(d => d.document_type === docType.value)">
                        <div class="doc-uploaded">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <span class="text-muted small">
                                {{ documents.find(d => d.document_type === docType.value)?.file_name }}
                            </span>
                            <BButton
                                type="button"
                                variant="outline-danger"
                                size="sm"
                                @click="removeDocument(documents.find(d => d.document_type === docType.value)!.id)"
                            >
                                <i class="bi bi-trash"></i>
                            </BButton>
                        </div>
                    </template>

                    <template v-else>
                        <BButton
                            type="button"
                            variant="outline-primary"
                            size="sm"
                            @click="selectFile(docType.value)"
                            :disabled="uploading"
                        >
                            <i class="bi bi-upload me-1"></i>
                            Téléverser
                        </BButton>
                    </template>
                </div>
            </div>
        </div>

        <!-- Documents optionnels -->
        <div class="document-section">
            <h6 class="section-subtitle">
                <i class="bi bi-file-earmark me-2"></i>
                Documents optionnels
            </h6>

            <div class="documents-list">
                <div
                    v-for="docType in optionalDocuments"
                    :key="docType.value"
                    class="document-item"
                >
                    <div class="doc-info">
                        <i class="bi bi-file-earmark text-secondary me-2"></i>
                        <span class="doc-label">{{ docType.label }}</span>
                    </div>

                    <template v-if="documents.find(d => d.document_type === docType.value)">
                        <div class="doc-uploaded">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <span class="text-muted small">
                                {{ documents.find(d => d.document_type === docType.value)?.file_name }}
                            </span>
                            <BButton
                                type="button"
                                variant="outline-danger"
                                size="sm"
                                @click="removeDocument(documents.find(d => d.document_type === docType.value)!.id)"
                            >
                                <i class="bi bi-trash"></i>
                            </BButton>
                        </div>
                    </template>

                    <template v-else>
                        <BButton
                            type="button"
                            variant="outline-secondary"
                            size="sm"
                            @click="selectFile(docType.value)"
                            :disabled="uploading"
                        >
                            <i class="bi bi-upload me-1"></i>
                            Téléverser
                        </BButton>
                    </template>
                </div>
            </div>
        </div>

        <!-- Progress bar -->
        <div v-if="uploading" class="mt-3">
            <BProgress :value="uploadProgress" :max="100" animated striped>
                {{ uploadProgress }}%
            </BProgress>
        </div>

        <!-- Avertissement si documents manquants -->
        <BAlert v-if="missingRequiredDocs.length > 0" variant="warning" :model-value="true" class="mt-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Documents manquants :</strong>
            <ul class="mb-0 mt-2">
                <li v-for="doc in missingRequiredDocs" :key="doc.value">
                    {{ doc.label }}
                </li>
            </ul>
        </BAlert>

        <BAlert v-else variant="success" :model-value="true" class="mt-3">
            <i class="bi bi-check-circle me-2"></i>
            Tous les documents obligatoires sont téléversés. Vous pouvez continuer.
        </BAlert>
    </div>
</template>

<style scoped>
.document-uploader {
    padding: 1rem;
}

.section-subtitle {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 1rem;
}

.documents-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.document-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.document-item:hover {
    background: #f1f5f9;
}

.doc-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.doc-label {
    font-weight: 500;
    color: #1a202c;
}

.required-badge {
    color: #ed2939;
    font-weight: bold;
    margin-left: 0.25rem;
}

.doc-uploaded {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>
