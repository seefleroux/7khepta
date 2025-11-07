# ARK Image Generator - Application Demo

## Application Interface

The application provides a clean, user-friendly GUI for generating images with the ARK API:

```
╔═══════════════════════════════════════════════════════════════╗
║              ARK Image Generator - Application                ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  🔑 API Key:        [••••••••••••••••••••••••••••••]         ║
║                                                               ║
║  🤖 Model:          [seedream-4-0-250828            ]         ║
║                                                               ║
║  ✍️  Prompt:         ┌──────────────────────────────┐        ║
║                     │Generate a close-up image of a│        ║
║                     │dog lying on lush grass.      │        ║
║                     └──────────────────────────────┘        ║
║                                                               ║
║  🖼️  Source URL:     [https://ark-doc.tos-ap...  ]          ║
║                                                               ║
║  ⚙️  Sequential:     [disabled ▼]                            ║
║  📋 Format:         [url ▼]                                  ║
║  📏 Size:           [4K ▼]                                   ║
║                                                               ║
║  ☐ Stream    ☐ Watermark    ☐ Safety                        ║
║                                                               ║
║              ┌──────────────────────┐                        ║
║              │   Generate Image     │                        ║
║              └──────────────────────┘                        ║
║                                                               ║
║  Status: ✓ Image generated successfully!                     ║
║                                                               ║
║  🔗 Generated:      [https://generated-url.com/image.png]    ║
║                                                               ║
║              ┌──────────────────────┐                        ║
║              │  Open in Browser     │                        ║
║              └──────────────────────┘                        ║
║                                                               ║
║  ┌─────────────────────────────────────────────────────┐    ║
║  │                                                     │    ║
║  │                 [Generated Image]                   │    ║
║  │              (Preview shown here)                   │    ║
║  │                                                     │    ║
║  └─────────────────────────────────────────────────────┘    ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
```

## Key Features

✅ **All API Parameters Supported**
- Model selection
- Custom prompts (multi-line text area)
- Source image URL for image-to-image generation
- Sequential generation toggle
- Response format (URL or base64)
- Size presets (4K, 1024x1024, 512x512)
- Stream, watermark, and safety options

✅ **User-Friendly Design**
- Clean, organized layout
- Clear labels and input fields
- Password-masked API key field
- Status updates during generation
- Error messages for issues

✅ **Image Preview**
- Automatic thumbnail display
- Maintains aspect ratio
- Quick visual confirmation

✅ **Browser Integration**
- One-click open in browser
- Direct access to full-resolution image

✅ **Security**
- URL validation (HTTP/HTTPS only)
- Timeout protection
- Safe error handling

## Example Usage Flow

1. **Setup**: Enter your ARK API key (or set ARK_API_KEY environment variable)
2. **Configure**: Adjust model, prompt, and other parameters as needed
3. **Generate**: Click "Generate Image" button
4. **Wait**: Status shows "Generating image..."
5. **Success**: Preview appears, URL is displayed
6. **View**: Click "Open in Browser" to see full image

## Files Created

- `image_generator.py` - Main GUI application (315 lines)
- `requirements.txt` - Python dependencies
- `IMAGE_GENERATOR_README.md` - Complete documentation
- `GUI_LAYOUT.md` - Interface design documentation
- `test_components.py` - Component validation script
- `.gitignore` - Git ignore patterns for Python

## Running the Application

```bash
# Install dependencies
pip install -r requirements.txt

# Run the application
python image_generator.py

# Or make it executable
chmod +x image_generator.py
./image_generator.py
```

## API Integration

The application makes POST requests to:
```
https://ark.ap-southeast.bytepluses.com/api/v3/images/generations
```

With all parameters from the curl command properly structured:
- Headers: Content-Type, Authorization
- Body: JSON with all generation parameters
- Response: Parses URL from response data
