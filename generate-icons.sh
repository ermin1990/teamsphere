#!/bin/bash

# Generate PWA icons from favicon using ImageMagick or fallback to copying favicon

ICON_DIR="/Users/batz-tuzla-backup/Laravel/teamsphere/public/icons"
FAVICON="/Users/batz-tuzla-backup/Laravel/teamsphere/public/favicon.ico"

# Check if ImageMagick is installed
if command -v convert &> /dev/null; then
    echo "ImageMagick found, generating PNG icons from favicon..."
    
    # Generate all required sizes
    convert "$FAVICON" -resize 72x72 "$ICON_DIR/icon-72x72.png"
    convert "$FAVICON" -resize 96x96 "$ICON_DIR/icon-96x96.png"
    convert "$FAVICON" -resize 128x128 "$ICON_DIR/icon-128x128.png"
    convert "$FAVICON" -resize 144x144 "$ICON_DIR/icon-144x144.png"
    convert "$FAVICON" -resize 152x152 "$ICON_DIR/icon-152x152.png"
    convert "$FAVICON" -resize 192x192 "$ICON_DIR/icon-192x192.png"
    convert "$FAVICON" -resize 384x384 "$ICON_DIR/icon-384x384.png"
    convert "$FAVICON" -resize 512x512 "$ICON_DIR/icon-512x512.png"
    
    echo "✓ Icons generated successfully!"
else
    echo "ImageMagick not found. Creating placeholder icons..."
    echo "Please install ImageMagick: brew install imagemagick"
    echo "Or use online tool: https://www.pwabuilder.com/imageGenerator"
fi
