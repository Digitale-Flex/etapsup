import type { BSIconType, FAIconType, IconType, LinkType } from '@/Types/index'
import type { IconDefinition } from '@fortawesome/free-solid-svg-icons'

export type LayoutState = {
  theme: 'light' | 'dark' | 'auto'
  dir: 'ltr' | 'rtl'
}

export type ThemeModeType = {
  theme: LayoutState['theme']
  icon: BSIconType
}

export type BookingMenuItemType = {
  key: string
  label: string
  isTitle?: boolean
  link?: LinkType
  children?: MenuItemType[]
  parentKey?: string
  icon?: IconDefinition
}

export type MenuItemType = {
  key: string
  label: string
  isTitle?: boolean
  link?: LinkType
  children?: MenuItemType[]
  parentKey?: string
  icon?: IconType
}

export type FooterLink = {
  title: string
  items: {
    name: string
    link?: LinkType
    icon?: FAIconType
  }[]
}

export type FooterTopLink = {
  name: string
  link?: LinkType
}
