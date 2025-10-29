import glob
import re

files = glob.glob('app/Http/Controllers/*.php')

for filepath in files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Fix escaped quotes
    content = content.replace(r"\'update\'", "'update'")
    content = content.replace(r"\'view\'", "'view'")
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Fixed: {filepath}")

print("\n✅ All files fixed!")
