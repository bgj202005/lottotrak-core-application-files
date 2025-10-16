<?php

// Test script to verify consolidation logic is working

echo "Testing URL: http://localhost/lottotrak/admin/history/followers/1\n";

// Check if we can access the URL directly
$url = "http://localhost/lottotrak/admin/history/followers/1";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
if ($error) {
    echo "CURL Error: " . $error . "\n";
} else {
    echo "Response length: " . strlen($response) . " characters\n";
    
    // Check for extra categories in response
    $extraCount = substr_count($response, "Extra");
    $winnerCount = substr_count($response, "Winners");
    
    echo "Found 'Extra' " . $extraCount . " times\n";
    echo "Found 'Winners' " . $winnerCount . " times\n";
    
    // Look for specific extra patterns
    if (strpos($response, "+ Extra") !== false) {
        echo "WARNING: Found '+ Extra' patterns in response - extra categories may not be hidden\n";
    } else {
        echo "SUCCESS: No '+ Extra' patterns found - extra categories appear to be hidden\n";
    }
}

echo "\nTest completed.\n";

?>