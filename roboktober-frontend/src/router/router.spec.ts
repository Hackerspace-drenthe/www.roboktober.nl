import router from '@/router'

describe('router contracts', () => {
  it('contains a public gallery route before the dynamic page route', () => {
    const route = router.resolve('/galerij')
    expect(route.name).toBe('galerij')
    expect(route.meta.requiresAuth).not.toBe(true)
    expect(route.meta.title).toBe("Foto's en video's — Roboktober")
  })

  it('contains public programma route', () => {
    const route = router.getRoutes().find((candidate) => candidate.name === 'programma')

    expect(route).toBeDefined()
    expect(route?.path).toBe('/programma')
  })

  it('protects admin users route for admin role', () => {
    const route = router.getRoutes().find((candidate) => candidate.name === 'admin-users')

    expect(route).toBeDefined()
    expect(route?.meta.requiresAuth).toBe(true)
    expect(route?.meta.minRole).toBe('admin')
  })

  it('uses home route title metadata', () => {
    const route = router.getRoutes().find((candidate) => candidate.name === 'home')

    expect(route).toBeDefined()
    expect(route?.meta.title).toContain('Roboktober')
  })
})
