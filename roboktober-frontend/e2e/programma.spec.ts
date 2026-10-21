import { expect, test } from '@playwright/test'

const edition = {
  id: 1,
  naam: 'Roboktober 2026',
  start_at: '2026-10-01T09:00:00+02:00',
  end_at: '2026-10-31T21:00:00+01:00',
  is_done: false,
  location: {
    name: 'Hackerspace Drenthe',
    full_address: 'Teststraat 1, 9400 AA Assen',
    instructions: 'Gebruik de ingang aan de zijkant.',
  },
}
const activities = [
  { id: 1, titel: 'Bouwavond', start_at: '2026-10-21T19:00:00+02:00', end_at: '2026-10-21T22:00:00+02:00', volgorde: 1, beschrijving_rendered: '<p>Samen werken aan je robot.</p>' },
  { id: 2, titel: 'Testmoment', start_at: '2026-10-21T20:00:00+02:00', end_at: null, volgorde: 2, beschrijving_rendered: '<p>Controleer de besturing.</p>' },
]

test.beforeEach(async ({ page }) => {
  await page.route('**/api/v1/edities', (route) => route.fulfill({ json: { data: [edition] } }))
  await page.route('**/api/v1/edities/1/programma', (route) => route.fulfill({ json: { data: activities } }))
})

test('programma behoudt activiteiten, datums en locatie en toont echte foto’s', async ({ page }) => {
  await page.goto('programma')
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Programma')
  await expect(page.getByRole('heading', { name: 'Bouwavond', exact: true })).toBeVisible()
  await expect(page.getByRole('heading', { name: 'Testmoment', exact: true })).toBeVisible()
  await expect(page.locator('time').first()).toHaveAttribute('datetime', activities[0]!.start_at)
  await expect(page.getByText('Samen werken aan je robot.')).toBeVisible()
  await expect(page.getByText(edition.location.full_address)).toBeVisible()
  await expect(page.getByText(edition.location.instructions)).toBeVisible()
  await expect(page.getByRole('button', { name: 'Open route in Google Maps' })).toBeEnabled()
  await expect(page.getByTitle('Locatiekaart Roboktober')).toHaveAttribute('src', /Teststraat/)
  const images = page.locator('main img')
  await expect(images).toHaveCount(3)
  for (const image of await images.all()) {
    await image.scrollIntoViewIfNeeded()
    await expect.poll(() => image.evaluate((element: HTMLImageElement) => element.complete && element.naturalWidth > 0)).toBe(true)
  }
  await expect(page.getByRole('link', { name: "Meer foto's en video's" })).toHaveAttribute('href', '/app/nieuws#galerij')
})

for (const state of ['empty', 'error', 'no-location']) {
  test(`programma toont duidelijke status bij ${state}`, async ({ page }) => {
    if (state === 'error') {
      await page.route('**/api/v1/edities', (route) => route.fulfill({ status: 500, json: { message: 'Niet beschikbaar' } }))
    } else if (state === 'empty') {
      await page.route('**/api/v1/edities', (route) => route.fulfill({ json: { data: [] } }))
    } else {
      await page.route('**/api/v1/edities', (route) => route.fulfill({ json: { data: [{ ...edition, location: null }] } }))
    }
    await page.goto('programma')
    if (state === 'error') {
      await expect(page.getByRole('alert')).toHaveText('Programma-informatie laden mislukt.')
    } else if (state === 'empty') {
      await expect(page.getByText('Er zijn nog geen programma-items gepubliceerd voor deze editie.')).toBeVisible()
    } else {
      await expect(page.getByRole('heading', { name: 'Bouwavond', exact: true })).toBeVisible()
    }
    await expect(page.getByRole('button', { name: 'Open route in Google Maps' })).toBeDisabled()
    await expect(page.getByTitle('Locatiekaart Roboktober')).toHaveCount(0)
    await expect(page.locator('main img')).toHaveCount(3)
  })
}

for (const width of [390, 768, 1440]) {
  test(`programma past op ${width}px en snelkoppelingen werken`, async ({ page }) => {
    await page.setViewportSize({ width, height: 900 })
    await page.goto('programma')
    await page.getByRole('link', { name: 'Bekijk het programma' }).click()
    await expect(page).toHaveURL(/#programma-title$/)
    await expect(page.getByRole('heading', { name: 'Programma per dag' })).toBeInViewport()
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth)).toBe(true)
  })
}
