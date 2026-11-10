import { expect, test } from '@playwright/test';

async function expectRevealed(card) {
    await card.scrollIntoViewIfNeeded();
    await expect(card).toHaveAttribute('data-gyos-revealed', '');
    // Playwright's toBeVisible() alone accepts opacity: 0.
    await expect(card).toHaveCSS('opacity', '1');
}

for (const viaCreate of [false, true]) {
    test(`product cards reveal after stocktake${viaCreate ? ' and create' : ''}`, async ({ page }) => {
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        await page.goto('/stocktake');
        await expectRevealed(page.locator('.stocktake-row').first());
        await page.evaluate(() => { window.__revealDocument = 'same'; });

        if (viaCreate) {
            await page.locator('nav a[href$="/products/create"]').click();
            await expect(page).toHaveURL(/\/products\/create$/);
        }
        await page.locator('nav a[href$="/products"]').click();
        await expect(page).toHaveURL(/\/products$/);
        expect(await page.evaluate(() => window.__revealDocument)).toBe('same');
        await expect(page.locator('.product-card')).toHaveCount(12);
        await expectRevealed(page.locator('.product-card').first());

        await page.getByRole('link', { name: 'Load 12 more' }).click();
        await expect(page.locator('.product-card')).toHaveCount(24);
        await expectRevealed(page.locator('.product-card').nth(12));
        expect(errors).toEqual([]);
    });
}

test('overview metrics reveal on initial load, boosted visits and snapshot history', async ({ page }) => {
    await page.goto('/');
    await expectRevealed(page.locator('.metric-card').first());
    await page.locator('nav a[href$="/stocktake"]').click();
    await expect(page).toHaveURL(/\/stocktake$/);
    await expectRevealed(page.locator('.stocktake-row').first());
    await page.locator('nav a').filter({ hasText: 'Overview' }).click();
    await expectRevealed(page.locator('.metric-card').first());
    await page.goBack();
    await expect(page).toHaveURL(/\/stocktake$/);
    await page.goForward();
    await expectRevealed(page.locator('.metric-card').first());
});
