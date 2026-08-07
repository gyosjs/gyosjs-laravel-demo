import { defineConfig } from '@playwright/test';

const baseURL = process.env.PLAYWRIGHT_BASE_URL || 'http://127.0.0.1:8790';

export default defineConfig({
    testDir: './tests/E2E',
    timeout: 30_000,
    fullyParallel: false,
    use: {
        baseURL,
        headless: true,
        launchOptions: process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE_PATH
            ? { executablePath: process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE_PATH }
            : {},
        screenshot: 'only-on-failure',
        trace: 'retain-on-failure',
    },
    webServer: process.env.PLAYWRIGHT_BASE_URL ? undefined : {
        command: 'php artisan serve --host=127.0.0.1 --port=8790',
        url: baseURL,
        reuseExistingServer: true,
        timeout: 30_000,
    },
});
