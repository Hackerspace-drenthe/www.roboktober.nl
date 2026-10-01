import { expect, test } from '@playwright/test'

test.describe('bouwen: 3D print pagina', () => {
  test('rendert heading, actieve tab en titel', async ({ page }) => {
    const response = await page.goto('bouwen/3dprint')

    expect(response?.ok()).toBeTruthy()
    await expect(page).toHaveURL(/\/app\/bouwen\/3dprint$/)
    await expect(page).toHaveTitle(/3D print chassis/)
    await expect(page.getByRole('heading', { level: 1, name: '3D print chassis' })).toBeVisible()
    await expect(page.getByRole('tab', { name: '3D print' })).toHaveAttribute('aria-selected', 'true')
    await expect(page.getByRole('tab', { name: 'Firmware' })).toHaveAttribute('aria-selected', 'false')
  })

  test('STL download linkt naar originele MakerWorld pagina', async ({ page }) => {
    await page.goto('bouwen/3dprint')

    const link = page.getByRole('link', { name: /Download STL op MakerWorld/ })
    await expect(link).toBeVisible()
    await expect(link).toHaveAttribute('href', /^https:\/\/makerworld\.com\/en\/models\/622437-wallieonline-mini-robot-chassis/)
    await expect(link).toHaveAttribute('target', '_blank')
    await expect(link).toHaveAttribute('rel', 'noopener noreferrer')
    await expect(page.locator('a[href*="/downloads/stl/"]')).toHaveCount(0)
  })

  test('toont printinstellingen en montage-checklist', async ({ page }) => {
    await page.goto('bouwen/3dprint')

    await expect(page.getByRole('heading', { name: /Stap 2: aanbevolen printinstellingen/ })).toBeVisible()
    await expect(page.getByRole('cell', { name: 'Materiaal' })).toBeVisible()
    await expect(page.getByRole('heading', { name: /Stap 3: snelle montage-checklist/ })).toBeVisible()
  })

  test('afbeeldingen laden zonder fouten', async ({ page }) => {
    await page.goto('bouwen/3dprint')
    await expect(page.getByRole('heading', { level: 1, name: '3D print chassis' })).toBeVisible()

    const images = page.locator('main img')
    const count = await images.count()
    expect(count).toBeGreaterThan(0)

    for (let i = 0; i < count; i++) {
      const img = images.nth(i)
      await img.scrollIntoViewIfNeeded()
      await expect.poll(() => img.evaluate((el: HTMLImageElement) => el.complete && el.naturalWidth > 0)).toBe(true)
    }
  })
})

test.describe('bouwen: firmware pagina', () => {
  test('rendert heading en actieve tab', async ({ page }) => {
    const response = await page.goto('bouwen/firmware')

    expect(response?.ok()).toBeTruthy()
    await expect(page).toHaveURL(/\/app\/bouwen\/firmware$/)
    await expect(page.getByRole('heading', { level: 1, name: 'Firmware en remote app bouwen' })).toBeVisible()
    await expect(page.getByRole('tab', { name: 'Firmware' })).toHaveAttribute('aria-selected', 'true')
    await expect(page.getByRole('tab', { name: '3D print' })).toHaveAttribute('aria-selected', 'false')
  })

  test('externe Wallie links openen veilig in nieuw venster', async ({ page }) => {
    await page.goto('bouwen/firmware')

    const expected = [
      { name: 'Open guide', href: 'https://wallieonline.nl/blogs/worc-robot-controller-nl.html' },
      { name: 'Open GitHub repo', href: 'https://github.com/wallieonline/worc-robot-controller-esp32c3' },
      { name: 'Open readmac.ino (origineel)', href: /readmac\.ino$/ },
    ]

    for (const { name, href } of expected) {
      const link = page.getByRole('link', { name })
      await expect(link).toBeVisible()
      await expect(link).toHaveAttribute('href', href)
      await expect(link).toHaveAttribute('target', '_blank')
      await expect(link).toHaveAttribute('rel', 'noopener noreferrer')
    }
  })

  test('toont bouw-, flash-, WebUI- en remote-secties', async ({ page }) => {
    await page.goto('bouwen/firmware')

    for (const name of ['WORC controller bouwen', 'Webflash stappen', 'WebUI verbinden', 'Remote app koppelen']) {
      await expect(page.getByRole('heading', { level: 2, name })).toBeVisible()
    }
    await expect(page.getByText('192.168.4.1')).toBeVisible()
  })

  test('tab-navigatie wisselt tussen firmware en 3D print', async ({ page }) => {
    await page.goto('bouwen/firmware')

    await page.getByRole('tab', { name: '3D print' }).click()
    await expect(page).toHaveURL(/\/app\/bouwen\/3dprint$/)
    await expect(page.getByRole('heading', { level: 1, name: '3D print chassis' })).toBeVisible()

    await page.getByRole('tab', { name: 'Firmware' }).click()
    await expect(page).toHaveURL(/\/app\/bouwen\/firmware$/)
    await expect(page.getByRole('heading', { level: 1, name: 'Firmware en remote app bouwen' })).toBeVisible()
  })
})
