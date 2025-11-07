#!/usr/bin/env python3
"""
Simple GUI application for generating images using ARK API.
"""

import tkinter as tk
from tkinter import ttk, messagebox, scrolledtext
import requests
import os
import webbrowser
from PIL import Image, ImageTk
import io
from urllib.request import urlopen, Request
from urllib.parse import urlparse

# Default values
DEFAULT_SOURCE_IMAGE_URL = "https://ark-doc.tos-ap-southeast-1.bytepluses.com/doc_image/seedream4_imageToimage.png"
IMAGE_PREVIEW_TIMEOUT = 10  # seconds

class ImageGeneratorApp:
    def __init__(self, root):
        self.root = root
        self.root.title("ARK Image Generator")
        self.root.geometry("900x800")
        
        # Create main container with scrollbar
        main_frame = ttk.Frame(root, padding="10")
        main_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))
        
        # Configure grid weights
        root.columnconfigure(0, weight=1)
        root.rowconfigure(0, weight=1)
        main_frame.columnconfigure(1, weight=1)
        
        row = 0
        
        # API Key
        ttk.Label(main_frame, text="API Key:").grid(row=row, column=0, sticky=tk.W, pady=5)
        self.api_key_var = tk.StringVar(value=os.environ.get('ARK_API_KEY', ''))
        ttk.Entry(main_frame, textvariable=self.api_key_var, width=50, show="*").grid(row=row, column=1, sticky=(tk.W, tk.E), pady=5)
        row += 1
        
        # Model
        ttk.Label(main_frame, text="Model:").grid(row=row, column=0, sticky=tk.W, pady=5)
        self.model_var = tk.StringVar(value="seedream-4-0-250828")
        ttk.Entry(main_frame, textvariable=self.model_var, width=50).grid(row=row, column=1, sticky=(tk.W, tk.E), pady=5)
        row += 1
        
        # Prompt
        ttk.Label(main_frame, text="Prompt:").grid(row=row, column=0, sticky=tk.W, pady=5)
        self.prompt_text = scrolledtext.ScrolledText(main_frame, height=4, width=50)
        self.prompt_text.grid(row=row, column=1, sticky=(tk.W, tk.E), pady=5)
        self.prompt_text.insert(1.0, "Generate a close-up image of a dog lying on lush grass.")
        row += 1
        
        # Source Image URL
        ttk.Label(main_frame, text="Source Image URL:").grid(row=row, column=0, sticky=tk.W, pady=5)
        self.image_url_var = tk.StringVar(value=DEFAULT_SOURCE_IMAGE_URL)
        ttk.Entry(main_frame, textvariable=self.image_url_var, width=50).grid(row=row, column=1, sticky=(tk.W, tk.E), pady=5)
        row += 1
        
        # Sequential Image Generation
        ttk.Label(main_frame, text="Sequential Generation:").grid(row=row, column=0, sticky=tk.W, pady=5)
        self.sequential_var = tk.StringVar(value="disabled")
        ttk.Combobox(main_frame, textvariable=self.sequential_var, values=["disabled", "enabled"], width=47).grid(row=row, column=1, sticky=(tk.W, tk.E), pady=5)
        row += 1
        
        # Response Format
        ttk.Label(main_frame, text="Response Format:").grid(row=row, column=0, sticky=tk.W, pady=5)
        self.response_format_var = tk.StringVar(value="url")
        ttk.Combobox(main_frame, textvariable=self.response_format_var, values=["url", "b64_json"], width=47).grid(row=row, column=1, sticky=(tk.W, tk.E), pady=5)
        row += 1
        
        # Size
        ttk.Label(main_frame, text="Size:").grid(row=row, column=0, sticky=tk.W, pady=5)
        self.size_var = tk.StringVar(value="4K")
        ttk.Combobox(main_frame, textvariable=self.size_var, values=["4K", "1024x1024", "512x512"], width=47).grid(row=row, column=1, sticky=(tk.W, tk.E), pady=5)
        row += 1
        
        # Stream
        ttk.Label(main_frame, text="Stream:").grid(row=row, column=0, sticky=tk.W, pady=5)
        self.stream_var = tk.BooleanVar(value=False)
        ttk.Checkbutton(main_frame, variable=self.stream_var).grid(row=row, column=1, sticky=tk.W, pady=5)
        row += 1
        
        # Watermark
        ttk.Label(main_frame, text="Watermark:").grid(row=row, column=0, sticky=tk.W, pady=5)
        self.watermark_var = tk.BooleanVar(value=False)
        ttk.Checkbutton(main_frame, variable=self.watermark_var).grid(row=row, column=1, sticky=tk.W, pady=5)
        row += 1
        
        # Safety
        ttk.Label(main_frame, text="Safety:").grid(row=row, column=0, sticky=tk.W, pady=5)
        self.safety_var = tk.BooleanVar(value=False)
        ttk.Checkbutton(main_frame, variable=self.safety_var).grid(row=row, column=1, sticky=tk.W, pady=5)
        row += 1
        
        # Generate Button
        self.generate_btn = ttk.Button(main_frame, text="Generate Image", command=self.generate_image)
        self.generate_btn.grid(row=row, column=0, columnspan=2, pady=20)
        row += 1
        
        # Status Label
        self.status_var = tk.StringVar(value="Ready")
        ttk.Label(main_frame, textvariable=self.status_var, foreground="blue").grid(row=row, column=0, columnspan=2, pady=5)
        row += 1
        
        # Result URL
        ttk.Label(main_frame, text="Generated Image URL:").grid(row=row, column=0, sticky=tk.W, pady=5)
        self.result_url_var = tk.StringVar()
        result_entry = ttk.Entry(main_frame, textvariable=self.result_url_var, width=50, state="readonly")
        result_entry.grid(row=row, column=1, sticky=(tk.W, tk.E), pady=5)
        row += 1
        
        # Open in Browser Button
        self.open_btn = ttk.Button(main_frame, text="Open in Browser", command=self.open_in_browser, state="disabled")
        self.open_btn.grid(row=row, column=0, columnspan=2, pady=5)
        row += 1
        
        # Image Preview
        self.image_label = ttk.Label(main_frame, text="Generated image will appear here")
        self.image_label.grid(row=row, column=0, columnspan=2, pady=10)
        row += 1
        
    def generate_image(self):
        """Generate image using ARK API"""
        api_key = self.api_key_var.get().strip()
        if not api_key:
            messagebox.showerror("Error", "Please provide an API key")
            return
        
        prompt = self.prompt_text.get(1.0, tk.END).strip()
        if not prompt:
            messagebox.showerror("Error", "Please provide a prompt")
            return
        
        # Prepare request data
        data = {
            "model": self.model_var.get(),
            "prompt": prompt,
            "image": self.image_url_var.get(),
            "sequential_image_generation": self.sequential_var.get(),
            "response_format": self.response_format_var.get(),
            "size": self.size_var.get(),
            "stream": self.stream_var.get(),
            "watermark": self.watermark_var.get(),
            "safety": self.safety_var.get()
        }
        
        headers = {
            "Content-Type": "application/json",
            "Authorization": f"Bearer {api_key}"
        }
        
        # Disable button and update status
        self.generate_btn.config(state="disabled")
        self.status_var.set("Generating image...")
        self.root.update()
        
        try:
            # Make API request
            response = requests.post(
                "https://ark.ap-southeast.bytepluses.com/api/v3/images/generations",
                json=data,
                headers=headers,
                timeout=60
            )
            
            if response.status_code == 200:
                result = response.json()
                # Extract image URL from response
                if 'data' in result and len(result['data']) > 0:
                    image_url = result['data'][0].get('url', '')
                    if image_url:
                        self.result_url_var.set(image_url)
                        self.status_var.set("Image generated successfully!")
                        self.open_btn.config(state="normal")
                        
                        # Try to load and display the image
                        try:
                            self.load_image_preview(image_url)
                        except Exception as e:
                            print(f"Could not load image preview: {e}")
                    else:
                        self.status_var.set("No image URL in response")
                        messagebox.showwarning("Warning", "Image generated but no URL found in response")
                else:
                    self.status_var.set("Unexpected response format")
                    messagebox.showwarning("Warning", f"Unexpected response format: {result}")
            else:
                error_msg = f"API Error: {response.status_code}\n{response.text}"
                self.status_var.set(f"Error: {response.status_code}")
                messagebox.showerror("API Error", error_msg)
        
        except requests.exceptions.Timeout:
            self.status_var.set("Request timed out")
            messagebox.showerror("Error", "Request timed out. Please try again.")
        except requests.exceptions.RequestException as e:
            self.status_var.set("Request failed")
            messagebox.showerror("Error", f"Request failed: {str(e)}")
        except Exception as e:
            self.status_var.set("An error occurred")
            messagebox.showerror("Error", f"An error occurred: {str(e)}")
        finally:
            self.generate_btn.config(state="normal")
    
    def load_image_preview(self, image_url):
        """Load and display image preview"""
        try:
            # Validate URL scheme for security
            parsed_url = urlparse(image_url)
            if parsed_url.scheme not in ('http', 'https'):
                raise ValueError("Only HTTP and HTTPS URLs are allowed")
            
            # Download image with timeout
            req = Request(image_url, headers={'User-Agent': 'ARK-Image-Generator/1.0'})
            with urlopen(req, timeout=IMAGE_PREVIEW_TIMEOUT) as response:
                image_data = response.read()
            
            image = Image.open(io.BytesIO(image_data))
            
            # Resize image to fit in preview (max 600x400)
            max_width = 600
            max_height = 400
            image.thumbnail((max_width, max_height), Image.Resampling.LANCZOS)
            
            # Convert to PhotoImage
            photo = ImageTk.PhotoImage(image)
            
            # Update label
            self.image_label.config(image=photo, text="")
            self.image_label.image = photo  # Keep a reference
        except Exception as e:
            print(f"Error loading image preview: {e}")
            self.image_label.config(text="Could not load image preview")
    
    def open_in_browser(self):
        """Open generated image URL in browser"""
        url = self.result_url_var.get()
        if url:
            webbrowser.open(url)

def main():
    root = tk.Tk()
    app = ImageGeneratorApp(root)
    root.mainloop()

if __name__ == "__main__":
    main()
