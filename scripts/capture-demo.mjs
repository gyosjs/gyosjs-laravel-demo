import { mkdir } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { chromium } from 'playwright';

const baseURL = process.env.DEMO_BASE_URL || 'http://127.0.0.1:8790';
const output = resolve(process.env.DEMO_SCREENSHOT || 'docs/inventory-desk.png');
const executablePath = process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE_PATH;
const browser = await chromium.launch({
    headless: true,
    ...(executablePath ? { executablePath } : {}),
});

try {
    const page = await browser.newPage({ viewport: { width: 1440, height: 960 } });
    await page.goto(`${baseURL}/products`, { waitUntil: 'networkidle' });
    await page.locator('[data-product-id]').first().waitFor();
    await mkdir(dirname(output), { recursive: true });
    await page.screenshot({ path: output, fullPage: process.env.DEMO_FULL_PAGE !== 'false' });
    console.log(`Saved ${output}`);
} finally {
    await browser.close();
}
