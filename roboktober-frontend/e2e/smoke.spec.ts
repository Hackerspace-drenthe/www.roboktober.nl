import { expect, test } from '@playwright/test'

test.describe('public smoke flows', () => {
  test('home route loads and renders primary heading', async ({ page }) => {
    await page.goto('')

    await expect(page).toHaveURL(/\/app\/?$/)
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible()
  })

  test('programma route is reachable and renders content shell', async ({ page }) => {
    const response = await page.goto('programma')

    expect(response?.ok()).toBeTruthy()
    await expect(page).toHaveURL(/\/app\/programma$/)
    await expect(page.locator('body')).toContainText(/.+/)
  })
})
