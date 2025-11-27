// resources/js/types/vue-icon.d.ts
declare module '@jamescoyle/vue-icon' {
  import { DefineComponent } from 'vue';
  
  interface SvgIconProps {
    type: string;
    path: string;
    size?: number | string;
    viewbox?: string;
    flip?: string;
    rotate?: number;
  }
  
  const SvgIcon: DefineComponent<SvgIconProps>;
  export default SvgIcon;
}