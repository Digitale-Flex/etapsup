// resources/js/types/sticky-js.d.ts
declare module 'sticky-js' {
  class Sticky {
    constructor(selector: string, options?: any);
    destroy(): void;
    update(): void;
  }
  export default Sticky;
}