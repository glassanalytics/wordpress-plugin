#!/bin/bash

# Path to your plugin directory
PLUGIN_DIR="/Users/linkly/Desktop/wordpress-plugin"
README_TXT="$PLUGIN_DIR/readme.txt"
README_MD="$PLUGIN_DIR/README.md"

# Check if readme.txt exists
if [ ! -f "$README_TXT" ]; then
  echo "Error: readme.txt not found at $README_TXT"
  exit 1
fi

# Create the README.md file
echo "# Glass Analytics

A WordPress plugin for website analytics tracking and reporting.
" > "$README_MD"

# Convert WordPress headings to Markdown and append to README.md
cat "$README_TXT" | sed 's/=== \(.*\) ===/# \1/g' | \
                     sed 's/== \(.*\) ==/## \1/g' | \
                     sed 's/= \(.*\) =/### \1/g' | \
                     sed 's/^\* /- /g' >> "$README_MD"

echo "Successfully converted readme.txt to README.md"