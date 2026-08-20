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
    await expect(page.getByRole('link', { name: 'Load 12 more' })).toHaveCount(1);
    await expect(page.getByRole('link', { name: 'Load 12 more' })).toHaveAttribute('href', /\/products\?page=2$/);
    await expect(page.getByRole('link', { name: 'Load 12 more' })).toHaveAttribute('g-router-link', /\/products\/load-more\?page=2$/);
    await page.getByRole('link', { name: 'Load 12 more' }).click();
    await expect(page.locator('.product-card')).toHaveCount(24);
    await expect(page).toHaveURL(/\/products$/);
    await expect(page.getByRole('link', { name: 'Load 12 more' })).toHaveCount(1);
    await expect(page.getByRole('link', { name: 'Load 12 more' })).toHaveAttribute('href', /\/products\?page=3$/);
    await expect(page.getByRole('link', { name: 'Load 12 more' })).toHaveAttribute('g-router-link', /\/products\/load-more\?page=3$/);
    await page.reload();
    await expect(page.locator('.product-card')).toHaveCount(12);

    expect(errors).toEqual([]);
});

test('boosted create and edit visits render the requested form record', async ({ page }) => {
    await page.goto('/products');

    const cards = page.locator('.product-card');
    const firstId = await cards.nth(0).getAttribute('data-product-id');
    const secondId = await cards.nth(1).getAttribute('data-product-id');
    const thirdId = await cards.nth(2).getAttribute('data-product-id');
    const firstName = (await cards.nth(0).locator('h3').textContent()).trim();
    const secondName = (await cards.nth(1).locator('h3').textContent()).trim();
    const thirdName = (await cards.nth(2).locator('h3').textContent()).trim();

    await cards.nth(0).getByRole('link', { name: /Edit/ }).click();
    await expect(page).toHaveURL(new RegExp(`/products/${firstId}/edit$`));
    await expect(page.locator('.form-card')).toHaveAttribute('action', new RegExp(`/products/${firstId}$`));
    await expect(page.locator('#name')).toHaveValue(firstName);

    await page.getByRole('link', { name: 'Products' }).click();
    await page.locator(`.product-card[data-product-id="${secondId}"]`).getByRole('link', { name: /Edit/ }).click();
    await expect(page).toHaveURL(new RegExp(`/products/${secondId}/edit$`));
    await expect(page.locator('.form-card')).toHaveAttribute('action', new RegExp(`/products/${secondId}$`));
    await expect(page.locator('#name')).toHaveValue(secondName);

    await page.getByRole('link', { name: 'Add product' }).click();
    await expect(page).toHaveURL(/\/products\/create$/);
    await expect(page.locator('.form-card')).toHaveAttribute('action', /\/products$/);
    await expect(page.locator('.form-card input[name="_method"]')).toHaveCount(0);
    await expect(page.locator('#name')).toHaveValue('');

    await page.getByRole('link', { name: 'Products' }).click();
    await page.locator(`.product-card[data-product-id="${thirdId}"]`).getByRole('link', { name: /Edit/ }).click();
    await expect(page).toHaveURL(new RegExp(`/products/${thirdId}/edit$`));
    await expect(page.locator('.form-card')).toHaveAttribute('action', new RegExp(`/products/${thirdId}$`));
    await expect(page.locator('#name')).toHaveValue(thirdName);
});

test('core links and forms retain a no-JavaScript fallback', async ({ browser }) => {
    const context = await browser.newContext({ javaScriptEnabled: false });
    const page = await context.newPage();

    await page.goto('/products');
    await page.getByRole('link', { name: 'Load 12 more' }).click({ force: true });
    await expect(page).toHaveURL(/\/products\?page=2$/);
    await expect(page.locator('.product-card')).toHaveCount(12);
    await expect(page.locator('main.content')).toBeVisible();

    await page.goto('/products?q=Arc');
    await expect(page.locator('.product-card')).toHaveCount(4);
    await page.getByRole('link', { name: /Arc Headphones/ }).first().click({ force: true });
    await expect(page).toHaveURL(/\/products\/\d+$/);

    await page.goto('/stocktake');
    await expect(page.locator('.stocktake-row')).toHaveCount(12);
    await expect(page.locator('form.stocktake-card')).toHaveAttribute('action', /\/stocktake$/);
    await expect(page.locator('input[name^="counts["]')).toHaveCount(12);

    await context.close();
});

test('delete confirmation can cancel a native server form', async ({ page }) => {
    await page.goto('/products');
    await page.getByRole('link', { name: /Arc Headphones/ }).first().click();
    await expect(page).toHaveURL(/\/products\/\d+$/);

    page.once('dialog', dialog => dialog.dismiss());
    await page.getByRole('button', { name: 'Delete' }).click();
    await expect(page).toHaveURL(/\/products\/\d+$/);
    await expect(page.getByRole('button', { name: 'Delete' })).toBeVisible();
});

test('strict CSP stocktake keeps nonce ownership and submits reactively', async ({ page }) => {
    const violations = [];
    page.on('securitypolicyviolation', event => violations.push(event.effectiveDirective));
    page.on('pageerror', error => violations.push(error.message));

    const initialResponse = await page.goto('/stocktake');
    const initialNonce = await page.locator('meta[name="csp-nonce"]').getAttribute('content');
    const initialPolicy = initialResponse.headers()['content-security-policy'];

    expect(initialPolicy).toContain("script-src 'self' 'nonce-");
    expect(initialPolicy).not.toContain("'unsafe-eval'");
    expect(initialPolicy).not.toContain("'unsafe-inline'");
    expect(initialNonce).toBeTruthy();
    await expect(page.locator('[g-reveal]').first()).toHaveAttribute('data-gyos-revealed', '');

    const firstInput = page.locator('.stocktake-row').first().locator('input');
    const secondInput = page.locator('.stocktake-row').nth(1).locator('input');
    const currentStock = Number(await firstInput.inputValue());
    await firstInput.fill(String(currentStock + 4));
    await expect(page.locator('.stocktake-row.is-changed')).toHaveCount(1);
    await expect(page.locator('.stocktake-summary')).toBeVisible();

    await secondInput.fill('');
    await page.getByRole('button', { name: 'Save stocktake' }).first().click();
    await expect(page.locator('.stocktake-row').nth(1).locator('.field-error')).toContainText('required');
    await expect(page).toHaveURL(/\/stocktake$/);

    await secondInput.fill(String(await secondInput.getAttribute('value')));
    const submitResponse = page.waitForResponse(response => response.url().endsWith('/stocktake') && response.request().method() === 'POST');
    await page.getByRole('button', { name: 'Save stocktake' }).first().click();
    await submitResponse;
    await expect(page).toHaveURL(/\/stocktake$/);
    await expect(page.locator('.flash')).toContainText('Stocktake saved');

    const navigation = page.waitForResponse(response => response.url().endsWith('/products') && response.request().method() === 'GET');
    await page.getByRole('link', { name: 'Products' }).click();
    const response = await navigation;
    const fetchedPolicy = response.headers()['content-security-policy'];
    expect(fetchedPolicy).toContain("'nonce-");
    expect(fetchedPolicy).not.toContain(initialNonce);
    await expect(page.locator('meta[name="csp-nonce"]')).toHaveAttribute('content', initialNonce);
    expect(violations).toEqual([]);
});
