// resources/js/types/menu-items.d.ts
declare module '@/assets/data/menu-items' {
  export const bookingHomeMenuItems: Array<{
    key: string;
    label: string;
    icon: any;
    link?: {
      name?: string;
      params?: any;
    };
  }>;
}