import {
  ADMIN_MENU_ITEMS,
  AGENT_MENU_ITEMS,
  APP_MENU_ITEMS,
  USER_PROFILE_MENU_ITEMS
} from '@assets/data/menu-items'

export const getAppMenuItems = () => {
  // NOTE - You can fetch from server and return here as well
  return APP_MENU_ITEMS
}
export const getUserMenuItems = () => {
  // NOTE - You can fetch from server and return here as well
  return USER_PROFILE_MENU_ITEMS
}

export const getAgentMenuItems = () => {
  // NOTE - You can fetch from server and return here as well
  return AGENT_MENU_ITEMS
}

export const getAdminMenuItems = () => {
  // NOTE - You can fetch from server and return here as well
  return ADMIN_MENU_ITEMS
}
