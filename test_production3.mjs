import { chromium } from 'playwright';

(async () => {
    console.log('Testing with larger viewport...');
    
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 }
    });
    const page = await context.newPage();
    
    try {
        // Login as ActivitiesManager
        await page.goto('https://gotrips.ai/login', { waitUntil: 'networkidle', timeout: 30000 });
        await page.fill('input[name="name"]', 'ActivitiesManager');
        await page.fill('input[name="password"]', 'test123');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);
        
        await page.screenshot({ path: 'tests/prod-activities-full.png', fullPage: true });
        console.log('ActivitiesManager dashboard saved');
        
        // Check sidebar HTML
        const sidebarHTML = await page.locator('#accordionSidebar').innerHTML().catch(() => 'Not found');
        console.log('\nSidebar HTML:\n' + sidebarHTML.substring(0, 2000));
        
        // Logout
        await page.goto('https://gotrips.ai/logout', { waitUntil: 'networkidle' }).catch(() => {});
        
        // Login as Admin
        console.log('\n\nNow testing as Admin...');
        await page.goto('https://gotrips.ai/login', { waitUntil: 'networkidle', timeout: 30000 });
        await page.fill('input[name="name"]', 'Admin');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);
        
        console.log('Admin URL: ' + page.url());
        await page.screenshot({ path: 'tests/prod-admin-full.png', fullPage: true });
        console.log('Admin dashboard saved');
        
        // Check admin sidebar
        const adminSidebarHTML = await page.locator('#accordionSidebar').innerHTML().catch(() => 'Not found');
        console.log('\nAdmin Sidebar HTML:\n' + adminSidebarHTML.substring(0, 2000));
        
    } catch (error) {
        console.log('ERROR: ' + error.message);
    } finally {
        await browser.close();
    }
})();
