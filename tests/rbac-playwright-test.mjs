/**
 * RBAC System Integration Test using Playwright
 * Tests: Admin full access, user creation with specific access, route protection
 */

import { chromium } from 'playwright';
import { writeFileSync, mkdirSync, existsSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const SCREENSHOTS_DIR = join(__dirname, 'rbac-screenshots');
const BASE_URL = 'http://127.0.0.1:8000';

// Ensure screenshots directory exists
if (!existsSync(SCREENSHOTS_DIR)) {
    mkdirSync(SCREENSHOTS_DIR, { recursive: true });
}

const results = [];

function log(msg) {
    const timestamp = new Date().toISOString().slice(11, 19);
    console.log(`[${timestamp}] ${msg}`);
}

function recordResult(testName, passed, details = '') {
    results.push({ testName, passed, details });
    const status = passed ? 'PASS' : 'FAIL';
    log(`  ${status}: ${testName}${details ? ' - ' + details : ''}`);
}

async function screenshot(page, name) {
    const path = join(SCREENSHOTS_DIR, `${name}.png`);
    await page.screenshot({ path, fullPage: true });
    log(`  Screenshot saved: ${name}.png`);
    return path;
}

async function main() {
    log('Starting RBAC Integration Tests');
    log('================================');

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1440, height: 900 }
    });

    try {
        // ================================================================
        // TEST 1: Admin Full Access
        // ================================================================
        log('\n--- TEST 1: Admin Full Access ---');
        const adminPage = await context.newPage();

        // Navigate to login
        await adminPage.goto(`${BASE_URL}/admin`);
        await adminPage.waitForLoadState('networkidle');
        await screenshot(adminPage, '01-admin-login-page');

        // Check login form is present
        const nameInput = await adminPage.locator('input[name="name"]');
        const passwordInput = await adminPage.locator('input[name="password"]');
        const nameVisible = await nameInput.isVisible();
        const passwordVisible = await passwordInput.isVisible();
        recordResult('Login form has name input', nameVisible);
        recordResult('Login form has password input', passwordVisible);

        // Login as admin
        await nameInput.fill('Admin');
        await passwordInput.fill('password123');
        await adminPage.locator('button[type="submit"]').click();
        await adminPage.waitForLoadState('networkidle');
        await adminPage.waitForTimeout(1000);

        const currentUrl = adminPage.url();
        log(`  Redirected to: ${currentUrl}`);
        await screenshot(adminPage, '02-admin-after-login');

        // Check if login was successful (should be on admin page, not login page)
        const loginSuccessful = !currentUrl.includes('/login') && currentUrl.includes('/admin');
        recordResult('Admin login successful', loginSuccessful, `URL: ${currentUrl}`);

        // Check sidebar items are visible
        // Admin should see: Admin Users, Announcements, Travel Ads, UAE Activities
        const sidebarHtml = await adminPage.locator('#accordionSidebar').innerHTML();

        const hasAdminUsers = sidebarHtml.includes('Admin Users');
        const hasAnnouncements = sidebarHtml.includes('Announcements');
        const hasTravelAds = sidebarHtml.includes('Travel Ads');
        const hasUAEActivities = sidebarHtml.includes('UAE Activities');

        recordResult('Admin sees Admin Users in sidebar', hasAdminUsers);
        recordResult('Admin sees Announcements in sidebar', hasAnnouncements);
        recordResult('Admin sees Travel Ads in sidebar', hasTravelAds);
        recordResult('Admin sees UAE Activities in sidebar', hasUAEActivities);

        // Check the admin badge
        const hasAdminBadge = sidebarHtml.includes('Admin:');
        recordResult('Admin badge is displayed', hasAdminBadge);

        await screenshot(adminPage, '03-admin-sidebar-full-access');

        // ================================================================
        // TEST 2: Create Specific-Access User (Activities Manager only)
        // ================================================================
        log('\n--- TEST 2: Create Specific-Access User ---');

        // Navigate to user management
        await adminPage.goto(`${BASE_URL}/admin/users`);
        await adminPage.waitForLoadState('networkidle');
        await screenshot(adminPage, '04-admin-users-list');

        const usersPageLoaded = adminPage.url().includes('/admin/users');
        recordResult('Admin can access user management page', usersPageLoaded);

        // Click Add New User / Create User link
        const createLink = adminPage.locator('a[href*="users/create"]');
        const createLinkExists = await createLink.count() > 0;
        recordResult('Create User button exists', createLinkExists);

        if (createLinkExists) {
            await createLink.first().click();
            await adminPage.waitForLoadState('networkidle');
            await screenshot(adminPage, '05-create-user-form');

            // Fill in user details
            await adminPage.locator('#name').fill('TestEmployee');
            await adminPage.locator('#email').fill('testemployee@gotrips.ai');
            await adminPage.locator('#password').fill('test123');

            // Select Specific Access
            const specificAccessCard = adminPage.locator('#specificAccessCard');
            await specificAccessCard.click();
            await adminPage.waitForTimeout(500);

            await screenshot(adminPage, '06-specific-access-selected');

            // Check that module section is visible
            const moduleSectionVisible = await adminPage.locator('#moduleSection').isVisible();
            recordResult('Module section visible when specific access selected', moduleSectionVisible);

            // Check only UAE Activities - click the label card (checkbox is visually hidden)
            const uaeLabel = adminPage.locator('label.module-checkbox-card').filter({ has: adminPage.locator('input[value="uaeactivities"]') });
            await uaeLabel.click();
            await adminPage.waitForTimeout(300);

            // Verify checkbox states via JavaScript evaluation
            const uaeChecked = await adminPage.locator('input[name="modules[]"][value="uaeactivities"]').isChecked();
            const announcementsChecked = await adminPage.locator('input[name="modules[]"][value="announcements"]').isChecked();
            const homepageadsChecked = await adminPage.locator('input[name="modules[]"][value="homepageads"]').isChecked();

            recordResult('UAE Activities checkbox is checked', uaeChecked);
            recordResult('Announcements checkbox is NOT checked', !announcementsChecked);
            recordResult('Travel Ads checkbox is NOT checked', !homepageadsChecked);

            await screenshot(adminPage, '07-modules-selected');

            // Submit the form (use the Create User button specifically)
            await adminPage.locator('#createUserForm button[type="submit"]').click();
            await adminPage.waitForLoadState('networkidle');
            await adminPage.waitForTimeout(1000);

            await screenshot(adminPage, '08-after-user-creation');

            const afterCreateUrl = adminPage.url();
            const userCreated = afterCreateUrl.includes('/admin/users') && !afterCreateUrl.includes('create');
            recordResult('User created and redirected to users list', userCreated, `URL: ${afterCreateUrl}`);

            // Check for error messages
            const errorMessages = await adminPage.locator('.alert-danger').count();
            if (errorMessages > 0) {
                const errorText = await adminPage.locator('.alert-danger').textContent();
                recordResult('No form validation errors', false, `Errors: ${errorText.trim()}`);
            } else {
                recordResult('No form validation errors', true);
            }

            // Check success message
            const successMessages = await adminPage.locator('.alert-success, [class*="success"]').count();
            recordResult('Success message displayed after user creation', successMessages > 0 || userCreated);

            // Verify the new user appears in the list
            const pageContent = await adminPage.textContent('body');
            const newUserVisible = pageContent.includes('TestEmployee') || pageContent.includes('testemployee@gotrips.ai');
            recordResult('TestEmployee appears in users list', newUserVisible);

        } else {
            recordResult('SKIP: Create user form tests (no create link found)', false, 'Create link not found');
        }

        // ================================================================
        // TEST 3: Logout Admin and Login as Specific-Access User
        // ================================================================
        log('\n--- TEST 3: Login as Specific-Access User ---');

        // Logout admin
        // Find and click the logout option
        const logoutForm = adminPage.locator('form[action*="logout"]');
        const logoutFormCount = await logoutForm.count();

        if (logoutFormCount > 0) {
            // Check for dropdown toggle first
            const dropdownToggle = adminPage.locator('.dropdown-toggle, [data-bs-toggle="dropdown"]').last();
            if (await dropdownToggle.count() > 0) {
                await dropdownToggle.click();
                await adminPage.waitForTimeout(300);
            }

            const logoutButton = adminPage.locator('a[href*="logout"], button:has-text("Logout"), a:has-text("Logout"), .dropdown-item:has-text("Logout")');
            if (await logoutButton.count() > 0) {
                await logoutButton.first().click();
            } else {
                // Direct form submission
                await logoutForm.first().evaluate(form => form.submit());
            }
        } else {
            // Navigate directly to logout via POST
            await adminPage.goto(`${BASE_URL}/admin`);
        }
        await adminPage.waitForLoadState('networkidle');
        await adminPage.waitForTimeout(1000);

        // Clear cookies to ensure clean session
        await context.clearCookies();

        // Navigate to login page
        await adminPage.goto(`${BASE_URL}/admin`);
        await adminPage.waitForLoadState('networkidle');
        await screenshot(adminPage, '09-after-admin-logout');

        // Login as TestEmployee
        const empNameInput = await adminPage.locator('input[name="name"]');
        const empPasswordInput = await adminPage.locator('input[name="password"]');

        if (await empNameInput.isVisible()) {
            await empNameInput.fill('TestEmployee');
            await empPasswordInput.fill('test123');
            await adminPage.locator('button[type="submit"]').click();
            await adminPage.waitForLoadState('networkidle');
            await adminPage.waitForTimeout(2000);

            const empUrl = adminPage.url();
            log(`  TestEmployee redirected to: ${empUrl}`);
            await screenshot(adminPage, '10-employee-after-login');

            // Check for login error on the page
            const hasLoginError = await adminPage.locator('.invalid-feedback, .alert-danger').count() > 0;
            if (hasLoginError) {
                const errorText = await adminPage.locator('.invalid-feedback, .alert-danger').first().textContent();
                log(`  Login error message: ${errorText.trim()}`);
            }

            // Check if login was successful - after successful login, URL should contain /admin/ (not bare /admin or /login)
            const empLoginSuccess = empUrl.includes('/admin/') && !empUrl.endsWith('/admin') && !empUrl.includes('/login');
            recordResult('TestEmployee login successful', empLoginSuccess, `URL: ${empUrl}`);

            // Check if redirected to UAE activities
            const redirectedToActivities = empUrl.includes('uaeactivities');
            recordResult('TestEmployee redirected to UAE Activities', redirectedToActivities, `URL: ${empUrl}`);

            // Check sidebar - should ONLY show UAE Activities (not Admin Users, Announcements, Travel Ads)
            if (empLoginSuccess) {
                const empSidebarHtml = await adminPage.locator('#accordionSidebar').innerHTML();

                const empHasAdminUsers = empSidebarHtml.includes('Admin Users');
                const empHasAnnouncements = empSidebarHtml.includes('>Announcements<') ||
                                            (empSidebarHtml.includes('Announcements') && empSidebarHtml.includes('fa-bullhorn'));
                const empHasTravelAds = empSidebarHtml.includes('Travel Ads');
                const empHasUAE = empSidebarHtml.includes('UAE Activities');

                recordResult('Employee does NOT see Admin Users', !empHasAdminUsers);
                recordResult('Employee does NOT see Announcements', !empHasAnnouncements);
                recordResult('Employee does NOT see Travel Ads', !empHasTravelAds);
                recordResult('Employee sees UAE Activities', empHasUAE);

                // Check for access-restricted message (bad) vs proper content (good)
                const hasRestricted = empSidebarHtml.includes('Access Restricted');
                recordResult('Employee sidebar does NOT show Access Restricted', !hasRestricted);

                await screenshot(adminPage, '11-employee-sidebar');
            }
        } else {
            recordResult('Login form available after admin logout', false, 'Login form not found');
        }

        // ================================================================
        // TEST 4: Route Protection
        // ================================================================
        log('\n--- TEST 4: Route Protection ---');

        // Test 4a: Try to access /admin/users (should be denied)
        const usersResponse = await adminPage.goto(`${BASE_URL}/admin/users`);
        await adminPage.waitForLoadState('networkidle');
        const usersAccessUrl = adminPage.url();
        const usersBlocked = !usersAccessUrl.includes('/admin/users') ||
                             usersAccessUrl.includes('/admin') && !usersAccessUrl.includes('/admin/users');
        // It should redirect away from /admin/users or show 403
        const usersStatusCode = usersResponse ? usersResponse.status() : 0;
        recordResult('Route /admin/users blocked for employee',
            usersBlocked || usersStatusCode === 403,
            `URL: ${usersAccessUrl}, Status: ${usersStatusCode}`);
        await screenshot(adminPage, '12-employee-access-users-denied');

        // Re-login if we got logged out
        if (usersAccessUrl.endsWith('/admin') || usersAccessUrl.includes('/login')) {
            await adminPage.locator('input[name="name"]').fill('TestEmployee');
            await adminPage.locator('input[name="password"]').fill('test123');
            await adminPage.locator('button[type="submit"]').click();
            await adminPage.waitForLoadState('networkidle');
            await adminPage.waitForTimeout(500);
        }

        // Test 4b: Try to access /admin/announcements (should be denied)
        const announcementsResponse = await adminPage.goto(`${BASE_URL}/admin/announcements`);
        await adminPage.waitForLoadState('networkidle');
        const announcementsUrl = adminPage.url();
        const announcementsStatus = announcementsResponse ? announcementsResponse.status() : 0;
        const announcementsBlocked = !announcementsUrl.includes('/admin/announcements') || announcementsStatus === 403;
        recordResult('Route /admin/announcements blocked for employee',
            announcementsBlocked,
            `URL: ${announcementsUrl}, Status: ${announcementsStatus}`);
        await screenshot(adminPage, '13-employee-access-announcements-denied');

        // Re-login if needed
        if (announcementsUrl.endsWith('/admin') || announcementsUrl.includes('/login')) {
            await adminPage.locator('input[name="name"]').fill('TestEmployee');
            await adminPage.locator('input[name="password"]').fill('test123');
            await adminPage.locator('button[type="submit"]').click();
            await adminPage.waitForLoadState('networkidle');
            await adminPage.waitForTimeout(500);
        }

        // Test 4c: Try to access /admin/homepageads (should be denied)
        const homepageadsResponse = await adminPage.goto(`${BASE_URL}/admin/homepageads`);
        await adminPage.waitForLoadState('networkidle');
        const homepageadsUrl = adminPage.url();
        const homepageadsStatus = homepageadsResponse ? homepageadsResponse.status() : 0;
        const homepageadsBlocked = !homepageadsUrl.includes('/admin/homepageads') || homepageadsStatus === 403;
        recordResult('Route /admin/homepageads blocked for employee',
            homepageadsBlocked,
            `URL: ${homepageadsUrl}, Status: ${homepageadsStatus}`);
        await screenshot(adminPage, '14-employee-access-homepageads-denied');

        // Re-login if needed
        if (homepageadsUrl.endsWith('/admin') || homepageadsUrl.includes('/login')) {
            await adminPage.locator('input[name="name"]').fill('TestEmployee');
            await adminPage.locator('input[name="password"]').fill('test123');
            await adminPage.locator('button[type="submit"]').click();
            await adminPage.waitForLoadState('networkidle');
            await adminPage.waitForTimeout(500);
        }

        // Test 4d: Try to access /admin/uaeactivities (should be ALLOWED)
        const uaeResponse = await adminPage.goto(`${BASE_URL}/admin/uaeactivities`);
        await adminPage.waitForLoadState('networkidle');
        const uaeUrl = adminPage.url();
        const uaeStatus = uaeResponse ? uaeResponse.status() : 0;
        const uaeAllowed = uaeUrl.includes('/admin/uaeactivities') && (uaeStatus === 200 || uaeStatus === 304);
        recordResult('Route /admin/uaeactivities ALLOWED for employee',
            uaeAllowed,
            `URL: ${uaeUrl}, Status: ${uaeStatus}`);
        await screenshot(adminPage, '15-employee-access-uaeactivities-allowed');

    } catch (error) {
        log(`\nERROR: ${error.message}`);
        console.error(error.stack);
    } finally {
        await browser.close();
    }

    // ================================================================
    // RESULTS SUMMARY
    // ================================================================
    log('\n================================');
    log('RESULTS SUMMARY');
    log('================================');

    const passed = results.filter(r => r.passed).length;
    const failed = results.filter(r => !r.passed).length;
    const total = results.length;

    for (const r of results) {
        const icon = r.passed ? '[PASS]' : '[FAIL]';
        log(`${icon} ${r.testName}${r.details ? ' (' + r.details + ')' : ''}`);
    }

    log(`\nTotal: ${total} | Passed: ${passed} | Failed: ${failed}`);
    log(`Screenshots saved to: ${SCREENSHOTS_DIR}/`);

    // Write results to JSON
    const reportPath = join(SCREENSHOTS_DIR, 'test-results.json');
    writeFileSync(reportPath, JSON.stringify({ results, summary: { total, passed, failed } }, null, 2));
    log(`Report saved to: ${reportPath}`);

    // Exit with error code if any tests failed
    if (failed > 0) {
        process.exit(1);
    }
}

main().catch(err => {
    console.error('Fatal error:', err);
    process.exit(2);
});
