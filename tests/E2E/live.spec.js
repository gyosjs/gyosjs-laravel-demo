import { expect, test } from '@playwright/test';

test('public Inventory Desk keeps its read-only GyosJS journey healthy @live', async ({ page }) => {
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));

    await page.goto('/products');
    await expect(page.locator('.product-card')).toHaveCount(12);
    await page.evaluate(() => { window.__inventoryLiveDocument = 'same-document'; });

    await page.locator('#q').fill('Arc');
    await Promise.all([
        page.waitForURL(url => url.pathname === '/products' && url.searchParams.get('q') === 'Arc'),
        page.getByRole('button', { name: 'Apply filters' }).click(),
    ]);
    await expect(page.locator('.product-card')).toHaveCount(4);
    expect(await page.evaluate(() => window.__inventoryLiveDocument)).toBe('same-document');

    await page.getByRole('link', { name: 'Quick view' }).first().click();
    await expect(page.locator('.modal')).toBeVisible();
    await page.getByRole('button', { name: 'Close modal' }).click();
    await expect(page.locator('.modal-overlay')).toBeHidden();

    await page.getByRole('link', { name: 'Products' }).click();
    const cards = page.locator('.product-card');
    const firstId = await cards.nth(0).getAttribute('data-product-id');
    const secondId = await cards.nth(1).getAttribute('data-product-id');
    const firstName = (await cards.nth(0).locator('h3').textContent()).trim();
    const secondName = (await cards.nth(1).locator('h3').textContent()).trim();

    await cards.nth(0).getByRole('link', { name: /Edit/ }).click();
    await expect(page).toHaveURL(new RegExp(`/products/${firstId}/edit$`));
    await expect(page.locator('#name')).toHaveValue(firstName);
    await page.getByRole('link', { name: 'Products' }).click();
    await page.locator(`.product-card[data-product-id="${secondId}"]`).getByRole('link', { name: /Edit/ }).click();
    await expect(page).toHaveURL(new RegExp(`/products/${secondId}/edit$`));
    await expect(page.locator('#name')).toHaveValue(secondName);

    await page.getByRole('link', { name: 'Add product' }).click();
    await expect(page).toHaveURL(/\/products\/create$/);
    await expect(page.locator('#name')).toHaveValue('');

    await page.getByRole('link', { name: 'Products' }).click();
    await expect(page.getByRole('link', { name: 'Load 12 more' })).toHaveAttribute('href', /\/products\?page=2$/);
    await page.getByRole('link', { name: 'Load 12 more' }).click();
    await expect(page.locator('.product-card')).toHaveCount(24);
    await expect(page).toHaveURL(/\/products$/);
    await expect(page.getByRole('link', { name: 'Load 12 more' })).toHaveCount(1);
    expect(errors).toEqual([]);
});
