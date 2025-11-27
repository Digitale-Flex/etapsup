export type UserType = {
  id: string
  email?: string
  username?: string
  password: string
  firstName?: string
  lastName?: string
  role?: string
  token?: string
}

export type AuthStoreType = {
  user: UserType | undefined
  isAuthenticated: boolean
  saveSession: (session: UserType) => void
  removeSession: () => void
}
