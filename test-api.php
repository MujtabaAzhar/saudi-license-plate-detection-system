<?php
/**
 * Simple API test script to verify the plate detection endpoint
 */

// Test the health endpoint
echo "Testing API Health Endpoint...\n";
$healthResponse = file_get_contents('http://localhost:8001/health');
echo "Health Response: " . $healthResponse . "\n\n";

// Test the plate detection endpoint with a simulated file upload
echo "Testing Plate Detection Endpoint...\n";

// Create a simple test image (1x1 pixel PNG)
$testImageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==');
$tempFile = tempnam(sys_get_temp_dir(), 'test_plate_') . '.png';
file_put_contents($tempFile, $testImageData);

// Prepare the POST data
$postData = [
    'plate_image' => new CURLFile($tempFile, 'image/png', 'test-plate.png')
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/plate-detect');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Clean up
unlink($tempFile);

// Display results
echo "HTTP Status Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";

if ($httpCode === 200) {
    echo "\n✅ SUCCESS: API is working correctly!\n";
    $data = json_decode($response, true);
    if (isset($data['plate'])) {
        echo "Detected Plate: " . $data['plate'] . "\n";
    }
} else {
    echo "\n❌ ERROR: API test failed\n";
}
?>
