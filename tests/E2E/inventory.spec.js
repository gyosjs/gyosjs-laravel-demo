import { expect, test } from '@playwright/test';

test('the full Inventory Desk journey remains server owned and boost enhanced', async ({ page }) => {
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));

    await page.goto('/products');
    await expect(page.locator('.product-card')).toHaveCount(12);
    await page.evaluate(() => { window.__inventoryDeskDocument = 'same-document'; });

    await page.locator('#q').fill('Arc');
    await page.locator('#category').selectOption('Audio');
    await Promise.all([
        page.waitForURL(url => url.pathname === '/products' && url.searchParams.get('q') === 'Arc'),
        page.getByRole('button', { name: 'Apply filters' }).click(),
    ]);
    await expect(page.locator('.product-card')).toHaveCount(4);
    expect(await page.evaluate(() => window.__inventoryDeskDocument)).toBe('same-document');

    await page.getByRole('link', { name: 'Quick view' }).first().click();
    await expect(page.locator('.modal')).toBeVisible();
    await page.getByRole('button', { name: 'Close modal' }).click();
    await expect(page.locator('.modal-overlay')).toBeHidden();

    await page.getByRole('button', { name: 'Open ops notes' }).click();
    await page.locator('.scratchpad textarea').fill('Recount aisle B');
    const scratchpad = await page.locator('.scratchpad').elementHandle();

    await page.getByRole('link', { name: /Edit ·/ }).first().click();
    await page.waitForURL(/\/products\/\d+\/edit$/);
    expect(await scratchpad.evaluate(node => node.isConnected)).toBe(true);
    await expect(page.locator('.scratchpad textarea')).toHaveValue('Recount aisle B');
    await page.getByRole('button', { name: 'Close notes' }).click();

    await page.locator('#name').fill('');
    await page.getByRole('button', { name: 'Next step' }).click();
    await page.locator('#price').fill('-1');
    await page.getByRole('button', { name: 'Next step' }).click();
    await page.locator('#stock').fill('-1');
    await page.getByRole('button', { name: 'Save product' }).click();
    await expect(page.locator('.error-summary')).toBeVisible();
    await expect(page.locator('[gd-step="1"]')).toHaveCount(0);

    await page.getByRole('button', { name: '01 Details' }).click();
    await page.locator('#name').fill('Arc Headphones Field Edition');
    await page.getByRole('button', { name: '02 Pricing' }).click();
    await page.locator('#price').fill('149.90');
    await page.getByRole('button', { name: '03 Inventory' }).click();
    await page.locator('#stock').fill('18');
    await page.getByRole('button', { name: 'Save product' }).click();
    await page.waitForURL(/\/products\/\d+$/);
    await expect(page.locator('.flash')).toContainText('Product saved');
    await expect(page.getByRole('heading', { name: 'Arc Headphones Field Edition', level: 2 })).toBeVisible();
    expect(await scratchpad.evaluate(node => node.isConnected)).toBe(true);
    await page.getByRole('button', { name: 'Open ops notes' }).click();
    await expect(page.locator('.scratchpad textarea')).toHaveValue('Recount aisle B');

    await page.goBack();
    await page.waitForURL(/\/products\/\d+\/edit$/);
    await page.goForward();
    await page.waitForURL(/\/products\/\d+$/);

    await page.goto('/products');
    await expect(page.locator('.product-card')).toHaveCount(12);
    await page.getByRole('link', { name: 'Load 12 more' }).click();
    await expect(page.locator('.product-card')).toHaveCount(24);

    expect(errors).toEqual([]);
});

test('core links and forms retain a no-JavaScript fallback', async ({ browser }) => {
    const context = await browser.newContext({ javaScriptEnabled: false });
    const page = await context.newPage();

    await page.goto('/products?q=Arc');
    await expect(page.locator('.product-card')).toHaveCount(4);
    await page.getByRole('link', { name: /Arc Headphones/ }).first().click({ force: true });
    await expect(page).toHaveURL(/\/products\/\d+$/);

    await context.close();
});
