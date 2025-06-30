<?php
/**
 * Standalone PHP API Server for Saudi Plate Detection
 * This serves as a simple backend for testing the frontend
 */

// Enable CORS for all origins
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get the request URI and method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Parse the URI to get the endpoint
$path = parse_url($requestUri, PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Route handling
if ($requestMethod === 'GET' && end($pathParts) === 'health') {
    // Health check endpoint
    echo json_encode([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => date('c'),
        'server' => 'Standalone PHP Server'
    ]);
    exit();
}

if ($requestMethod === 'POST' && end($pathParts) === 'plate-detect') {
    // Plate detection endpoint
    handlePlateDetection();
    exit();
}

// 404 for unknown endpoints
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
exit();

/**
 * Handle plate detection request
 */
function handlePlateDetection() {
    try {
        // Check if file was uploaded
        if (!isset($_FILES['plate_image'])) {
            http_response_code(422);
            echo json_encode(['error' => 'No image file provided']);
            return;
        }

        $uploadedFile = $_FILES['plate_image'];

        // Validate file upload
        if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
            http_response_code(422);
            echo json_encode(['error' => 'File upload error']);
            return;
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $fileType = $uploadedFile['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            http_response_code(422);
            echo json_encode(['error' => 'Invalid file type. Please upload JPEG, PNG, JPG, GIF, or WebP images.']);
            return;
        }

        // Validate file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($uploadedFile['size'] > $maxSize) {
            http_response_code(422);
            echo json_encode(['error' => 'File size too large. Maximum size is 5MB.']);
            return;
        }

        // Simulate AI plate detection
        $detectedPlate = simulatePlateDetection($uploadedFile);

        // Validate Saudi plate format
        if (!validateSaudiPlateFormat($detectedPlate)) {
            http_response_code(404);
            echo json_encode(['error' => 'No valid Saudi Arabia license plate detected in the image.']);
            return;
        }

        // Success response
        echo json_encode([
            'message' => 'Saved in the system',
            'plate' => $detectedPlate,
            'timestamp' => date('c'),
            'file_info' => [
                'name' => $uploadedFile['name'],
                'size' => $uploadedFile['size'],
                'type' => $uploadedFile['type']
            ]
        ]);

    } catch (Exception $e) {
        error_log('Plate detection error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'An unexpected error occurred while processing the image.']);
    }
}

/**
 * Simulate AI plate detection
 * In production, this would call a real AI model
 */
function simulatePlateDetection($uploadedFile) {
    $fileName = strtolower($uploadedFile['name']);
    $fileSize = $uploadedFile['size'];

    // Simulate different detection results based on file characteristics
    if ($fileSize > 1000000) { // Large file, assume good quality
        $plates = ['ر س د 1234', 'أ ب ج 5678', 'ل م ن 9012', 'ق و ي 3456'];
        return $plates[array_rand($plates)];
    } elseif (strpos($fileName, 'test') !== false) {
        return 'ر س د 1234'; // Test plate
    } elseif (strpos($fileName, 'sample') !== false) {
        return 'أ ب ج 5678'; // Sample plate
    } else {
        // Generate random valid Saudi plate
        $arabicLetters = ['أ', 'ب', 'ج', 'د', 'ر', 'س', 'ص', 'ط', 'ع', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي'];
        $letter1 = $arabicLetters[array_rand($arabicLetters)];
        $letter2 = $arabicLetters[array_rand($arabicLetters)];
        $letter3 = $arabicLetters[array_rand($arabicLetters)];
        $numbers = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return "{$letter1} {$letter2} {$letter3} {$numbers}";
    }
}

/**
 * Validate Saudi Arabia license plate format
 */
function validateSaudiPlateFormat($plate) {
    // Remove extra spaces and normalize
    $plate = trim($plate);
    
    // Check if contains Arabic characters and numbers
    $hasArabic = preg_match('/[\x{0600}-\x{06FF}]/u', $plate);
    $hasNumbers = preg_match('/\d/', $plate);
    $validLength = strlen($plate) >= 6 && strlen($plate) <= 20;

    return $hasArabic && $hasNumbers && $validLength;
}

/**
 * Log request for debugging
 */
function logRequest() {
    $logData = [
        'timestamp' => date('c'),
        'method' => $_SERVER['REQUEST_METHOD'],
        'uri' => $_SERVER['REQUEST_URI'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ];
    
    error_log('API Request: ' . json_encode($logData));
}

// Log all requests
logRequest();
?>
