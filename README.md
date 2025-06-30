# Saudi Arabia License Plate Detection System

A modern web application for detecting and processing Saudi Arabia license plates using AI-powered image recognition.

## Features

- 🚗 **Real-time Plate Detection**: Advanced AI-powered license plate recognition
- 📱 **Mobile-Friendly**: Responsive design with camera capture support
- 🇸🇦 **Saudi Arabia Focus**: Specialized for Saudi license plate formats
- ⚡ **Fast Processing**: Quick image analysis and response
- 🎨 **Modern UI**: Clean, intuitive interface built with Next.js and Tailwind CSS
- 🔒 **Secure API**: Laravel-based backend with proper validation

## Tech Stack

### Frontend
- **Next.js 15** - React framework
- **TypeScript** - Type safety
- **Tailwind CSS** - Styling
- **shadcn/ui** - UI components
- **Lucide React** - Icons

### Backend
- **Laravel 10** - PHP framework
- **Intervention Image** - Image processing
- **MySQL** - Database (optional)

## Project Structure

```
├── src/                          # Next.js frontend
│   ├── app/
│   │   ├── page.tsx             # Main dashboard page
│   │   ├── layout.tsx           # Root layout
│   │   └── globals.css          # Global styles
│   ├── components/
│   │   ├── PlateScanner.tsx     # Main scanner component
│   │   └── ui/                  # shadcn/ui components
│   └── lib/
│       └── api.ts               # API utilities
├── laravel-api/                  # Laravel backend
│   ├── app/Http/Controllers/
│   │   └── PlateDetectionController.php
│   ├── routes/
│   │   └── api.php
│   ├── config/
│   │   └── cors.php
│   └── .env
└── README.md
```

## Setup Instructions

### Frontend Setup (Next.js)

1. **Install dependencies:**
   ```bash
   npm install
   ```

2. **Create environment file:**
   ```bash
   cp .env.example .env.local
   ```

3. **Configure environment variables:**
   ```env
   NEXT_PUBLIC_API_URL=http://localhost:8001/api
   ```

4. **Start development server:**
   ```bash
   npm run dev
   ```

   The frontend will be available at `http://localhost:8000`

### Backend Setup (Laravel)

1. **Navigate to Laravel directory:**
   ```bash
   cd laravel-api
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

4. **Configure database (optional):**
   - Update `.env` file with your database credentials
   - Run migrations if you plan to store plate records:
   ```bash
   php artisan migrate
   ```

5. **Start Laravel server:**
   ```bash
   php artisan serve --port=8001
   ```

   The API will be available at `http://localhost:8001`

## Usage

1. **Access the Dashboard:**
   - Open your browser and go to `http://localhost:8000`
   - You'll see the Saudi Plate Detection dashboard

2. **Scan a License Plate:**
   - Click the "Scan License Plate" button
   - Choose an image file or use your device's camera
   - The system will process the image and extract the plate number
   - Results will be displayed with a success/error message

3. **API Response:**
   - **Success (200)**: "Saved in the system" with detected plate number
   - **Error (404)**: "Plate not found or invalid plate format"
   - **Error (422)**: "Invalid image file or format"

## API Endpoints

### POST `/api/plate-detect`
Detect license plate from uploaded image.

**Request:**
- Method: `POST`
- Content-Type: `multipart/form-data`
- Body: `plate_image` (image file)

**Response (Success - 200):**
```json
{
  "message": "Saved in the system",
  "plate": "ر س د 1234",
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

**Response (Error - 404):**
```json
{
  "error": "No valid Saudi Arabia license plate detected in the image."
}
```

### GET `/api/health`
Check API health status.

**Response:**
```json
{
  "status": "ok",
  "message": "API is running",
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

## Saudi License Plate Formats

The system recognizes various Saudi Arabia license plate formats:
- **Standard Format**: 3 Arabic letters + 4 numbers (e.g., "أ ب ج 1234")
- **Alternative Formats**: Numbers + Arabic letters combinations
- **Validation**: Checks for Arabic characters, numbers, and proper length

## Development Notes

### AI Model Integration
Currently, the system uses simulated AI detection for demonstration. To integrate a real AI model:

1. **Replace the simulation in `PlateDetectionController.php`:**
   ```php
   private function processPlateDetection($imageFile): string
   {
       // Integrate with your AI service (e.g., Tesseract, custom ML model)
       // Example: return $this->callAIService($imageFile);
   }
   ```

2. **Consider these AI solutions:**
   - **Tesseract OCR** with Arabic language support
   - **Custom YOLO model** trained on Saudi plates
   - **Cloud AI services** (Google Vision, AWS Rekognition)
   - **Python microservice** with OpenCV and deep learning

### Database Integration
To store plate detection history:

1. **Create migration:**
   ```bash
   php artisan make:migration create_plates_table
   ```

2. **Create model:**
   ```bash
   php artisan make:model Plate
   ```

3. **Uncomment database code** in `PlateDetectionController.php`

## Troubleshooting

### Common Issues

1. **CORS Errors:**
   - Ensure Laravel CORS is properly configured
   - Check that frontend URL is allowed in CORS settings

2. **File Upload Issues:**
   - Verify PHP upload limits in `php.ini`
   - Check Laravel file size limits

3. **Image Processing Errors:**
   - Ensure Intervention Image is properly installed
   - Check GD or ImageMagick extension availability

4. **API Connection Issues:**
   - Verify both servers are running on correct ports
   - Check firewall settings

### Performance Optimization

1. **Frontend:**
   - Implement image compression before upload
   - Add loading states and progress indicators
   - Cache API responses where appropriate

2. **Backend:**
   - Optimize image processing pipeline
   - Implement queue system for heavy processing
   - Add Redis caching for frequent requests

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions:
- Create an issue in the repository
- Check the troubleshooting section
- Review the API documentation

---

**Note**: This system is designed for demonstration purposes. For production use, ensure proper security measures, error handling, and AI model integration.
