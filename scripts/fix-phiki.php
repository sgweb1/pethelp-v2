<?php
/**
 * Fix script for phiki/phiki PatternSearcher exceptions
 * Run after composer update/install to prevent crashes
 */

$filePath = __DIR__ . '/../vendor/phiki/phiki/src/TextMate/PatternSearcher.php';

if (!file_exists($filePath)) {
    echo "Phiki PatternSearcher.php not found\n";
    exit(1);
}

$content = file_get_contents($filePath);

// Check if already fixed
if (strpos($content, '@mb_ereg_search_init') !== false) {
    echo "Phiki PatternSearcher.php already fixed\n";
    exit(0);
}

// Apply fixes
$fixes = [
    'if (! mb_ereg_search_init($lineText)) {
            throw new FailedToInitializePatternSearchException;
        }' => 'if (! @mb_ereg_search_init($lineText)) {
            // Silently return null instead of throwing exception
            return null;
        }',

    'if (! mb_ereg_search_setpos($linePos)) {
                throw new FailedToSetSearchPositionException;
            }' => 'if (! @mb_ereg_search_setpos($linePos)) {
                // Skip this pattern instead of throwing exception
                continue;
            }'
];

foreach ($fixes as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

file_put_contents($filePath, $content);

echo "Phiki PatternSearcher.php fixed successfully\n";