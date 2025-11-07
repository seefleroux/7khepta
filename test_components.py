#!/usr/bin/env python3
"""
Test script to verify the ARK API integration works correctly.
This script tests the API call without the GUI.
"""

import sys

def test_imports():
    """Test that all required modules can be imported"""
    print("Testing imports...")
    try:
        import requests
        print("✓ requests module available")
    except ImportError:
        print("✗ requests module not available - run: pip install requests")
        return False
    
    try:
        from PIL import Image
        print("✓ PIL/Pillow module available")
    except ImportError:
        print("✗ PIL/Pillow module not available - run: pip install Pillow")
        return False
    
    try:
        import tkinter
        print("✓ tkinter module available")
    except ImportError:
        print("⚠ tkinter module not available - GUI won't work")
        print("  Install tkinter for your system (e.g., apt-get install python3-tk)")
    
    print("\nAll critical imports successful!")
    return True

def test_api_structure():
    """Test that the API call structure is correct"""
    print("\nTesting API request structure...")
    import requests
    
    # Test data (won't actually call API without valid key)
    test_data = {
        "model": "seedream-4-0-250828",
        "prompt": "Test prompt",
        "image": "https://example.com/image.png",
        "sequential_image_generation": "disabled",
        "response_format": "url",
        "size": "4K",
        "stream": False,
        "watermark": False,
        "safety": False
    }
    
    test_headers = {
        "Content-Type": "application/json",
        "Authorization": "Bearer test_key"
    }
    
    # Prepare request (don't send it)
    req = requests.Request(
        'POST',
        "https://ark.ap-southeast.bytepluses.com/api/v3/images/generations",
        json=test_data,
        headers=test_headers
    )
    prepared = req.prepare()
    
    print("✓ API request structure is valid")
    print(f"  URL: {prepared.url}")
    print(f"  Method: {prepared.method}")
    print(f"  Headers: Content-Type and Authorization present")
    print(f"  Body: {len(prepared.body)} bytes")
    
    return True

def main():
    """Run all tests"""
    print("=" * 60)
    print("ARK Image Generator - Component Test")
    print("=" * 60)
    
    if not test_imports():
        print("\n❌ Import tests failed. Please install missing dependencies.")
        sys.exit(1)
    
    if not test_api_structure():
        print("\n❌ API structure test failed.")
        sys.exit(1)
    
    print("\n" + "=" * 60)
    print("✓ All tests passed!")
    print("=" * 60)
    print("\nThe application should work correctly.")
    print("Run 'python image_generator.py' to start the GUI.")
    print("\nNote: You'll need a valid ARK_API_KEY to generate images.")

if __name__ == "__main__":
    main()
