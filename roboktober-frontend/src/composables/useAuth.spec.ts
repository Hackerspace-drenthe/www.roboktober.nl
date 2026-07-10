import { useAuth } from '@/composables/useAuth'
import { getCurrentUser, setAuthToken } from '@/api'
import { vi } from 'vitest'

vi.mock('@/api', () => ({
  getCurrentUser: vi.fn(),
  loginUser: vi.fn(),
  logoutUser: vi.fn(),
  registerUser: vi.fn(),
  setAuthToken: vi.fn(),
}))

describe('useAuth', () => {
  beforeEach(() => {
    const auth = useAuth()
    auth.user.value = null
    auth.initialized.value = false
    auth.loading.value = false
  })

  it('marks initialized when no token exists', async () => {
    const auth = useAuth()

    await auth.initAuth()

    expect(auth.initialized.value).toBe(true)
    expect(getCurrentUser).not.toHaveBeenCalled()
  })

  it('clears token on failed refresh', async () => {
    localStorage.setItem('auth_token', 'broken-token')
    vi.mocked(getCurrentUser).mockRejectedValueOnce(new Error('invalid token'))

    const auth = useAuth()
    await auth.initAuth()

    expect(setAuthToken).toHaveBeenCalledWith(null)
    expect(auth.user.value).toBeNull()
  })

  it('evaluates hierarchical role checks', () => {
    const auth = useAuth()

    auth.user.value = {
      id: 1,
      name: 'Moderator',
      email: 'mod@example.test',
      role: 'moderator',
      media: [],
    }

    expect(auth.hasRole('visitor')).toBe(true)
    expect(auth.hasRole('teamcaptain')).toBe(true)
    expect(auth.hasRole('moderator')).toBe(true)
    expect(auth.hasRole('admin')).toBe(false)
  })
})
