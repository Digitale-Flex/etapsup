import type { IconDefinition } from '@fortawesome/free-solid-svg-icons';
import type { ComponentOptionsBase, CreateComponentPublicInstance } from 'vue';

export interface RealEstateSetting {
    vat: number;
    tourist_tax: number;
    consumable: number;
    monthly_rental: number;
    service_fees: number;
    application_fees: number;
}
export interface Reservation {
    id: string;
    price: string;
    reason: string;
    address: string;
    guests: string;
    type: string;
    status: { value: string; label: string };
    fees: {
        price: number;
        touristTax: number;
        consumable: number;
        serviceFees: number;
        cleaningFees: number;
        caution: number;
        firstMonthRent: number;
        applicationFees: number;
    };
    comments: Comment[];
    ratings: {
        average: number;
        count: number;
        distribution: string;
        user_rating: string;
    };
    start_date: string;
    end_date: string;
    created_at: string;
    property: Property;
}
export interface Comment {
    id: string;
    content: string;
    score: number;
    author: { name: string; avatar: string; reviews_count: number };
    created_at: string;
    parent_id: string;
}
export interface Equipment {
    id: string;
    label: string;
}
export interface RentalType {
    id: string;
    label: string;
}
export interface Regulation {
    id: string;
    label: string;
}
export interface Layout {
    id: string;
    label: string;
}
export interface MediaImage {
    name: string;
    file_name: string;
    uuid: string;
    preview_url: string;
    original_url: string;
    order: number;
    custom_properties: any[];
    extension: string;
    size: number;
}
export interface Property {
    id: string;
    title: string;
    slug: string;
    description: string;
    email?: string; // Contact email
    phone?: string; // Contact phone
    room: string;
    living_room: string;
    kitchen: string;
    bathroom: string;
    dining_room: string;
    balcony: boolean;
    outdoor_space: boolean;
    address: string;
    price: number;
    cleaning_fees: number;
    airbnb: string;
    discount: boolean;
    regulation: string;
    website?: string; // Site web
    student_count?: number; // Nombre d'étudiants
    ranking?: number; // Classement
    regulations?: any[]; // Règlements
    accreditations?: any[]; // Accréditations
    propertyType: PropertyType;
    equipments: Equipment[];
    layouts: Layout[];
    comments: Comment[];
    ratings: {
        average: number;
        count: number;
        distribution: string;
        user_rating: string;
    };
    city: City;
    category: Category;
    subCategory: SubCategory;
    thumb: string;
    images: MediaImage[];
}

export interface PropertyType {
    id: string;
    label: string;
}
export interface Category {
    id: string;
    label: string;
}
export interface SubCategory {
    id: string;
    label: string;
}
export interface Regulation {
    id: string;
    label: string;
}

export interface CertificateRequest {
    id: string;
    reference: string;
    name: string;
    nationality: string;
    budget: number;
    rentalStart: string;
    duration: string;
    furtherInformation: string;
    address: string;
    state: { label: string; color: string; value: string };
    country: Country;
    city: City;
    genre: Genre;
    rentalDeposit: RentalDeposit;
    rentalDeposits: RentalDeposit[];
    partner: Partner;
    createdAt: string;
    file: string;
    coupon: { id: string; code: string; amount: number };
    paid: number
}
export interface Country {
    id: string;
    name: string;
}
export interface Genre {
    id: string;
    name: string;
}
export interface RentalDeposit {
    id: string;
    name: string;
}
export interface Region {
    id: string;
    name: string;
    cities: City[];
}
export interface City {
    id: string;
    name: string;
    budget: number;
    country_id?: string;
    country?: Country; // Pays
    region: Region; // Région
}
export interface Partner {
    id: string;
    name: string;
}

export interface DegreeLevel {
    id: string;
    label: string;
    description?: string;
}

export interface User {
    id: string;
    surname: string;
    name: string;
    email: string;
    email_verified_at?: string;
    full_name: string;
    phone: string;
    place_birth: string;
    date_birth: string;
    nationality: string;
    passport_number: string;
    country: Country;
    photo?: string;
    photo_url?: string; // Alias pour photo
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    links: PaginationLink[];
    path: string;
    per_page: number;
    to: number;
    total: number;
}

export interface EnhancedPaginationLink extends PaginationLink {
    isIcon?: boolean;
    icon?: IconDefinition;
    isPrevious?: boolean;
    isNext?: boolean;
}

export type LinkType = {
    name: string;
    params?: any;
};

export type FAIconType = IconDefinition;

export type BSIconType = ComponentOptionsBase<
    {},
    any,
    any,
    any,
    any,
    any,
    any,
    any,
    string,
    any
> &
    ThisType<
        CreateComponentPublicInstance<
            {},
            any,
            any,
            any,
            any,
            any,
            any,
            any,
            Readonly<{}>
        >
    >;

export type IconType =
    | IconDefinition
    | (ComponentOptionsBase<
          {},
          any,
          any,
          any,
          any,
          any,
          any,
          any,
          string,
          any
      > &
          ThisType<
              CreateComponentPublicInstance<
                  {},
                  any,
                  any,
                  any,
                  any,
                  any,
                  any,
                  any,
                  Readonly<{}>
              >
          >);

export type GuestAndRoomFormType = {
    guests: {
        adults: number;
        children: number;
        rooms: number;
    };
};
