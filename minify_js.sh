#!/bin/bash

# JavaScript Minification Script for Magento Theme
echo "🚀 Starting JavaScript minification..."

# Find all JavaScript files that are not already minified
find /Users/naserodeh/Desktop/rivage/rivageskincare/rivageae_live_copy/pub/static/frontend/Rivage/theme/en_US/ -name "*.js" ! -name "*.min.js" | while read js_file; do
    # Get the directory and filename
    dir=$(dirname "$js_file")
    filename=$(basename "$js_file" .js)
    
    # Create minified filename
    minified_file="$dir/${filename}.min.js"
    
    # Skip if minified file already exists
    if [ -f "$minified_file" ]; then
        echo "⏭️  Skipping $js_file (minified version exists)"
        continue
    fi
    
    # Get original file size
    original_size=$(wc -c < "$js_file")
    
    # Skip very small files (less than 1KB)
    if [ $original_size -lt 1024 ]; then
        echo "⏭️  Skipping $js_file (too small: $(wc -c < "$js_file") bytes)"
        continue
    fi
    
    # Minify the JavaScript file
    if terser "$js_file" -o "$minified_file" --compress --mangle 2>/dev/null; then
        # Get minified file size
        minified_size=$(wc -c < "$minified_file")
        
        # Calculate reduction percentage
        reduction=$(( (original_size - minified_size) * 100 / original_size ))
        
        echo "✅ Minified: $js_file"
        echo "   📊 Size: $(wc -c < "$js_file") → $(wc -c < "$minified_file") bytes ($reduction% reduction)"
    else
        echo "❌ Failed to minify: $js_file"
    fi
done

echo "🎉 JavaScript minification complete!"
