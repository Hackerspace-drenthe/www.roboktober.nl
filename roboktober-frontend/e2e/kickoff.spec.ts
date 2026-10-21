import { expect, test } from '@playwright/test'

for (const path of ['programma', 'nieuws']) {
  test(`kickoff terugkijken op ${path}`, async ({ page }) => {
    await page.goto(path)
    const heading = page.getByRole('heading', { name: 'Kickoff terugkijken', exact: true })
    await expect(heading).toHaveCount(1)
    await heading.scrollIntoViewIfNeeded()
    await expect(heading).toBeVisible()
    const link = page.getByRole('link', { name: 'Bekijk de kickoff op YouTube' })
    await expect(link).toHaveAttribute('href', 'https://www.youtube.com/live/qtz0UkJw4DU')
    await expect(link).toHaveAttribute('target', '_blank')
    await expect(link).toHaveAttribute('rel', 'noopener noreferrer')
    await expect(page.locator('iframe[src*="youtube"]')).toHaveCount(0)
    if (path === 'nieuws') {
      await expect(page.locator('#video').getByRole('heading', { name: 'Kickoff terugkijken' })).toBeVisible()
    }
  })
}
