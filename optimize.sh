#!/bin/bash

# Set directory to optimize
DIR=storage/app/public

# Set optimization level for PNG
PNG_OPT_LEVEL=7

# Set quality for JPEG (1-100, where 100 is best quality)
JPEG_QUALITY=65

# Optimize JPEG/JPG files
find $DIR -type f \( -iname "*.jpg" -o -iname "*.jpeg" \) -exec jpegoptim --max=$JPEG_QUALITY --strip-all --all-progressive {} \;

# Optimize PNG files
find $DIR -type f -iname "*.png" -exec optipng -o$PNG_OPT_LEVEL {} \;

echo "Image optimization complete!"
