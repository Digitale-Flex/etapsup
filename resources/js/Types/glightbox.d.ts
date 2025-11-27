// resources/js/types/glightbox.d.ts
declare module 'glightbox' {
  interface GLightboxOptions {
    openEffect?: string;
    closeEffect?: string;
    moreText?: string;
    moreLength?: number;
    closeButton?: boolean;
    touchNavigation?: boolean;
    keyboardNavigation?: boolean;
    closeOnOutsideClick?: boolean;
    startAt?: number;
    width?: string | number;
    height?: string | number;
    videosWidth?: string | number;
    beforeSlideChange?: (prev: number, current: number) => void;
    afterSlideChange?: (prev: number, current: number) => void;
    beforeSlideLoad?: (slide: any, data: any) => void;
    afterSlideLoad?: (slide: any, data: any) => void;
    slideExtraAttributes?: any;
    onOpen?: () => void;
    onClose?: () => void;
    loop?: boolean;
    zoomable?: boolean;
    draggable?: boolean;
    dragAutoSnap?: boolean;
    dragToleranceX?: number;
    dragToleranceY?: number;
    preload?: boolean;
    oneSlidePerOpen?: boolean;
    touchFollowAxis?: boolean;
    skin?: string;
    cssEfects?: {
      fade?: { in: string; out: string };
      zoom?: { in: string; out: string };
      slide?: { in: string; out: string };
      slideBack?: { in: string; out: string };
      none?: { in: string; out: string };
    };
  }

  class GLightbox {
    constructor(options?: GLightboxOptions);
    open(index?: number): void;
    close(): void;
    reload(): void;
    destroy(): void;
    setElements(elements: any): void;
    openAt(index: number): void;
    insertSlide(config: any, index?: number): void;
    removeSlide(index: number): void;
    getActiveSlide(): any;
    getActiveSlideIndex(): number;
    prevSlide(): void;
    nextSlide(): void;
    goToSlide(index: number): void;
    setSlideContent(slide: any, data: any, callback?: () => void): void;
    getSlideContent(slide: any, data: any, callback?: () => void): void;
  }

  export default GLightbox;
}