import { chromium } from 'playwright';

(async () => {
    console.log('Testing sidebar visibility for ActivitiesManager...');
    
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();
    
    try {
        // Login
        await page.goto('https://gotrips.ai/login', { waitUntil: 'networkidle', timeout: 30000 });
        await page.fill('input[name="name"]', 'ActivitiesManager');
        await page.fill('input[name="password"]', 'test123');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);
        
        console.log('Current URL: ' + page.url());
        await page.screenshot({ path: 'tests/prod-activities-dashboard.png', fullPage: true });
        
        // Check sidebar items
        const navLinks = await page.locator('#accordionSidebar .nav-link').all();
        console.log('\nSidebar navigation items found: ' + navLinks.length);
        
        for (let i = 0; i < navLinks.length; i++) {
            const text = await navLinks[i].textContent();
            const href = await navLinks[i].getAttribute('href');
            console.log('  - ' + text.trim() + ' -> ' + href);
        }
        
        // Check if Admin Users link is visible (it should NOT be for ActivitiesManager)
        const adminUsersLink = await page.locator('a[href*="admin/users"]').count();
        console.log('\nAdmin Users link visible: ' + (adminUsersLink > 0 ? 'YES (BUG!)' : 'NO (correct)'));
        
        // Check if UAE Activities link is visible (it should be)
        const activitiesLink = await page.locator('a[href*="uaeactivities"]').count();
        console.log('UAE Activities link visible: ' + (activitiesLink > 0 ? 'YES (correct)' : 'NO (BUG!)'));
        
        // Try to access admin/users directly (should be blocked)
        console.log('\nTrying to access /admin/users directly...');
        await page.goto('https://gotrips.ai/admin/users', { waitUntil: 'networkidle', timeout: 30000 });
        await page.waitForTimeout(2000);
        console.log('After access attempt URL: ' + page.url());
        await page.screenshot({ path: 'tests/prod-users-access-attempt.png', fullPage: true });
        
        if (page.url().includes('admin/users')) {
            console.log('WARNING: ActivitiesManager can access admin/users! (SECURITY BUG)');
        } else {
            console.log('SUCCESS: ActivitiesManager was blocked from admin/users');
        }
        
        console.log('\nTest completed!');
        
    } catch (error) {
        console.log('ERROR: ' + error.message);
        await page.screenshot({ path: 'tests/prod-error.png', fullPage: true });
    } finally {
        await browser.close();
    }
})();
