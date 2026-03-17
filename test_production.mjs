import { chromium } from 'playwright';

(async () => {
    console.log('Starting Playwright test on production...');
    
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();
    
    try {
        // Go to login page
        console.log('1. Navigating to https://gotrips.ai/login...');
        await page.goto('https://gotrips.ai/login', { waitUntil: 'networkidle', timeout: 30000 });
        await page.screenshot({ path: 'tests/prod-1-login-page.png', fullPage: true });
        console.log('   Screenshot saved: tests/prod-1-login-page.png');
        
        // Check page title
        const title = await page.title();
        console.log('   Page title: ' + title);
        
        // Fill login form as ActivitiesManager
        console.log('2. Logging in as ActivitiesManager...');
        await page.fill('input[name="name"]', 'ActivitiesManager');
        await page.fill('input[name="password"]', 'test123');
        await page.screenshot({ path: 'tests/prod-2-filled-form.png', fullPage: true });
        
        // Click submit
        console.log('3. Clicking submit button...');
        await page.click('button[type="submit"]');
        
        // Wait for navigation
        console.log('4. Waiting for response...');
        await page.waitForTimeout(5000);
        await page.screenshot({ path: 'tests/prod-3-after-login.png', fullPage: true });
        
        // Check current URL
        const currentUrl = page.url();
        console.log('   Current URL: ' + currentUrl);
        
        // Check for error messages
        const errorText = await page.locator('.invalid-feedback, .alert-danger, .text-danger').first().textContent().catch(() => null);
        if (errorText) {
            console.log('   ERROR MESSAGE: ' + errorText.trim());
        }
        
        // Check if we're logged in (look for sidebar or dashboard elements)
        const isLoggedIn = await page.locator('.sidebar, #accordionSidebar').count() > 0;
        console.log('   Logged in: ' + isLoggedIn);
        
        if (isLoggedIn) {
            console.log('5. SUCCESS! Taking dashboard screenshot...');
            await page.screenshot({ path: 'tests/prod-4-dashboard.png', fullPage: true });
            
            // Check what menu items are visible
            const menuItems = await page.locator('.sidebar .nav-link span, .sidebar .nav-item a').allTextContents();
            console.log('   Visible menu items: ' + menuItems.filter(t => t.trim()).join(', '));
        } else {
            console.log('5. FAILED - Still on login page or error');
            
            // Get page content for debugging
            const bodyText = await page.locator('body').textContent();
            console.log('   Page content preview: ' + bodyText.substring(0, 500));
        }
        
        console.log('\nTest completed!');
        
    } catch (error) {
        console.log('ERROR: ' + error.message);
        await page.screenshot({ path: 'tests/prod-error.png', fullPage: true });
    } finally {
        await browser.close();
    }
})();
