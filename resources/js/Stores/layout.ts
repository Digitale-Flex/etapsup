import { defineStore } from 'pinia'
import { useLocalStorage } from '@vueuse/core'

import type { LayoutState } from '@/Types/layout'
import { changeHTMLAttribute } from '@/Helpers/layout'

export const useLayoutStore = defineStore('layout_store', () => {
  const theme = useLocalStorage<LayoutState['theme']>('BOOKING_VUE_THEME', 'light')
  const dir = useLocalStorage<LayoutState['dir']>('BOOKING_VUE_DIR', 'ltr')

  if (theme.value === 'auto') {
    const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches
      ? 'dark'
      : 'light'
    changeHTMLAttribute('data-bs-theme', preferredTheme)
  }

  const setTheme = (nTheme: LayoutState['theme']) => {
    if (nTheme === 'auto') {
      const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark'
        : 'light'
      theme.value = 'auto'
      changeHTMLAttribute('data-bs-theme', preferredTheme)
      return
    }
    theme.value = nTheme
    changeHTMLAttribute('data-bs-theme', nTheme)
  }

  const setDir = (nDir: LayoutState['dir']) => {
    dir.value = nDir
    changeHTMLAttribute('data-bs-dir', nDir)
  }

  return {
    theme,
    dir,
    setTheme,
    setDir
  }
})
