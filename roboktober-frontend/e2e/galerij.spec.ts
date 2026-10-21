import { expect, test } from '@playwright/test'

test('galerij toont alle rubrieken en werkende afbeeldingen', async ({ page }) => {
  await page.goto('galerij')
  await expect(page).toHaveTitle("Foto's en video's — Roboktober")
  await expect(page.getByRole('heading', { level: 1 })).toHaveText("Foto's en video's")
  for (const name of ['Robots', 'Samen bouwen', 'Controllers', 'Video']) {
    await expect(page.getByRole('heading', { level: 2, name, exact: true })).toBeVisible()
  }
  const images = page.locator('main figure img')
  await expect(images).toHaveCount(15)
  for (const image of await images.all()) {
    await image.scrollIntoViewIfNeeded()
    await expect.poll(() => image.evaluate((node: HTMLImageElement) =>
      node.complete && node.naturalWidth > 0,
    )).toBe(true)
  }
})

test('foto vergroot met toetsenbord, sluit met Escape en geeft focus terug', async ({ page }) => {
  await page.goto('galerij')
  const button = page.getByRole('button', { name: 'Vergroot Robots — foto 1', exact: true })
  await button.focus()
  await page.keyboard.press('Enter')
  const dialog = page.getByRole('dialog', { name: 'Robots — foto 1' })
  await expect(dialog).toBeVisible()
  await expect.poll(() => dialog.locator('img').evaluate((node: HTMLImageElement) =>
    node.complete && node.naturalWidth > 0,
  )).toBe(true)
  await expect(dialog.getByRole('link', { name: 'Download foto (WebP)' })).toHaveAttribute('download', '')
  await page.keyboard.press('Escape')
  await expect(dialog).not.toBeVisible()
  await expect(button).toBeFocused()
  await button.click()
  await page.getByRole('button', { name: 'Sluiten', exact: true }).click()
  await expect(dialog).not.toBeVisible()
})

test('video heeft poster, handmatige bediening en een werkende download', async ({ page, request }) => {
  await page.goto('galerij')
  const video = page.locator('video')
  await expect(video).toHaveAttribute('controls', '')
  await expect(video).toHaveAttribute('preload', 'none')
  await expect(video).not.toHaveAttribute('autoplay')
  const poster = await request.get((await video.getAttribute('poster'))!)
  expect(poster.ok()).toBe(true)
  expect(poster.headers()['content-type']).toContain('image/webp')
  const download = page.getByRole('link', { name: 'Download video (MP4)' })
  const response = await request.get((await download.getAttribute('href'))!)
  expect(response.ok()).toBe(true)
  expect(response.headers()['content-type']).toContain('video/mp4')
  await video.scrollIntoViewIfNeeded()
  await video.evaluate((element: HTMLVideoElement) => element.load())
  await expect.poll(() => video.evaluate((element: HTMLVideoElement) => element.readyState)).toBeGreaterThanOrEqual(1)
  expect(await video.evaluate((element: HTMLVideoElement) => element.duration)).toBeCloseTo(19.9, 0)
})

for (const width of [390, 768, 1024, 1440]) {
  test(`galerij is bereikbaar via Nieuws en past op ${width}px`, async ({ page }) => {
    await page.setViewportSize({ width, height: 900 })
    await page.goto('galerij')
    if (width < 1280) await page.getByRole('button', { name: 'Menu openen' }).click()
    await expect(page.getByRole('link', { name: 'Galerij', exact: true })).toHaveCount(0)
    const link = page.getByRole('link', { name: 'Nieuws', exact: true }).filter({ visible: true })
    await expect(link).toBeVisible()
    await link.click()
    await expect(page).toHaveURL(/\/app\/nieuws$/)
    await expect(page.getByRole('heading', { level: 1 })).toHaveText('Nieuws')
    await expect(page.getByRole('heading', { level: 2, name: "Foto's en video's" })).toBeVisible()
    await expect(page.locator('#galerij figure img')).toHaveCount(15)
    await expect.poll(() => page.evaluate(() =>
      document.documentElement.scrollWidth <= window.innerWidth,
    )).toBe(true)
  })
}

for (const state of ['articles', 'empty', 'error']) {
  test(`nieuwsgalerij werkt bij nieuwsstatus ${state}`, async ({ page }) => {
    await page.route('**/api/v1/posts?*', async (route) => {
      if (state === 'error') {
        await route.fulfill({ status: 500, json: { message: 'Niet beschikbaar' } })
      } else {
        await route.fulfill({ json: {
          data: state === 'articles'
            ? [{ id: 1, slug: 'bouwavond', titel: 'Samen bouwen', excerpt: 'Een bouwavond bij Roboktober.', published_at: '2026-10-20T18:00:00Z' }]
            : [],
        } })
      }
    })
    await page.goto('nieuws')
    if (state === 'articles') {
      await expect(page.getByRole('heading', { name: 'Samen bouwen', level: 2 })).toBeVisible()
    } else if (state === 'empty') {
      await expect(page.getByText('Nog geen artikelen gepubliceerd.')).toBeVisible()
    } else {
      await expect(page.getByRole('alert')).toContainText('Nieuws kon niet worden geladen.')
    }
    expect(await page.locator('main').evaluate((main) => {
      const sections = [...main.children]
      return sections.at(-1)?.id === 'galerij'
    })).toBe(true)
    await page.getByRole('link', { name: "Bekijk foto's en video's" }).click()
    await expect(page).toHaveURL(/\/nieuws#galerij$/)
    await page.getByRole('button', { name: 'Vergroot Robots — foto 1', exact: true }).click()
    await expect(page.getByRole('dialog', { name: 'Robots — foto 1' })).toBeVisible()
    await page.keyboard.press('Escape')
    await expect(page.locator('#galerij video')).toHaveAttribute('controls', '')
  })
}
