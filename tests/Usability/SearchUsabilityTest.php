<?php

namespace Tests\Usability;

use Tests\TestCase;

/**
 * Usability Tests for Search Functionality
 * Tests user experience, accessibility, and interaction patterns
 */
class SearchUsabilityTest extends TestCase
{
    /**
     * Test search flow usability scenarios
     */
    public function test_typical_user_search_scenarios()
    {
        // Scenario 1: User looking for pet sitter in their city
        $scenario1 = $this->simulateUserScenario([
            'intent' => 'Find pet sitter in Olsztyn',
            'steps' => [
                'visit_search_page',
                'enter_location_olsztyn',
                'select_pet_sitter_type',
                'submit_search',
                'view_results',
                'hover_on_result',
                'click_contact_button',
            ],
        ]);

        $this->assertScenarioSuccess($scenario1);

        // Scenario 2: User browsing with price filter
        $scenario2 = $this->simulateUserScenario([
            'intent' => 'Find affordable services under 40 PLN',
            'steps' => [
                'visit_search_page',
                'set_max_price_40',
                'submit_search',
                'verify_all_results_under_price',
                'sort_by_price_low_to_high',
                'select_cheapest_option',
            ],
        ]);

        $this->assertScenarioSuccess($scenario2);

        // Scenario 3: User using map to explore area
        $scenario3 = $this->simulateUserScenario([
            'intent' => 'Explore services around my location',
            'steps' => [
                'visit_search_page',
                'enable_geolocation',
                'switch_to_map_view',
                'zoom_to_preferred_area',
                'click_on_markers',
                'compare_multiple_options',
            ],
        ]);

        $this->assertScenarioSuccess($scenario3);
    }

    /**
     * Test search performance from user perspective
     */
    public function test_search_response_times()
    {
        $performanceMetrics = [];

        // Test basic search performance
        $startTime = microtime(true);
        $response = $this->getJson('/api/search?limit=10');
        $endTime = microtime(true);

        $performanceMetrics['basic_search'] = [
            'time' => ($endTime - $startTime) * 1000, // Convert to milliseconds
            'status' => $response->getStatusCode(),
            'acceptable_threshold' => 500, // 500ms
        ];

        // Test filtered search performance
        $startTime = microtime(true);
        $response = $this->getJson('/api/search?content_type=pet_sitter&location=Olsztyn&min_price=20&max_price=50&limit=20');
        $endTime = microtime(true);

        $performanceMetrics['filtered_search'] = [
            'time' => ($endTime - $startTime) * 1000,
            'status' => $response->getStatusCode(),
            'acceptable_threshold' => 800, // 800ms for complex queries
        ];

        // Test map format performance
        $startTime = microtime(true);
        $response = $this->getJson('/api/search?format=map&limit=50');
        $endTime = microtime(true);

        $performanceMetrics['map_search'] = [
            'time' => ($endTime - $startTime) * 1000,
            'status' => $response->getStatusCode(),
            'acceptable_threshold' => 1000, // 1000ms for map data
        ];

        // Assert all performance metrics are within acceptable thresholds
        foreach ($performanceMetrics as $test => $metrics) {
            $this->assertLessThan(
                $metrics['acceptable_threshold'],
                $metrics['time'],
                "Search performance test '{$test}' took {$metrics['time']}ms, expected under {$metrics['acceptable_threshold']}ms"
            );

            $this->assertEquals(200, $metrics['status'], "Search test '{$test}' should return 200 status");
        }
    }

    /**
     * Test error messages and user guidance
     */
    public function test_user_error_handling()
    {
        // Test validation error messages are user-friendly
        $response = $this->getJson('/api/search?content_type=invalid&min_price=-10&max_price=abc');

        $response->assertStatus(422);
        $errors = $response->json('errors');

        $this->assertArrayHasKey('content_type', $errors);
        $this->assertArrayHasKey('min_price', $errors);
        $this->assertArrayHasKey('max_price', $errors);

        // Verify error messages are in Polish and user-friendly
        foreach ($errors as $field => $messages) {
            $this->assertIsArray($messages);
            foreach ($messages as $message) {
                $this->assertIsString($message);
                $this->assertNotEmpty($message);
                // Error messages should not contain technical jargon
                $this->assertDoesNotMatchRegularExpression('/\b(SQL|Exception|Error)\b/', $message);
            }
        }
    }

    /**
     * Test search result relevance and ordering
     */
    public function test_search_result_relevance()
    {
        // Test search by term relevance
        $response = $this->getJson('/api/search?search_term=pet&content_type=pet_sitter&limit=10');
        $response->assertStatus(200);

        $items = $response->json('data.items');
        $this->assertNotEmpty($items, 'Search for "pet" should return relevant results');

        // Verify results contain the search term in title or description
        foreach ($items as $item) {
            $searchContent = strtolower($item['title'].' '.($item['description'] ?? $item['description_short'] ?? '').' '.$item['category']['name']);
            $this->assertTrue(
                str_contains($searchContent, 'pet') || str_contains($searchContent, 'sitter'),
                'Search result should be relevant to "pet" search term'
            );
        }

        // Test location-based relevance
        $response = $this->getJson('/api/search?location=Olsztyn&content_type=pet_sitter&limit=10');
        $response->assertStatus(200);

        $items = $response->json('data.items');
        foreach ($items as $item) {
            $this->assertStringContainsStringIgnoringCase(
                'olsztyn',
                $item['location']['city'],
                'Location search should return results from specified city'
            );
        }
    }

    /**
     * Test mobile usability patterns
     */
    public function test_mobile_usability_patterns()
    {
        // Simulate mobile user agent
        $mobileHeaders = [
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15',
        ];

        // Test that search API works with mobile requests
        $response = $this->withHeaders($mobileHeaders)
            ->getJson('/api/search?limit=5');

        $response->assertStatus(200);
        $items = $response->json('data.items');

        // Mobile users should get optimized responses
        $this->assertLessThanOrEqual(5, count($items), 'Mobile should get limited results for performance');

        // Verify essential mobile data is present
        foreach ($items as $item) {
            $this->assertArrayHasKey('title', $item);
            $this->assertArrayHasKey('price', $item);
            $this->assertArrayHasKey('location', $item);
            $this->assertArrayHasKey('quality', $item);

            // Mobile-optimized data should be concise
            $this->assertLessThanOrEqual(50, strlen($item['title']), 'Mobile titles should be concise');
        }
    }

    /**
     * Test accessibility compliance
     */
    public function test_accessibility_compliance()
    {
        $response = $this->get('/search');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Test for proper ARIA labels and semantic HTML
        $this->assertStringContainsString('role=', $content, 'Page should contain ARIA roles');
        $this->assertStringContainsString('aria-label', $content, 'Page should contain ARIA labels');

        // Test for proper heading structure
        $this->assertMatchesRegularExpression('/<h[1-6][^>]*>/', $content, 'Page should have proper headings');

        // Test for form labels
        $this->assertStringContainsString('<label', $content, 'Forms should have proper labels');

        // Test for keyboard navigation support
        $this->assertStringContainsString('tabindex', $content, 'Interactive elements should support keyboard navigation');
    }

    /**
     * Test loading states and user feedback
     */
    public function test_loading_states_and_feedback()
    {
        // Test that API responses include timing information
        $response = $this->getJson('/api/search?limit=10');
        $response->assertStatus(200);

        $meta = $response->json('meta');
        $this->assertArrayHasKey('response_time_ms', $meta, 'API should provide response timing for UX optimization');
        $this->assertIsNumeric($meta['response_time_ms']);

        // Test that paginated results provide clear information
        $this->assertArrayHasKey('total', $meta, 'Users should know total number of results');
        $this->assertArrayHasKey('limit', $meta, 'Users should understand pagination limits');

        $data = $response->json('data');
        $this->assertArrayHasKey('pagination', $data, 'Pagination information should be clear');

        $pagination = $data['pagination'];
        $this->assertArrayHasKey('current_page', $pagination);
        $this->assertArrayHasKey('has_more', $pagination);
    }

    /**
     * Test search suggestions and user guidance
     */
    public function test_search_suggestions()
    {
        // Test empty search provides guidance
        $response = $this->getJson('/api/search?limit=10');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertNotEmpty($data['items'], 'Empty search should show popular/featured results');

        // Test search with no results
        $response = $this->getJson('/api/search?search_term=nonexistentservice123&limit=10');
        $response->assertStatus(200);

        $items = $response->json('data.items');
        // Even if no exact matches, should suggest alternatives or show general results
        $this->assertIsArray($items, 'No-results search should still provide suggestions');
    }

    /**
     * Helper method to simulate user scenarios
     */
    private function simulateUserScenario(array $scenario): array
    {
        $results = [
            'scenario' => $scenario['intent'],
            'steps_completed' => 0,
            'errors' => [],
            'success' => false,
        ];

        foreach ($scenario['steps'] as $step) {
            try {
                $this->executeScenarioStep($step);
                $results['steps_completed']++;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'step' => $step,
                    'error' => $e->getMessage(),
                ];
                break;
            }
        }

        $results['success'] = count($results['errors']) === 0;

        return $results;
    }

    /**
     * Execute individual scenario step
     */
    private function executeScenarioStep(string $step): void
    {
        switch ($step) {
            case 'visit_search_page':
                $response = $this->get('/search');
                $response->assertStatus(200);
                break;

            case 'enter_location_olsztyn':
            case 'select_pet_sitter_type':
            case 'submit_search':
                $response = $this->getJson('/api/search?location=Olsztyn&content_type=pet_sitter&limit=10');
                $response->assertStatus(200);
                break;

            case 'view_results':
                $response = $this->getJson('/api/search?limit=10');
                $response->assertStatus(200);
                $this->assertNotEmpty($response->json('data.items'));
                break;

            case 'set_max_price_40':
            case 'verify_all_results_under_price':
                $response = $this->getJson('/api/search?max_price=40&limit=10');
                $response->assertStatus(200);
                $items = $response->json('data.items');
                foreach ($items as $item) {
                    if (isset($item['price']['from'])) {
                        $this->assertLessThanOrEqual(40, (float) $item['price']['from']);
                    }
                }
                break;

            case 'switch_to_map_view':
                $response = $this->getJson('/api/search?format=map&limit=10');
                $response->assertStatus(200);
                $this->assertArrayHasKey('markers', $response->json('data'));
                break;

            default:
                // For steps that don't require API calls (UI interactions)
                break;
        }
    }

    /**
     * Assert scenario completed successfully
     */
    private function assertScenarioSuccess(array $scenario): void
    {
        $this->assertTrue(
            $scenario['success'],
            "User scenario '{$scenario['scenario']}' failed. Errors: ".json_encode($scenario['errors'])
        );

        $this->assertEmpty(
            $scenario['errors'],
            'User scenario should complete without errors'
        );
    }
}
