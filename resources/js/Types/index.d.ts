import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    surname?: string;
    email: string;
    email_verified_at?: string;
    phone?: string;
    photo_url?: string;
    date_birth?: string;
    place_birth?: string;
    nationality?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};
