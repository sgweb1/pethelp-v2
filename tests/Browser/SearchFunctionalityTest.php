<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SearchFunctionalityTest extends DuskTestCase
{
    /**
     * Test complete search flow - filters, map, list interactions
     */
    public function test_complete_search_functionality()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search')
                ->waitFor('.search-container', 10)
                ->assertSee('Wyszukiwarka');

            // Test search input
            $browser->type('input[name="search_term"]', 'pet sitter')
                ->pause(500);

            // Test location autocomplete
            if ($browser->element('input[name="location"]')) {
                $browser->type('input[name="location"]', 'Olsztyn')
                    ->pause(1000)
                    ->waitFor('.location-suggestions', 5);
            }

            // Test content type filter
            if ($browser->element('select[name="content_type"]')) {
                $browser->select('select[name="content_type"]', 'pet_sitter')
                    ->pause(500);
            }

            // Test price range
            if ($browser->element('input[name="min_price"]')) {
                $browser->type('input[name="min_price"]', '25')
                    ->type('input[name="max_price"]', '50')
                    ->pause(500);
            }

            // Submit search
            $browser->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            // Verify results are displayed
            $browser->assertSee('Wyniki wyszukiwania')
                ->assertPresent('.search-results .result-item');

            // Test view toggle (grid/list)
            if ($browser->element('.view-toggle')) {
                $browser->click('.view-toggle .grid-view')
                    ->pause(500)
                    ->click('.view-toggle .list-view')
                    ->pause(500);
            }

            // Test map functionality
            if ($browser->element('.map-container')) {
                $browser->click('.map-toggle')
                    ->pause(1000)
                    ->waitFor('.map-container .ol-viewport', 5)
                    ->assertPresent('.map-container');

                // Test map markers interaction
                if ($browser->element('.ol-overlay-container')) {
                    $browser->click('.ol-overlay-container .marker')
                        ->pause(500)
                        ->waitFor('.marker-popup', 3);
                }
            }

            // Test result item hover highlighting
            $browser->mouseover('.result-item:first-child')
                ->pause(500);

            // Test pagination if present
            if ($browser->element('.pagination')) {
                $browser->click('.pagination .next-page')
                    ->pause(1000)
                    ->assertPresent('.search-results');
            }

            // Test filter reset
            if ($browser->element('.clear-filters')) {
                $browser->click('.clear-filters')
                    ->pause(500)
                    ->assertInputValue('input[name="search_term"]', '')
                    ->assertInputValue('input[name="location"]', '');
            }
        });
    }

    /**
     * Test map-specific functionality
     */
    public function test_map_functionality()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search?view=map&location=Olsztyn')
                ->waitFor('.map-container', 10)
                ->assertPresent('.map-container .ol-viewport');

            // Test map controls
            $browser->click('.ol-zoom-in')
                ->pause(500)
                ->click('.ol-zoom-out')
                ->pause(500);

            // Test marker clustering
            if ($browser->element('.marker-cluster')) {
                $browser->click('.marker-cluster:first-child')
                    ->pause(1000);
            }

            // Test individual markers
            if ($browser->element('.marker:not(.cluster)')) {
                $browser->click('.marker:not(.cluster):first-child')
                    ->pause(500)
                    ->waitFor('.marker-popup', 3)
                    ->assertSee('Zobacz szczegóły');
            }

            // Test map bounds change triggers search
            $browser->drag('.ol-viewport', 100, 100)
                ->pause(2000)
                ->waitFor('.loading-indicator', 2);
        });
    }

    /**
     * Test list-map interactions
     */
    public function test_list_map_interactions()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search?content_type=pet_sitter')
                ->waitFor('.search-results', 10)
                ->assertPresent('.result-item');

            // Test hover on list item highlights map marker
            if ($browser->element('.result-item[data-item-id]') && $browser->element('.map-container')) {
                $itemId = $browser->attribute('.result-item:first-child', 'data-item-id');

                if ($itemId) {
                    // Store initial marker state
                    $initialMarkerClass = $browser->attribute(".map-marker[data-item-id=\"{$itemId}\"]", 'class');

                    // Hover over list item
                    $browser->mouseover(".result-item[data-item-id=\"{$itemId}\"]")
                        ->pause(500);

                    // Check if marker is highlighted
                    $hoveredMarkerClass = $browser->attribute(".map-marker[data-item-id=\"{$itemId}\"]", 'class');

                    // Verify marker visual change (should have highlight class)
                    $this->assertNotEquals($initialMarkerClass, $hoveredMarkerClass, 'Map marker should change appearance when list item is hovered');
                    $this->assertStringContains('highlighted', $hoveredMarkerClass, 'Map marker should have highlighted class');

                    // Mouse away and check if highlight is removed
                    $browser->mouseover('.search-container')
                        ->pause(500);

                    $finalMarkerClass = $browser->attribute(".map-marker[data-item-id=\"{$itemId}\"]", 'class');
                    $this->assertEquals($initialMarkerClass, $finalMarkerClass, 'Map marker should return to normal state when hover ends');
                }
            }

            // Test click on list item centers map on marker
            if ($browser->element('.result-item[data-item-id]') && $browser->element('.map-container')) {
                $itemId = $browser->attribute('.result-item:first-child', 'data-item-id');

                if ($itemId) {
                    // Store initial map center
                    $initialMapCenter = $browser->script('return window.map ? window.map.getView().getCenter() : null;')[0];

                    // Click on list item
                    $browser->click(".result-item[data-item-id=\"{$itemId}\"] .item-title")
                        ->pause(1000);

                    // Check if map center changed (marker should be centered)
                    $newMapCenter = $browser->script('return window.map ? window.map.getView().getCenter() : null;')[0];

                    if ($initialMapCenter && $newMapCenter) {
                        $this->assertNotEquals($initialMapCenter, $newMapCenter, 'Map should center on marker when list item is clicked');
                    }
                }
            }
        });
    }

    /**
     * Test sorting functionality
     */
    public function test_sorting_functionality()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search?content_type=pet_sitter&limit=10')
                ->waitFor('.search-results', 10)
                ->assertPresent('.result-item');

            // Test price sorting (low to high)
            if ($browser->element('.sort-dropdown') || $browser->element('[name="sort_by"]')) {
                // Get initial prices
                $initialPrices = $browser->script('
                    return Array.from(document.querySelectorAll(".result-item .price")).map(el => {
                        const priceText = el.textContent || el.innerText;
                        const match = priceText.match(/(\d+(?:\.\d+)?)/);
                        return match ? parseFloat(match[1]) : 0;
                    });
                ')[0];

                // Sort by price low to high
                if ($browser->element('.sort-dropdown')) {
                    $browser->click('.sort-dropdown')
                        ->pause(500)
                        ->click('[data-sort="price_low"]')
                        ->pause(2000)
                        ->waitFor('.search-results', 5);
                } else {
                    $browser->select('[name="sort_by"]', 'price_low')
                        ->pause(2000)
                        ->waitFor('.search-results', 5);
                }

                // Get prices after sorting
                $sortedPrices = $browser->script('
                    return Array.from(document.querySelectorAll(".result-item .price")).map(el => {
                        const priceText = el.textContent || el.innerText;
                        const match = priceText.match(/(\d+(?:\.\d+)?)/);
                        return match ? parseFloat(match[1]) : 0;
                    });
                ')[0];

                // Verify prices are sorted (ascending)
                $sortedPricesCopy = $sortedPrices;
                sort($sortedPricesCopy);
                $this->assertEquals($sortedPricesCopy, $sortedPrices, 'Prices should be sorted from low to high');
                $this->assertNotEquals($initialPrices, $sortedPrices, 'Price order should change after sorting');
            }

            // Test rating sorting
            if ($browser->element('.sort-dropdown') || $browser->element('[name="sort_by"]')) {
                // Sort by rating
                if ($browser->element('.sort-dropdown')) {
                    $browser->click('.sort-dropdown')
                        ->pause(500)
                        ->click('[data-sort="rating"]')
                        ->pause(2000)
                        ->waitFor('.search-results', 5);
                } else {
                    $browser->select('[name="sort_by"]', 'rating')
                        ->pause(2000)
                        ->waitFor('.search-results', 5);
                }

                // Get ratings after sorting
                $sortedRatings = $browser->script('
                    return Array.from(document.querySelectorAll(".result-item .rating")).map(el => {
                        const ratingText = el.textContent || el.innerText;
                        const match = ratingText.match(/(\d+(?:\.\d+)?)/);
                        return match ? parseFloat(match[1]) : 0;
                    });
                ')[0];

                // Verify ratings are sorted (descending - highest first)
                if (count($sortedRatings) > 1) {
                    for ($i = 0; $i < count($sortedRatings) - 1; $i++) {
                        $this->assertGreaterThanOrEqual(
                            $sortedRatings[$i + 1],
                            $sortedRatings[$i],
                            'Ratings should be sorted from high to low'
                        );
                    }
                }
            }

            // Test newest sorting
            if ($browser->element('.sort-dropdown') || $browser->element('[name="sort_by"]')) {
                if ($browser->element('.sort-dropdown')) {
                    $browser->click('.sort-dropdown')
                        ->pause(500)
                        ->click('[data-sort="newest"]')
                        ->pause(2000)
                        ->waitFor('.search-results', 5);
                } else {
                    $browser->select('[name="sort_by"]', 'newest')
                        ->pause(2000)
                        ->waitFor('.search-results', 5);
                }

                // Verify results are displayed (newest sorting is harder to verify visually)
                $browser->assertPresent('.result-item');
            }
        });
    }

    /**
     * Test specific pet sitter for cats search scenario
     */
    public function test_pet_sitter_cats_search()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search')
                ->waitFor('.search-container', 10);

            // Search for pet sitters for cats
            $browser->type('input[name="search_term"]', 'koty')
                ->pause(500);

            if ($browser->element('select[name="content_type"]')) {
                $browser->select('select[name="content_type"]', 'pet_sitter');
            }

            if ($browser->element('select[name="pet_type"]')) {
                $browser->select('select[name="pet_type"]', 'cat');
            }

            $browser->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            // Verify results are relevant to cats
            if ($browser->element('.result-item')) {
                $resultTexts = $browser->script('
                    return Array.from(document.querySelectorAll(".result-item")).map(el =>
                        (el.textContent || el.innerText).toLowerCase()
                    );
                ')[0];

                $catRelatedFound = false;
                foreach ($resultTexts as $text) {
                    if (str_contains($text, 'kot') || str_contains($text, 'cat')) {
                        $catRelatedFound = true;
                        break;
                    }
                }

                $this->assertTrue($catRelatedFound, 'Search results should contain cat-related content');
            }

            // Test that map markers correspond to list items
            if ($browser->element('.map-container') && $browser->element('.result-item')) {
                $listItemCount = $browser->script('return document.querySelectorAll(".result-item").length;')[0];
                $markerCount = $browser->script('return document.querySelectorAll(".map-marker").length;')[0];

                $this->assertEquals($listItemCount, $markerCount, 'Number of map markers should match number of list items');
            }

            // Test marker disappearance when filters change
            if ($browser->element('.map-container')) {
                $initialMarkerCount = $browser->script('return document.querySelectorAll(".map-marker").length;')[0];

                // Change search to different pet type
                if ($browser->element('select[name="pet_type"]')) {
                    $browser->select('select[name="pet_type"]', 'dog')
                        ->pause(2000)
                        ->waitFor('.search-results', 10);

                    $newMarkerCount = $browser->script('return document.querySelectorAll(".map-marker").length;')[0];

                    // Markers should change when filter changes
                    $this->assertNotEquals($initialMarkerCount, $newMarkerCount, 'Map markers should update when filters change');
                }
            }
        });
    }

    /**
     * Test all search filters functionality
     */
    public function test_all_search_filters()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search')
                ->waitFor('.search-container', 10);

            // Test content type filter
            if ($browser->element('select[name="content_type"]') || $browser->element('input[name="content_type"]')) {
                $this->testContentTypeFilter($browser);
            }

            // Test location filter
            if ($browser->element('input[name="location"]')) {
                $this->testLocationFilter($browser);
            }

            // Test price range filters
            if ($browser->element('input[name="min_price"]') || $browser->element('input[name="max_price"]')) {
                $this->testPriceFilters($browser);
            }

            // Test pet type filter
            if ($browser->element('select[name="pet_type"]')) {
                $this->testPetTypeFilter($browser);
            }

            // Test category filter
            if ($browser->element('select[name="category"]') || $browser->element('input[name="category"]')) {
                $this->testCategoryFilter($browser);
            }

            // Test rating filter
            if ($browser->element('input[name="min_rating"]') || $browser->element('.rating-filter')) {
                $this->testRatingFilter($browser);
            }

            // Test featured filter
            if ($browser->element('input[name="featured_only"]') || $browser->element('.featured-filter')) {
                $this->testFeaturedFilter($browser);
            }

            // Test search term filter
            if ($browser->element('input[name="search_term"]') || $browser->element('input[name="search"]')) {
                $this->testSearchTermFilter($browser);
            }

            // Test filter combinations
            $this->testFilterCombinations($browser);

            // Test filter reset
            $this->testFilterReset($browser);
        });
    }

    /**
     * Test content type filter
     */
    private function test_content_type_filter(Browser $browser)
    {
        $browser->visit('/search')
            ->waitFor('.search-container', 10);

        // Test pet_sitter filter
        if ($browser->element('select[name="content_type"]')) {
            $browser->select('select[name="content_type"]', 'pet_sitter')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            if ($browser->element('.result-item')) {
                $contentTypes = $browser->script('
                    return Array.from(document.querySelectorAll(".result-item")).map(item => {
                        const contentType = item.getAttribute("data-content-type") ||
                                          item.querySelector("[data-content-type]")?.getAttribute("data-content-type") ||
                                          (item.textContent.toLowerCase().includes("pet sitter") ? "pet_sitter" : "unknown");
                        return contentType;
                    });
                ')[0];

                foreach ($contentTypes as $type) {
                    $this->assertTrue(
                        $type === 'pet_sitter' || $type === 'unknown',
                        'Content type filter should show only pet_sitter results'
                    );
                }
            }
        }

        // Test service filter
        if ($browser->element('select[name="content_type"]')) {
            $browser->select('select[name="content_type"]', 'service')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        }
    }

    /**
     * Test location filter
     */
    private function test_location_filter(Browser $browser)
    {
        $browser->visit('/search')
            ->waitFor('.search-container', 10);

        // Test specific city
        $browser->type('input[name="location"]', 'Warszawa')
            ->pause(1000);

        // Check for location autocomplete
        if ($browser->element('.location-suggestions') || $browser->element('.autocomplete-dropdown')) {
            $browser->pause(1000)
                ->waitFor('.location-suggestions', 5);
        }

        $browser->press('Szukaj')
            ->pause(2000)
            ->waitFor('.search-results', 10);

        if ($browser->element('.result-item')) {
            $locations = $browser->script('
                return Array.from(document.querySelectorAll(".result-item")).map(item => {
                    const locationText = item.querySelector(".location, .city, .address")?.textContent ||
                                       item.textContent;
                    return locationText.toLowerCase();
                });
            ')[0];

            $warszawaFound = false;
            foreach ($locations as $location) {
                if (str_contains($location, 'warszaw')) {
                    $warszawaFound = true;
                    break;
                }
            }

            $this->assertTrue($warszawaFound, 'Location filter should return results from Warszawa');
        }

        // Test different city
        $browser->clear('input[name="location"]')
            ->type('input[name="location"]', 'Kraków')
            ->pause(1000)
            ->press('Szukaj')
            ->pause(2000)
            ->waitFor('.search-results', 10);

        $browser->assertPresent('.search-results');
    }

    /**
     * Test price range filters
     */
    private function test_price_filters(Browser $browser)
    {
        $browser->visit('/search')
            ->waitFor('.search-container', 10);

        // Test minimum price filter
        if ($browser->element('input[name="min_price"]')) {
            $browser->type('input[name="min_price"]', '30')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            if ($browser->element('.result-item .price')) {
                $prices = $browser->script('
                    return Array.from(document.querySelectorAll(".result-item .price")).map(el => {
                        const priceText = el.textContent || el.innerText;
                        const match = priceText.match(/(\d+(?:\.\d+)?)/);
                        return match ? parseFloat(match[1]) : 0;
                    }).filter(price => price > 0);
                ')[0];

                foreach ($prices as $price) {
                    $this->assertGreaterThanOrEqual(30, $price, 'Prices should be >= 30 PLN');
                }
            }
        }

        // Test maximum price filter
        if ($browser->element('input[name="max_price"]')) {
            $browser->clear('input[name="min_price"]')
                ->type('input[name="max_price"]', '50')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            if ($browser->element('.result-item .price')) {
                $prices = $browser->script('
                    return Array.from(document.querySelectorAll(".result-item .price")).map(el => {
                        const priceText = el.textContent || el.innerText;
                        const match = priceText.match(/(\d+(?:\.\d+)?)/);
                        return match ? parseFloat(match[1]) : 0;
                    }).filter(price => price > 0);
                ')[0];

                foreach ($prices as $price) {
                    $this->assertLessThanOrEqual(50, $price, 'Prices should be <= 50 PLN');
                }
            }
        }

        // Test price range
        if ($browser->element('input[name="min_price"]') && $browser->element('input[name="max_price"]')) {
            $browser->clear('input[name="max_price"]')
                ->type('input[name="min_price"]', '25')
                ->type('input[name="max_price"]', '40')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        }
    }

    /**
     * Test pet type filter
     */
    private function test_pet_type_filter(Browser $browser)
    {
        $browser->visit('/search')
            ->waitFor('.search-container', 10);

        if ($browser->element('select[name="pet_type"]')) {
            // Test cat filter
            $browser->select('select[name="pet_type"]', 'cat')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            if ($browser->element('.result-item')) {
                $resultTexts = $browser->script('
                    return Array.from(document.querySelectorAll(".result-item")).map(el =>
                        (el.textContent || el.innerText).toLowerCase()
                    );
                ')[0];

                $catRelated = false;
                foreach ($resultTexts as $text) {
                    if (str_contains($text, 'kot') || str_contains($text, 'cat')) {
                        $catRelated = true;
                        break;
                    }
                }

                $this->assertTrue($catRelated, 'Pet type filter should return cat-related results');
            }

            // Test dog filter
            $browser->select('select[name="pet_type"]', 'dog')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        }
    }

    /**
     * Test category filter
     */
    private function test_category_filter(Browser $browser)
    {
        $browser->visit('/search')
            ->waitFor('.search-container', 10);

        if ($browser->element('select[name="category"]')) {
            $browser->select('select[name="category"]', 'Pet Sitter - Koty')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        } elseif ($browser->element('input[name="category"]')) {
            $browser->type('input[name="category"]', 'Opieka nad kotami')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        }
    }

    /**
     * Test rating filter
     */
    private function test_rating_filter(Browser $browser)
    {
        $browser->visit('/search')
            ->waitFor('.search-container', 10);

        if ($browser->element('input[name="min_rating"]')) {
            $browser->type('input[name="min_rating"]', '4')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            if ($browser->element('.result-item .rating')) {
                $ratings = $browser->script('
                    return Array.from(document.querySelectorAll(".result-item .rating")).map(el => {
                        const ratingText = el.textContent || el.innerText;
                        const match = ratingText.match(/(\d+(?:\.\d+)?)/);
                        return match ? parseFloat(match[1]) : 0;
                    }).filter(rating => rating > 0);
                ')[0];

                foreach ($ratings as $rating) {
                    $this->assertGreaterThanOrEqual(4.0, $rating, 'Ratings should be >= 4.0');
                }
            }
        } elseif ($browser->element('.rating-filter')) {
            $browser->click('.rating-filter .rating-4')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        }
    }

    /**
     * Test featured filter
     */
    private function test_featured_filter(Browser $browser)
    {
        $browser->visit('/search')
            ->waitFor('.search-container', 10);

        if ($browser->element('input[name="featured_only"]')) {
            $browser->check('input[name="featured_only"]')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            if ($browser->element('.result-item')) {
                $featuredItems = $browser->script('
                    return Array.from(document.querySelectorAll(".result-item")).map(item => {
                        return item.classList.contains("featured") ||
                               item.querySelector(".featured-badge") !== null ||
                               item.getAttribute("data-featured") === "true";
                    });
                ')[0];

                foreach ($featuredItems as $isFeatured) {
                    $this->assertTrue($isFeatured, 'Featured filter should show only featured items');
                }
            }
        } elseif ($browser->element('.featured-filter')) {
            $browser->click('.featured-filter')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        }
    }

    /**
     * Test search term filter
     */
    private function test_search_term_filter(Browser $browser)
    {
        $browser->visit('/search')
            ->waitFor('.search-container', 10);

        $searchInput = $browser->element('input[name="search_term"]') ? 'input[name="search_term"]' : 'input[name="search"]';

        if ($browser->element($searchInput)) {
            // Test search for "opieka"
            $browser->type($searchInput, 'opieka')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            if ($browser->element('.result-item')) {
                $resultTexts = $browser->script('
                    return Array.from(document.querySelectorAll(".result-item")).map(el =>
                        (el.textContent || el.innerText).toLowerCase()
                    );
                ')[0];

                $relevantFound = false;
                foreach ($resultTexts as $text) {
                    if (str_contains($text, 'opieka') || str_contains($text, 'care')) {
                        $relevantFound = true;
                        break;
                    }
                }

                $this->assertTrue($relevantFound, 'Search term should return relevant results');
            }

            // Test search for "weterynarz"
            $browser->clear($searchInput)
                ->type($searchInput, 'weterynarz')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        }
    }

    /**
     * Test filter combinations
     */
    private function test_filter_combinations(Browser $browser)
    {
        $browser->visit('/search')
            ->waitFor('.search-container', 10);

        // Combination 1: Content type + Location
        if ($browser->element('select[name="content_type"]') && $browser->element('input[name="location"]')) {
            $browser->select('select[name="content_type"]', 'pet_sitter')
                ->type('input[name="location"]', 'Warszawa')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        }

        // Combination 2: Price range + Pet type
        if ($browser->element('input[name="min_price"]') && $browser->element('select[name="pet_type"]')) {
            $browser->clear('input[name="location"]')
                ->type('input[name="min_price"]', '20')
                ->type('input[name="max_price"]', '60')
                ->select('select[name="pet_type"]', 'cat')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        }

        // Combination 3: Search term + Featured + Rating
        $searchInput = $browser->element('input[name="search_term"]') ? 'input[name="search_term"]' : 'input[name="search"]';

        if ($browser->element($searchInput) && $browser->element('input[name="featured_only"]')) {
            $browser->clear('input[name="min_price"]')
                ->clear('input[name="max_price"]')
                ->type($searchInput, 'pet')
                ->check('input[name="featured_only"]')
                ->pause(1000)
                ->press('Szukaj')
                ->pause(2000)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        }
    }

    /**
     * Test filter reset functionality
     */
    private function test_filter_reset(Browser $browser)
    {
        $browser->visit('/search')
            ->waitFor('.search-container', 10);

        // Set multiple filters
        if ($browser->element('select[name="content_type"]')) {
            $browser->select('select[name="content_type"]', 'pet_sitter');
        }

        if ($browser->element('input[name="location"]')) {
            $browser->type('input[name="location"]', 'Warszawa');
        }

        if ($browser->element('input[name="min_price"]')) {
            $browser->type('input[name="min_price"]', '25');
        }

        $searchInput = $browser->element('input[name="search_term"]') ? 'input[name="search_term"]' : 'input[name="search"]';
        if ($browser->element($searchInput)) {
            $browser->type($searchInput, 'opieka');
        }

        $browser->pause(1000)
            ->press('Szukaj')
            ->pause(2000)
            ->waitFor('.search-results', 10);

        // Test reset functionality
        if ($browser->element('.reset-filters') || $browser->element('.clear-filters') || $browser->element('[type="reset"]')) {
            $resetSelector = $browser->element('.reset-filters') ? '.reset-filters' :
                           ($browser->element('.clear-filters') ? '.clear-filters' : '[type="reset"]');

            $browser->click($resetSelector)
                ->pause(1000);

            // Verify filters are cleared
            if ($browser->element('select[name="content_type"]')) {
                $selectedValue = $browser->value('select[name="content_type"]');
                $this->assertTrue(empty($selectedValue) || $selectedValue === '', 'Content type should be reset');
            }

            if ($browser->element('input[name="location"]')) {
                $this->assertEquals('', $browser->value('input[name="location"]'), 'Location should be cleared');
            }

            if ($browser->element('input[name="min_price"]')) {
                $this->assertEquals('', $browser->value('input[name="min_price"]'), 'Min price should be cleared');
            }

            if ($browser->element($searchInput)) {
                $this->assertEquals('', $browser->value($searchInput), 'Search term should be cleared');
            }
        }
    }

    /**
     * Test responsive behavior
     */
    public function test_responsive_search()
    {
        $this->browse(function (Browser $browser) {
            // Mobile view
            $browser->resize(375, 667)
                ->visit('/search')
                ->waitFor('.search-container', 10);

            // Test mobile menu/filters
            if ($browser->element('.mobile-filter-toggle')) {
                $browser->click('.mobile-filter-toggle')
                    ->pause(500)
                    ->assertVisible('.mobile-filters');
            }

            // Test mobile map view
            if ($browser->element('.mobile-map-toggle')) {
                $browser->click('.mobile-map-toggle')
                    ->pause(1000)
                    ->assertVisible('.map-container');
            }

            // Tablet view
            $browser->resize(768, 1024)
                ->pause(500)
                ->assertPresent('.search-container');

            // Desktop view
            $browser->resize(1440, 900)
                ->pause(500)
                ->assertPresent('.search-container');
        });
    }

    /**
     * Test advanced filtering scenarios
     */
    public function test_advanced_filtering_scenarios()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search')
                ->waitFor('.search-container', 10);

            // Test no results scenario
            $searchInput = $browser->element('input[name="search_term"]') ? 'input[name="search_term"]' : 'input[name="search"]';
            if ($browser->element($searchInput)) {
                $browser->type($searchInput, 'nonexistentservice12345xyz')
                    ->pause(1000)
                    ->press('Szukaj')
                    ->pause(2000)
                    ->waitFor('.search-results', 10);

                // Should show "no results" message or empty state
                if ($browser->element('.no-results') || $browser->element('.empty-state')) {
                    $browser->assertSee('Brak');
                }
            }

            // Test invalid price range (min > max)
            if ($browser->element('input[name="min_price"]') && $browser->element('input[name="max_price"]')) {
                $browser->clear($searchInput)
                    ->type('input[name="min_price"]', '100')
                    ->type('input[name="max_price"]', '50')
                    ->pause(1000)
                    ->press('Szukaj')
                    ->pause(2000);

                // Should handle gracefully
                $browser->assertPresent('.search-results');
            }

            // Test filter combinations that might conflict
            if ($browser->element('select[name="content_type"]') && $browser->element('select[name="pet_type"]')) {
                $browser->clear('input[name="min_price"]')
                    ->clear('input[name="max_price"]')
                    ->select('select[name="content_type"]', 'service')
                    ->select('select[name="pet_type"]', 'cat')
                    ->pause(1000)
                    ->press('Szukaj')
                    ->pause(2000)
                    ->waitFor('.search-results', 10);

                $browser->assertPresent('.search-results');
            }
        });
    }

    /**
     * Test URL parameter handling and bookmarking
     */
    public function test_url_parameters_and_bookmarking()
    {
        $this->browse(function (Browser $browser) {
            // Test direct URL with parameters
            $browser->visit('/search?content_type=pet_sitter&location=Warszawa&min_price=25&max_price=50')
                ->waitFor('.search-container', 10)
                ->waitFor('.search-results', 10);

            // Verify filters are applied from URL
            if ($browser->element('select[name="content_type"]')) {
                $this->assertEquals('pet_sitter', $browser->value('select[name="content_type"]'), 'Content type should be loaded from URL');
            }

            if ($browser->element('input[name="location"]')) {
                $this->assertStringContains('Warszawa', $browser->value('input[name="location"]'), 'Location should be loaded from URL');
            }

            if ($browser->element('input[name="min_price"]')) {
                $this->assertEquals('25', $browser->value('input[name="min_price"]'), 'Min price should be loaded from URL');
            }

            if ($browser->element('input[name="max_price"]')) {
                $this->assertEquals('50', $browser->value('input[name="max_price"]'), 'Max price should be loaded from URL');
            }

            // Test URL updates when filters change
            if ($browser->element('input[name="location"]')) {
                $browser->clear('input[name="location"]')
                    ->type('input[name="location"]', 'Kraków')
                    ->pause(1000)
                    ->press('Szukaj')
                    ->pause(2000);

                $currentUrl = $browser->driver->getCurrentURL();
                $this->assertStringContains('location=Krak', $currentUrl, 'URL should update with new location');
            }

            // Test shareable URLs
            $urlToShare = $browser->driver->getCurrentURL();
            $browser->visit($urlToShare)
                ->waitFor('.search-container', 10)
                ->waitFor('.search-results', 10);

            $browser->assertPresent('.search-results');
        });
    }

    /**
     * Test live/real-time filtering
     */
    public function test_live_filtering()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search')
                ->waitFor('.search-container', 10);

            // Test if filters update results without pressing submit (live filtering)
            if ($browser->element('select[name="content_type"]')) {
                $browser->select('select[name="content_type"]', 'pet_sitter')
                    ->pause(3000); // Wait for potential auto-update

                // Check if results updated automatically
                if ($browser->element('.result-item')) {
                    $browser->assertPresent('.search-results');
                }
            }

            // Test debounced search input
            $searchInput = $browser->element('input[name="search_term"]') ? 'input[name="search_term"]' : 'input[name="search"]';
            if ($browser->element($searchInput)) {
                $browser->type($searchInput, 'o')
                    ->pause(500)
                    ->type($searchInput, 'p')
                    ->pause(500)
                    ->type($searchInput, 'i')
                    ->pause(500)
                    ->type($searchInput, 'e')
                    ->pause(500)
                    ->type($searchInput, 'k')
                    ->pause(500)
                    ->type($searchInput, 'a')
                    ->pause(2000); // Wait for debounce

                // Check if search executed automatically
                if ($browser->element('.result-item')) {
                    $browser->assertPresent('.search-results');
                }
            }

            // Test price range sliders if present
            if ($browser->element('.price-slider') || $browser->element('input[type="range"]')) {
                $slider = $browser->element('.price-slider') ? '.price-slider' : 'input[type="range"]';

                $browser->script([
                    "document.querySelector('{$slider}').value = 40;",
                    "document.querySelector('{$slider}').dispatchEvent(new Event('input'));",
                ]);

                $browser->pause(2000);
                $browser->assertPresent('.search-results');
            }
        });
    }

    /**
     * Test error handling
     */
    public function test_error_handling()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search')
                ->waitFor('.search-container', 10);

            // Test invalid location
            if ($browser->element('input[name="location"]')) {
                $browser->type('input[name="location"]', 'InvalidLocation123')
                    ->press('Szukaj')
                    ->pause(2000)
                    ->waitFor('.error-message', 5);
            }

            // Test invalid price range
            if ($browser->element('input[name="min_price"]')) {
                $browser->type('input[name="min_price"]', '1000')
                    ->type('input[name="max_price"]', '10')
                    ->press('Szukaj')
                    ->pause(1000);
                // Should show validation error or auto-correct
            }

            // Test network error simulation (if possible)
            $browser->script([
                'window.fetch = () => Promise.reject(new Error("Network error"));',
            ]);

            $browser->press('Szukaj')
                ->pause(2000)
                ->waitFor('.error-message', 5);
        });
    }

    /**
     * Test performance and loading states
     */
    public function test_loading_states()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search')
                ->waitFor('.search-container', 10);

            // Test search loading state
            $browser->type('input[name="search_term"]', 'test')
                ->press('Szukaj')
                ->assertPresent('.loading-indicator')
                ->waitUntilMissing('.loading-indicator', 10);

            // Test map loading state
            if ($browser->element('.map-toggle')) {
                $browser->click('.map-toggle')
                    ->assertPresent('.map-loading')
                    ->waitUntilMissing('.map-loading', 10);
            }

            // Test infinite scroll loading
            if ($browser->element('.infinite-scroll')) {
                $browser->scrollToBottom()
                    ->pause(1000)
                    ->assertPresent('.loading-more');
            }
        });
    }

    /**
     * Test accessibility
     */
    public function test_accessibility_features()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search')
                ->waitFor('.search-container', 10);

            // Test keyboard navigation
            $browser->keys('input[name="search_term"]', ['{tab}'])
                ->assertFocused('input[name="location"]');

            // Test ARIA labels
            $browser->assertAttribute('.search-button', 'aria-label')
                ->assertAttribute('.map-container', 'role');

            // Test screen reader announcements
            if ($browser->element('[aria-live]')) {
                $browser->type('input[name="search_term"]', 'test')
                    ->press('Szukaj')
                    ->pause(2000)
                    ->assertAttribute('[aria-live]', 'aria-live', 'polite');
            }
        });
    }
}
