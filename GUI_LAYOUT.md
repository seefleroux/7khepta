# ARK Image Generator - GUI Layout

```
┌─────────────────────────────────────────────────────────────────┐
│                    ARK Image Generator                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  API Key:           [********************************]          │
│                                                                 │
│  Model:             [seedream-4-0-250828            ]          │
│                                                                 │
│  Prompt:            ┌────────────────────────────────┐         │
│                     │Generate a close-up image of a  │         │
│                     │dog lying on lush grass.        │         │
│                     │                                │         │
│                     └────────────────────────────────┘         │
│                                                                 │
│  Source Image URL:  [https://ark-doc.tos-ap...     ]          │
│                                                                 │
│  Sequential Gen:    [disabled ▼]                               │
│                                                                 │
│  Response Format:   [url ▼]                                    │
│                                                                 │
│  Size:              [4K ▼]                                     │
│                                                                 │
│  Stream:            [ ]                                        │
│                                                                 │
│  Watermark:         [ ]                                        │
│                                                                 │
│  Safety:            [ ]                                        │
│                                                                 │
│                   [ Generate Image ]                           │
│                                                                 │
│  Status: Ready                                                 │
│                                                                 │
│  Generated URL:     [https://generated-image-url.com...]       │
│                                                                 │
│                   [ Open in Browser ]                          │
│                                                                 │
│  ┌───────────────────────────────────────────────────────┐    │
│  │                                                       │    │
│  │            [Image Preview Area]                       │    │
│  │      Generated image will appear here                 │    │
│  │                                                       │    │
│  └───────────────────────────────────────────────────────┘    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Features Shown:

1. **API Key Field** - Secure input (shown as asterisks) that reads from ARK_API_KEY env var
2. **Model Selection** - Text field for model name
3. **Prompt Area** - Multi-line text area for image description
4. **Source Image URL** - URL input for image-to-image generation
5. **Sequential Generation** - Dropdown (enabled/disabled)
6. **Response Format** - Dropdown (url/b64_json)
7. **Size Selection** - Dropdown (4K/1024x1024/512x512)
8. **Checkboxes** - For stream, watermark, and safety options
9. **Generate Button** - Triggers the API call
10. **Status Display** - Shows current operation status
11. **Generated URL Field** - Displays the resulting image URL
12. **Open in Browser Button** - Opens the image in default browser
13. **Image Preview** - Shows thumbnail of generated image

## Workflow:

1. User fills in API key and parameters
2. Clicks "Generate Image"
3. Status updates to "Generating image..."
4. Upon success:
   - Generated URL appears in the field
   - Image preview loads and displays
   - "Open in Browser" button becomes active
5. User can click "Open in Browser" to view full-size image
