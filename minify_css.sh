#!/bin/bash

# CSS Minification Script for Magento Theme
echo "🎨 Starting CSS minification..."

# Find all CSS files that are not already minified
find /Users/naserodeh/Desktop/rivage/rivageskincare/rivageae_live_copy/pub/static/frontend/Rivage/theme/en_US/ -name "*.css" ! -name "*.min.css" | while read css_file; do
    # Get the directory and filename
    dir=$(dirname "$css_file")
    filename=$(basename "$css_file" .css)
    
    # Create minified filename
    minified_file="$dir/${filename}.min.css"
    
    # Skip if minified file already exists
    if [ -f "$minified_file" ]; then
        echo "⏭️  Skipping $css_file (minified version exists)"
        continue
    fi
    
    # Get original file size
    original_size=$(wc -c < "$css_file")
    
    # Minify the CSS file
    if cleancss -o "$minified_file" "$css_file" 2>/dev/null; then
        # Get minified file size
        minified_size=$(wc -c < "$minified_file")
        
        # Calculate reduction percentage
        reduction=$(( (original_size - minified_size) * 100 / original_size ))
        
        echo "✅ Minified: $css_file"
        echo "   📊 Size: $(numfmt --to=iec $original_size) → $(numfmt --to=iec $minified_size) ($reduction% reduction)"
    else
        echo "❌ Failed to minify: $css_file"
    fi
done

echo "🎉 CSS minification complete!"
