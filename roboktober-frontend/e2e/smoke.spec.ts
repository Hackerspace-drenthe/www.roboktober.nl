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

  test('nieuws route is reachable and renders content shell', async ({ page }) => {
    const response = await page.goto('nieuws')

    expect(response?.ok()).toBeTruthy()
    await expect(page).toHaveURL(/\/app\/nieuws$/)
    await expect(page.locator('body')).toContainText(/.+/)
  })

  test('aanmelden route redirects unauthenticated visitor to login', async ({ page }) => {
    await page.goto('aanmelden')

    await expect(page).toHaveURL(/\/app\/login\?redirect=\/aanmelden$/)
  })

  test('account route redirects unauthenticated visitor to login', async ({ page }) => {
    await page.goto('account')

    await expect(page).toHaveURL(/\/app\/login\?redirect=\/account$/)
  })

  test('admin route redirects unauthenticated visitor to login', async ({ page }) => {
    await page.goto('admin/users')

    await expect(page).toHaveURL(/\/app\/login\?redirect=\/admin\/users$/)
  })

  test('moderator admin route redirects unauthenticated visitor to login', async ({ page }) => {
    await page.goto('admin')

    await expect(page).toHaveURL(/\/app\/login\?redirect=\/admin$/)
  })
})
