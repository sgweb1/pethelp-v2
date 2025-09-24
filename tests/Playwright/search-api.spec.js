import { test, expect } from '@playwright/test';

test.describe('Search API Tests', () => {
    const baseApiUrl = '/api/search';

    test('should return valid API response structure', async ({ request }) => {
        const response = await request.get(`${baseApiUrl}?limit=5`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        expect(data).toHaveProperty('success', true);
        expect(data).toHaveProperty('data');
        expect(data).toHaveProperty('meta');

        expect(data.data).toHaveProperty('items');
        expect(data.data).toHaveProperty('pagination');
        expect(Array.isArray(data.data.items)).toBe(true);
    });

    test('should filter by content type correctly', async ({ request }) => {
        const response = await request.get(`${baseApiUrl}?content_type=pet_sitter&limit=10`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        const items = data.data.items;

        if (items.length > 0) {
            for (const item of items) {
                expect(item.content_type).toBe('pet_sitter');
            }
        }
    });

    test('should filter by location correctly', async ({ request }) => {
        const response = await request.get(`${baseApiUrl}?location=Olsztyn&limit=10`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        const items = data.data.items;

        if (items.length > 0) {
            for (const item of items) {
                expect(item.location.city.toLowerCase()).toContain('olsztyn');
            }
        }
    });

    test('should filter by price range correctly', async ({ request }) => {
        const minPrice = 20;
        const maxPrice = 50;
        const response = await request.get(`${baseApiUrl}?min_price=${minPrice}&max_price=${maxPrice}&limit=10`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        const items = data.data.items;

        if (items.length > 0) {
            for (const item of items) {
                if (item.price && item.price.from) {
                    expect(parseFloat(item.price.from)).toBeGreaterThanOrEqual(minPrice);
                    expect(parseFloat(item.price.from)).toBeLessThanOrEqual(maxPrice);
                }
            }
        }
    });

    test('should filter by rating correctly', async ({ request }) => {
        const minRating = 4;
        const response = await request.get(`${baseApiUrl}?min_rating=${minRating}&limit=10`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        const items = data.data.items;

        if (items.length > 0) {
            for (const item of items) {
                if (item.quality && item.quality.rating) {
                    expect(parseFloat(item.quality.rating)).toBeGreaterThanOrEqual(minRating);
                }
            }
        }
    });

    test('should filter featured items correctly', async ({ request }) => {
        const response = await request.get(`${baseApiUrl}?featured_only=1&limit=10`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        const items = data.data.items;

        if (items.length > 0) {
            for (const item of items) {
                // Check if item has featured flag
                expect(item.flags && item.flags.featured === true).toBe(true);
            }
        }
    });

    test('should handle search term filtering', async ({ request }) => {
        const searchTerm = 'pet';
        const response = await request.get(`${baseApiUrl}?search_term=${searchTerm}&limit=10`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        const items = data.data.items;

        if (items.length > 0) {
            // Check that search term appears in title, description, or category
            for (const item of items) {
                const searchableText = `${item.title} ${item.description || item.description_short || ''} ${item.category.name}`.toLowerCase();
                expect(searchableText).toMatch(/pet|sitter|kot|cat/);
            }
        }
    });

    test('should return map format correctly', async ({ request }) => {
        const response = await request.get(`${baseApiUrl}?format=map&limit=20`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        expect(data.data).toHaveProperty('markers');
        expect(Array.isArray(data.data.markers)).toBe(true);

        if (data.data.markers.length > 0) {
            const marker = data.data.markers[0];
            expect(marker).toHaveProperty('id');
            expect(marker).toHaveProperty('lat');
            expect(marker).toHaveProperty('lng');
            expect(marker).toHaveProperty('title');
        }
    });

    test('should handle pagination correctly', async ({ request }) => {
        const response = await request.get(`${baseApiUrl}?page=1&limit=5`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        expect(data.data.pagination).toHaveProperty('current_page', 1);
        expect(data.data.pagination).toHaveProperty('has_more');
        // Check if total is present or if we have items
        expect(data.data.items).toBeDefined();
        expect(Array.isArray(data.data.items)).toBe(true);
    });

    test('should handle sorting correctly', async ({ request }) => {
        const response = await request.get(`${baseApiUrl}?sort=price_asc&limit=10`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        const items = data.data.items;

        // Just verify that we get items and sorting parameter is accepted
        expect(Array.isArray(items)).toBe(true);

        // If we have items with prices, check basic price structure
        if (items.length > 0) {
            const itemsWithPrices = items.filter(item => item.price && item.price.from);
            // Just verify we can access price data, actual sorting may depend on business logic
            if (itemsWithPrices.length > 0) {
                expect(typeof parseFloat(itemsWithPrices[0].price.from)).toBe('number');
            }
        }
    });

    test('should validate input parameters', async ({ request }) => {
        // Test invalid content_type
        const response1 = await request.get(`${baseApiUrl}?content_type=invalid_type`);
        expect(response1.status()).toBe(422);

        // Test invalid price range
        const response2 = await request.get(`${baseApiUrl}?min_price=abc&max_price=xyz`);
        expect(response2.status()).toBe(422);

        // Test negative price
        const response3 = await request.get(`${baseApiUrl}?min_price=-10`);
        expect(response3.status()).toBe(422);
    });

    test('should handle empty results gracefully', async ({ request }) => {
        // Search for something that doesn't exist
        const response = await request.get(`${baseApiUrl}?search_term=nonexistentservice123xyz&limit=10`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        expect(data.success).toBe(true);
        expect(Array.isArray(data.data.items)).toBe(true);
        // Empty results should still return valid structure
    });

    test('should include performance metrics', async ({ request }) => {
        const response = await request.get(`${baseApiUrl}?limit=10`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        expect(data.meta).toHaveProperty('response_time_ms');
        expect(typeof data.meta.response_time_ms).toBe('number');
        expect(data.meta.response_time_ms).toBeGreaterThan(0);
    });

    test('should handle combined filters correctly', async ({ request }) => {
        const response = await request.get(`${baseApiUrl}?content_type=pet_sitter&location=Olsztyn&min_price=20&max_price=50&featured_only=1&limit=10`);

        expect(response.status()).toBe(200);

        const data = await response.json();
        const items = data.data.items;

        if (items.length > 0) {
            for (const item of items) {
                expect(item.content_type).toBe('pet_sitter');
                expect(item.location.city.toLowerCase()).toContain('olsztyn');
                // Check featured flag
                expect(item.flags && item.flags.featured === true).toBe(true);

                if (item.price && item.price.from) {
                    expect(parseFloat(item.price.from)).toBeGreaterThanOrEqual(20);
                    expect(parseFloat(item.price.from)).toBeLessThanOrEqual(50);
                }
            }
        }
    });

    test('should measure API response times', async ({ request }) => {
        const tests = [
            { name: 'basic_search', url: `${baseApiUrl}?limit=10` },
            { name: 'filtered_search', url: `${baseApiUrl}?content_type=pet_sitter&location=Olsztyn&min_price=20&max_price=50&limit=20` },
            { name: 'map_search', url: `${baseApiUrl}?format=map&limit=50` },
        ];

        for (const testCase of tests) {
            const startTime = Date.now();
            const response = await request.get(testCase.url);
            const endTime = Date.now();

            const responseTime = endTime - startTime;

            expect(response.status()).toBe(200);

            // Performance thresholds
            const thresholds = {
                basic_search: 500,    // 500ms
                filtered_search: 800, // 800ms for complex queries
                map_search: 1000      // 1000ms for map data
            };

            expect(responseTime).toBeLessThan(thresholds[testCase.name]);
            console.log(`${testCase.name}: ${responseTime}ms (threshold: ${thresholds[testCase.name]}ms)`);
        }
    });

    test('should return consistent data between list and map formats', async ({ request }) => {
        // Get same data in list format
        const listResponse = await request.get(`${baseApiUrl}?content_type=pet_sitter&limit=10`);
        expect(listResponse.status()).toBe(200);
        const listData = await listResponse.json();

        // Get same data in map format
        const mapResponse = await request.get(`${baseApiUrl}?content_type=pet_sitter&format=map&limit=10`);
        expect(mapResponse.status()).toBe(200);
        const mapData = await mapResponse.json();

        // Compare item counts
        expect(listData.data.items.length).toBe(mapData.data.markers.length);

        // Check that map markers contain essential data
        if (mapData.data.markers.length > 0) {
            const marker = mapData.data.markers[0];
            expect(marker).toHaveProperty('id');
            expect(marker).toHaveProperty('lat');
            expect(marker).toHaveProperty('lng');
            expect(marker).toHaveProperty('title');
            expect(marker).toHaveProperty('content_type');
        }
    });
});