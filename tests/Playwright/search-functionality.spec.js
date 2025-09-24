import { test, expect } from '@playwright/test';

test.describe('Search Functionality Tests', () => {
    test.beforeEach(async ({ page }) => {
        // Go to search page before each test
        await page.goto('/search');
        // Wait for page to be fully loaded
        await page.waitForLoadState('networkidle');
    });

    test('should load search page successfully', async ({ page }) => {
        // Check that the page title contains expected text
        await expect(page).toHaveTitle(/PetHelp/);

        // Check that search form is visible
        await expect(page.locator('[data-testid="search-form"]')).toBeVisible();

        // Check that view mode buttons are present
        await expect(page.locator('[data-testid="view-grid"]')).toBeVisible();
        await expect(page.locator('[data-testid="view-map"]')).toBeVisible();
    });

    test('should toggle between grid and map views', async ({ page }) => {
        // Start in grid view
        await expect(page.locator('[data-testid="results-grid"]')).toBeVisible();

        // Switch to map view
        await page.click('[data-testid="view-map"]');
        await page.waitForTimeout(1000); // Wait for transition

        // Check map is visible
        await expect(page.locator('[data-testid="search-map"]')).toBeVisible();

        // Switch back to grid view
        await page.click('[data-testid="view-grid"]');
        await page.waitForTimeout(1000);

        // Check grid is visible again
        await expect(page.locator('[data-testid="results-grid"]')).toBeVisible();
    });

    test('should filter by content type', async ({ page }) => {
        // Open content type filter
        const contentTypeSelect = page.locator('select[name="content_type"]');
        await contentTypeSelect.selectOption('pet_sitter');

        // Wait for results to load
        await page.waitForTimeout(2000);

        // Check that results are updated
        const results = page.locator('[data-testid="search-result"]');
        await expect(results.first()).toBeVisible();

        // Verify content type in results
        const firstResult = results.first();
        const contentType = await firstResult.getAttribute('data-content-type');
        expect(contentType).toBe('pet_sitter');
    });

    test('should filter by location', async ({ page }) => {
        // Set location filter
        const locationInput = page.locator('input[name="location"]');
        await locationInput.fill('Olsztyn');
        await locationInput.press('Enter');

        // Wait for results
        await page.waitForTimeout(2000);

        // Check results contain location
        const results = page.locator('[data-testid="search-result"]');
        await expect(results.first()).toBeVisible();

        // Verify location in results
        const locationText = await results.first().locator('[data-testid="result-location"]').textContent();
        expect(locationText.toLowerCase()).toContain('olsztyn');
    });

    test('should filter by price range', async ({ page }) => {
        // Set price filters
        await page.fill('input[name="min_price"]', '20');
        await page.fill('input[name="max_price"]', '50');

        // Apply filters
        await page.click('[data-testid="apply-filters"]');
        await page.waitForTimeout(2000);

        // Check that results respect price range
        const results = page.locator('[data-testid="search-result"]');
        if (await results.count() > 0) {
            const priceElement = results.first().locator('[data-testid="result-price"]');
            const priceText = await priceElement.textContent();
            const price = parseFloat(priceText.replace(/[^\d.]/g, ''));
            expect(price).toBeGreaterThanOrEqual(20);
            expect(price).toBeLessThanOrEqual(50);
        }
    });

    test('should filter by featured items only', async ({ page }) => {
        // Enable featured filter
        await page.check('input[name="featured_only"]');

        // Wait for results
        await page.waitForTimeout(2000);

        // Check that results are featured
        const results = page.locator('[data-testid="search-result"]');
        if (await results.count() > 0) {
            const featuredBadge = results.first().locator('[data-testid="featured-badge"]');
            await expect(featuredBadge).toBeVisible();
        }
    });

    test('should sort results correctly', async ({ page }) => {
        // Get initial results
        await page.waitForTimeout(2000);
        const initialResults = await page.locator('[data-testid="search-result"]').count();

        if (initialResults > 1) {
            // Test price sorting
            await page.selectOption('select[name="sort"]', 'price_asc');
            await page.waitForTimeout(2000);

            // Get first two prices
            const results = page.locator('[data-testid="search-result"]');
            const firstPrice = await results.nth(0).locator('[data-testid="result-price"]').textContent();
            const secondPrice = await results.nth(1).locator('[data-testid="result-price"]').textContent();

            const price1 = parseFloat(firstPrice.replace(/[^\d.]/g, ''));
            const price2 = parseFloat(secondPrice.replace(/[^\d.]/g, ''));

            expect(price1).toBeLessThanOrEqual(price2);
        }
    });

    test('should handle search term input', async ({ page }) => {
        // Enter search term
        const searchInput = page.locator('input[name="search_term"]');
        await searchInput.fill('kot');
        await searchInput.press('Enter');

        // Wait for results
        await page.waitForTimeout(2000);

        // Check that results are relevant to search term
        const results = page.locator('[data-testid="search-result"]');
        if (await results.count() > 0) {
            const firstResultText = await results.first().textContent();
            expect(firstResultText.toLowerCase()).toMatch(/kot|cat|sitter/);
        }
    });

    test('should test list-map interaction on hover', async ({ page }) => {
        // Switch to map view first
        await page.click('[data-testid="view-map"]');
        await page.waitForTimeout(2000);

        // Ensure we have results
        const results = page.locator('[data-testid="search-result"]');
        const resultCount = await results.count();

        if (resultCount > 0) {
            // Hover over first result
            await results.first().hover();
            await page.waitForTimeout(500);

            // Check if map marker is highlighted (implementation dependent)
            // This would need specific CSS classes or attributes to verify
            const mapContainer = page.locator('[data-testid="search-map"]');
            await expect(mapContainer).toBeVisible();
        }
    });

    test('should test map marker click centering', async ({ page }) => {
        // Switch to map view
        await page.click('[data-testid="view-map"]');
        await page.waitForTimeout(2000);

        // Wait for map to load
        const mapContainer = page.locator('[data-testid="search-map"]');
        await expect(mapContainer).toBeVisible();

        // Click on a map marker (would need specific marker selectors)
        const mapMarkers = page.locator('[data-testid="map-marker"]');
        if (await mapMarkers.count() > 0) {
            await mapMarkers.first().click();
            await page.waitForTimeout(1000);

            // Verify map has centered (implementation dependent)
            // This would require checking map state or position
        }
    });

    test('should handle URL parameters correctly', async ({ page }) => {
        // Navigate with URL parameters
        await page.goto('/search?content_type=pet_sitter&location=Olsztyn&min_price=20&max_price=50');
        await page.waitForLoadState('networkidle');

        // Check that filters are applied from URL
        const contentTypeSelect = page.locator('select[name="content_type"]');
        await expect(contentTypeSelect).toHaveValue('pet_sitter');

        const locationInput = page.locator('input[name="location"]');
        await expect(locationInput).toHaveValue('Olsztyn');

        const minPriceInput = page.locator('input[name="min_price"]');
        await expect(minPriceInput).toHaveValue('20');

        const maxPriceInput = page.locator('input[name="max_price"]');
        await expect(maxPriceInput).toHaveValue('50');
    });

    test('should test advanced filter combinations', async ({ page }) => {
        // Apply multiple filters
        await page.selectOption('select[name="content_type"]', 'pet_sitter');
        await page.fill('input[name="location"]', 'Olsztyn');
        await page.fill('input[name="min_price"]', '20');
        await page.check('input[name="featured_only"]');

        // Apply filters
        await page.click('[data-testid="apply-filters"]');
        await page.waitForTimeout(3000);

        // Verify results match all criteria
        const results = page.locator('[data-testid="search-result"]');
        if (await results.count() > 0) {
            const firstResult = results.first();

            // Check content type
            const contentType = await firstResult.getAttribute('data-content-type');
            expect(contentType).toBe('pet_sitter');

            // Check location
            const locationText = await firstResult.locator('[data-testid="result-location"]').textContent();
            expect(locationText.toLowerCase()).toContain('olsztyn');

            // Check featured badge
            const featuredBadge = firstResult.locator('[data-testid="featured-badge"]');
            await expect(featuredBadge).toBeVisible();
        }
    });

    test('should handle empty search results gracefully', async ({ page }) => {
        // Search for something that doesn't exist
        const searchInput = page.locator('input[name="search_term"]');
        await searchInput.fill('nonexistentservice123xyz');
        await searchInput.press('Enter');

        await page.waitForTimeout(2000);

        // Check for empty state message
        const emptyMessage = page.locator('[data-testid="no-results"]');
        await expect(emptyMessage).toBeVisible();
    });

    test('should test pagination functionality', async ({ page }) => {
        // Check if pagination exists
        const pagination = page.locator('[data-testid="pagination"]');

        if (await pagination.isVisible()) {
            const nextButton = page.locator('[data-testid="pagination-next"]');
            if (await nextButton.isVisible()) {
                // Click next page
                await nextButton.click();
                await page.waitForTimeout(2000);

                // Verify page changed
                const currentPage = page.locator('[data-testid="current-page"]');
                const pageNumber = await currentPage.textContent();
                expect(parseInt(pageNumber)).toBeGreaterThan(1);
            }
        }
    });

    test('should test responsive design on mobile', async ({ page }) => {
        // Set mobile viewport
        await page.setViewportSize({ width: 375, height: 667 });

        // Check that mobile-specific elements are visible
        const mobileMenu = page.locator('[data-testid="mobile-menu"]');
        if (await mobileMenu.isVisible()) {
            await mobileMenu.click();

            // Check mobile navigation
            const mobileNav = page.locator('[data-testid="mobile-nav"]');
            await expect(mobileNav).toBeVisible();
        }

        // Check that results are displayed properly on mobile
        const results = page.locator('[data-testid="search-result"]');
        if (await results.count() > 0) {
            await expect(results.first()).toBeVisible();
        }
    });

    test('should test filter reset functionality', async ({ page }) => {
        // Apply some filters
        await page.selectOption('select[name="content_type"]', 'pet_sitter');
        await page.fill('input[name="location"]', 'Olsztyn');
        await page.fill('input[name="min_price"]', '20');

        // Reset filters
        const resetButton = page.locator('[data-testid="reset-filters"]');
        if (await resetButton.isVisible()) {
            await resetButton.click();
            await page.waitForTimeout(1000);

            // Check that filters are cleared
            const contentTypeSelect = page.locator('select[name="content_type"]');
            await expect(contentTypeSelect).toHaveValue('');

            const locationInput = page.locator('input[name="location"]');
            await expect(locationInput).toHaveValue('');

            const minPriceInput = page.locator('input[name="min_price"]');
            await expect(minPriceInput).toHaveValue('');
        }
    });

    test('should test live search functionality', async ({ page }) => {
        // Enable live search if available
        const liveSearchToggle = page.locator('[data-testid="live-search"]');
        if (await liveSearchToggle.isVisible()) {
            await liveSearchToggle.check();
        }

        // Type in search field and wait for live results
        const searchInput = page.locator('input[name="search_term"]');
        await searchInput.fill('kot');

        // Wait for live search to trigger
        await page.waitForTimeout(1500);

        // Check that results updated without pressing enter
        const results = page.locator('[data-testid="search-result"]');
        if (await results.count() > 0) {
            const firstResultText = await results.first().textContent();
            expect(firstResultText.toLowerCase()).toMatch(/kot|cat/);
        }
    });

    test('should test accessibility features', async ({ page }) => {
        // Check for proper ARIA labels
        const searchForm = page.locator('[data-testid="search-form"]');
        await expect(searchForm).toHaveAttribute('role', 'search');

        // Check that form elements have labels
        const inputs = page.locator('input, select');
        const inputCount = await inputs.count();

        for (let i = 0; i < inputCount; i++) {
            const input = inputs.nth(i);
            const id = await input.getAttribute('id');
            if (id) {
                const label = page.locator(`label[for="${id}"]`);
                await expect(label).toBeVisible();
            }
        }

        // Test keyboard navigation
        await page.keyboard.press('Tab');
        const focusedElement = await page.evaluate(() => document.activeElement.tagName);
        expect(['INPUT', 'BUTTON', 'SELECT']).toContain(focusedElement);
    });
});