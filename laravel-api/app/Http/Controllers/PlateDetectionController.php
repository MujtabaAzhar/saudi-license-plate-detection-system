<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class PlateDetectionController extends Controller
{
    /**
     * Detect license plate from uploaded image
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function detect(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'plate_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Invalid file. Please upload a valid image (JPEG, PNG, JPG, GIF, WebP) under 5MB.'
                ], 422);
            }

            $imageFile = $request->file('plate_image');
            
            // Log the detection attempt
            Log::info('Plate detection attempt', [
                'file_name' => $imageFile->getClientOriginalName(),
                'file_size' => $imageFile->getSize(),
                'mime_type' => $imageFile->getMimeType()
            ]);

            // Process the image and extract plate number
            $extractedPlate = $this->processPlateDetection($imageFile);

            // Validate the extracted plate format
            if (!$this->validateSaudiPlateFormat($extractedPlate)) {
                Log::warning('Invalid plate format detected', ['plate' => $extractedPlate]);
                return response()->json([
                    'error' => 'No valid Saudi Arabia license plate detected in the image.'
                ], 404);
            }

            // Optionally save to database (uncomment if you have a Plate model)
            // $this->savePlateRecord($extractedPlate, $imageFile);

            Log::info('Plate detection successful', ['plate' => $extractedPlate]);

            return response()->json([
                'message' => 'Saved in the system',
                'plate' => $extractedPlate,
                'timestamp' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Plate detection error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred while processing the image.'
            ], 500);
        }
    }

    /**
     * Process the uploaded image and extract plate number using AI/OCR
     * This is a simplified simulation - in production, integrate with actual AI model
     *
     * @param \Illuminate\Http\UploadedFile $imageFile
     * @return string
     */
    private function processPlateDetection($imageFile): string
    {
        try {
            // In a real implementation, you would:
            // 1. Use an AI model like YOLO or Tesseract OCR
            // 2. Preprocess the image (resize, enhance contrast, etc.)
            // 3. Extract text from the detected plate region
            // 4. Post-process the extracted text

            // For demonstration, we'll simulate different scenarios based on image properties
            $fileSize = $imageFile->getSize();
            $fileName = strtolower($imageFile->getClientOriginalName());

            // Simulate AI detection based on file characteristics
            if ($fileSize > 1000000) { // Large file, assume good quality
                $plates = ['ر س د 1234', 'أ ب ج 5678', 'ل م ن 9012', 'ق و ي 3456'];
                return $plates[array_rand($plates)];
            } elseif (strpos($fileName, 'test') !== false) {
                return 'ر س د 1234'; // Test plate
            } elseif (strpos($fileName, 'sample') !== false) {
                return 'أ ب ج 5678'; // Sample plate
            } else {
                // Random valid Saudi plate for demo
                $arabicLetters = ['أ', 'ب', 'ج', 'د', 'ر', 'س', 'ص', 'ط', 'ع', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي'];
                $letter1 = $arabicLetters[array_rand($arabicLetters)];
                $letter2 = $arabicLetters[array_rand($arabicLetters)];
                $letter3 = $arabicLetters[array_rand($arabicLetters)];
                $numbers = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                
                return "{$letter1} {$letter2} {$letter3} {$numbers}";
            }
        } catch (\Exception $e) {
            Log::error('Image processing error', ['message' => $e->getMessage()]);
            throw new \Exception('Failed to process image for plate detection');
        }
    }

    /**
     * Validate Saudi Arabia license plate format
     * Saudi plates typically have 3 Arabic letters followed by 4 numbers
     *
     * @param string $plate
     * @return bool
     */
    private function validateSaudiPlateFormat(string $plate): bool
    {
        // Remove extra spaces and normalize
        $plate = trim($plate);
        
        // Saudi Arabia plate patterns:
        // Pattern 1: 3 Arabic letters + 4 numbers (e.g., "أ ب ج 1234")
        // Pattern 2: Numbers + Arabic letters combinations
        
        $patterns = [
            '/^[\u0600-\u06FF\s]{5,}\s+\d{4}$/', // Arabic letters + numbers
            '/^\d{1,4}\s+[\u0600-\u06FF\s]{3,}$/', // Numbers + Arabic letters
            '/^[\u0600-\u06FF]\s+[\u0600-\u06FF]\s+[\u0600-\u06FF]\s+\d{4}$/', // Specific format
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $plate)) {
                return true;
            }
        }

        // Simplified validation for demo - check if contains Arabic characters and numbers
        $hasArabic = preg_match('/[\u0600-\u06FF]/', $plate);
        $hasNumbers = preg_match('/\d/', $plate);
        $validLength = strlen($plate) >= 6 && strlen($plate) <= 20;

        return $hasArabic && $hasNumbers && $validLength;
    }

    /**
     * Save plate record to database (optional)
     *
     * @param string $plateNumber
     * @param \Illuminate\Http\UploadedFile $imageFile
     * @return void
     */
    private function savePlateRecord(string $plateNumber, $imageFile): void
    {
        // Uncomment and modify if you have a Plate model and database table
        /*
        try {
            Plate::create([
                'plate_number' => $plateNumber,
                'image_path' => $imageFile->store('plates', 'public'),
                'detected_at' => now(),
                'file_size' => $imageFile->getSize(),
                'mime_type' => $imageFile->getMimeType(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save plate record', ['error' => $e->getMessage()]);
        }
        */
    }
}
