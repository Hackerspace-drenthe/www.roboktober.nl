import { computed, ref } from 'vue'
import {
  claimTeam,
  getCurrentUser,
  loginUser,
  logoutUser,
  registerUser,
  setAuthToken,
} from '@/api'
import type { AuthUser, LoginPayload, RegisterPayload, UserRole } from '@/types/api'

const user = ref<AuthUser | null>(null)
const loading = ref(false)
const initialized = ref(false)

const rolePriority: Record<UserRole, number> = {
  visitor: 10,
  teamcaptain: 20,
  moderator: 30,
  admin: 40,
}

export function useAuth() {
  const isAuthenticated = computed(() => user.value !== null)

  async function initAuth(): Promise<void> {
    if (initialized.value) return

    const token = localStorage.getItem('auth_token')
    if (!token) {
      initialized.value = true
      return
    }

    try {
      user.value = await getCurrentUser()
    } catch {
      setAuthToken(null)
      user.value = null
    } finally {
      initialized.value = true
    }
  }

  async function register(payload: RegisterPayload): Promise<AuthUser> {
    loading.value = true

    try {
      const response = await registerUser(payload)
      setAuthToken(response.token)
      user.value = response.data
      return response.data
    } finally {
      loading.value = false
      initialized.value = true
    }
  }

  async function login(payload: LoginPayload): Promise<AuthUser> {
    loading.value = true

    try {
      const response = await loginUser(payload)
      setAuthToken(response.token)
      user.value = response.data
      return response.data
    } finally {
      loading.value = false
      initialized.value = true
    }
  }

  async function logout(): Promise<void> {
    try {
      await logoutUser()
    } catch {
      // Best effort: local token is still cleared below.
    }

    setAuthToken(null)
    user.value = null
    initialized.value = true
  }

  async function refreshMe(): Promise<AuthUser | null> {
    if (!localStorage.getItem('auth_token')) {
      user.value = null
      initialized.value = true
      return null
    }

    try {
      user.value = await getCurrentUser()
      return user.value
    } catch {
      setAuthToken(null)
      user.value = null
      return null
    } finally {
      initialized.value = true
    }
  }

  function hasRole(requiredRole: UserRole): boolean {
    if (!user.value) return false
    return rolePriority[user.value.role] >= rolePriority[requiredRole]
  }

  async function claimTeamByToken(editToken: string): Promise<void> {
    await claimTeam(editToken)
    await refreshMe()
  }

  return {
    user,
    loading,
    initialized,
    isAuthenticated,
    initAuth,
    register,
    login,
    logout,
    refreshMe,
    hasRole,
    claimTeamByToken,
  }
}
