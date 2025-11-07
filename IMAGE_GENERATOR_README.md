# ARK Image Generator GUI

A simple Python GUI application for generating images using the ARK API.

## Features

- User-friendly GUI built with tkinter
- Support for all ARK API image generation parameters:
  - Model selection
  - Custom prompts
  - Source image URL for image-to-image generation
  - Sequential image generation toggle
  - Response format options (URL or base64)
  - Size options (4K, 1024x1024, 512x512)
  - Stream, watermark, and safety options
- Image preview display
- Open generated images directly in your browser
- API key management (reads from ARK_API_KEY environment variable)

## Requirements

- Python 3.7 or higher
- tkinter (usually comes with Python)
- Required Python packages (see requirements.txt)

## Installation

1. Install the required packages:
```bash
pip install -r requirements.txt
```

2. Set your ARK API key as an environment variable (optional):
```bash
export ARK_API_KEY="your_api_key_here"
```

## Usage

Run the application:
```bash
python image_generator.py
```

Or make it executable and run directly:
```bash
chmod +x image_generator.py
./image_generator.py
```

## How to Use

1. Enter your ARK API key (or set it as an environment variable)
2. Configure the generation parameters:
   - Model: The AI model to use
   - Prompt: Description of the image you want to generate
   - Source Image URL: (Optional) Base image for image-to-image generation
   - Size: Output image resolution
   - Other options as needed
3. Click "Generate Image"
4. Wait for the image to be generated
5. View the preview or click "Open in Browser" to see the full image

## API Parameters

- **model**: AI model identifier (default: seedream-4-0-250828)
- **prompt**: Text description of the desired image
- **image**: URL of source image for image-to-image generation
- **sequential_image_generation**: Enable/disable sequential generation
- **response_format**: Output format (url or b64_json)
- **size**: Image dimensions (4K, 1024x1024, 512x512)
- **stream**: Enable streaming response
- **watermark**: Add watermark to generated image
- **safety**: Enable safety filtering

## Troubleshooting

- **No module named 'PIL'**: Install Pillow with `pip install Pillow`
- **tkinter not found**: Install tkinter for your OS (usually `python3-tk` on Linux)
- **API errors**: Check your API key and internet connection
- **Timeout errors**: Try again or increase the timeout in the code

## License

This is a simple utility application. Use it as needed.
