// resources/js/types/inertia.d.ts
import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { User } from '@/Types/index';

declare module '@inertiajs/core' {
  interface PageProps extends InertiaPageProps {
    auth?: {
      user: User;
    };
    ziggy?: any;
    flash?: {
      message?: string;
      error?: string;
      success?: string;
      warning?: string;
      info?: string;
    };
    errors?: Record<string, string>;
    // Add other global props here if needed
    establishment?: any;
    establishments?: any;
    [key: string]: any;
  }
}