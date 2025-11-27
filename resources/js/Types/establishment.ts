import type { MediaImage, Country, City } from '@/Types/index';

export interface StudyField {
    id: string;
    name: string;
    description?: string;
}

export interface EstablishmentType {
    id: string;
    name: string;
    description?: string;
}

export interface Program {
    id: string;
    name: string;
    description?: string;
    duration: string; // "3 ans", "2 ans", etc.
    degree_level: string; // "Licence", "Master", "Doctorat"
    study_field: StudyField;
    tuition_fees?: number;
    language: string; // "Français", "Anglais", etc.
}

export interface Establishment {
    id: string;
    name: string;
    slug: string;
    description: string;
    address: string;
    website?: string;
    email?: string;
    phone?: string;
    founded_year?: number;
    student_count?: number;
    accreditation?: string;
    
    // Relations
    country: Country;
    city: City;
    establishment_type: EstablishmentType;
    study_fields: StudyField[];
    programs: Program[];
    
    // Media
    logo?: string;
    thumb: string;
    images: MediaImage[];
    
    // Ratings et reviews
    ratings?: {
        average: number;
        count: number;
        distribution: string;
    };
    
    // Métadonnées
    is_featured: boolean;
    is_verified: boolean;
    created_at: string;
    updated_at: string;
}

// Re-export geographic types for external use
export type { Country, City } from '@/Types/index';

export interface EstablishmentFilters {
    query?: string;
    country?: string;
    city?: string;
    studyFields?: string[];
    establishmentTypes?: string[];
    levels?: string[];
    isPublic?: boolean;
    isPrivate?: boolean;
    isVerified?: boolean;
    minStudents?: number | null;
    maxStudents?: number | null;
    foundedAfter?: number | null;
}