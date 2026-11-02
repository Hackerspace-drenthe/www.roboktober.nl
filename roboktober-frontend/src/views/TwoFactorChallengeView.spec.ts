import { flushPromises, mount } from '@vue/test-utils'
import { createMemoryHistory, createRouter } from 'vue-router'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { completeTwoFactorChallenge, setAuthToken } from '@/api'
import TwoFactorChallengeView from './TwoFactorChallengeView.vue'

vi.mock('@/api', () => ({
  completeTwoFactorChallenge: vi.fn(),
  setAuthToken: vi.fn(),
}))

vi.mock('@/composables/useAuth', () => ({
  useAuth: () => ({ refreshMe: vi.fn() }),
}))

async function setup() {
  const router = createRouter({
    history: createMemoryHistory(),
    routes: [
      { path: '/challenge', component: TwoFactorChallengeView },
      { path: '/login', component: { template: '<div>Login</div>' } },
    ],
  })
  await router.push('/challenge?challenge=test-challenge&redirect=/account')
  await router.isReady()
  return mount(TwoFactorChallengeView, { global: { plugins: [router] } })
}

describe('TwoFactorChallengeView', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('shows the API error and lets the user retry the same challenge', async () => {
    vi.mocked(completeTwoFactorChallenge).mockRejectedValue({
      isAxiosError: true,
      response: { status: 422, data: { message: 'Ongeldige twee-factor code.' } },
    })
    const wrapper = await setup()
    await wrapper.get('input').setValue('123456')
    await wrapper.get('form').trigger('submit')
    await flushPromises()
    expect(wrapper.text()).toContain('Ongeldige twee-factor code.')
    expect(wrapper.get('a').attributes('href')).toBe('/login?redirect=/account')
    expect(wrapper.text()).toContain('datum en tijd op je telefoon op automatisch')

    await wrapper.get('input').setValue('654321')
    await wrapper.get('form').trigger('submit')
    await flushPromises()
    expect(completeTwoFactorChallenge).toHaveBeenLastCalledWith({
      challenge_id: 'test-challenge',
      code: '654321',
      device_name: 'web-app',
    })
    expect(setAuthToken).not.toHaveBeenCalled()
  })

  it('distinguishes an expired challenge from an incorrect code', async () => {
    vi.mocked(completeTwoFactorChallenge).mockRejectedValue({
      isAxiosError: true,
      response: { status: 422, data: { message: 'Twee-factor challenge is verlopen of ongeldig.' } },
    })
    const wrapper = await setup()
    await wrapper.get('form').trigger('submit')
    await flushPromises()
    expect(wrapper.text()).toContain('Twee-factor challenge is verlopen of ongeldig.')
  })

  it.each([
    [{ isAxiosError: true, response: { status: 429 } }, 'Te veel pogingen.'],
    [new Error('Network failed'), 'Controleer je verbinding'],
    [{ isAxiosError: true, response: { status: 500 } }, 'Controleer je verbinding'],
  ])('does not describe transport or rate-limit failures as incorrect codes', async (cause, message) => {
    vi.mocked(completeTwoFactorChallenge).mockRejectedValue(cause)
    const wrapper = await setup()
    await wrapper.get('form').trigger('submit')
    await flushPromises()
    expect(wrapper.text()).toContain(message)
    expect(wrapper.text()).not.toContain('Ongeldige twee-factor code.')
  })
})
