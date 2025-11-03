# PWA Icons

This directory contains the Progressive Web App icons for TeamSphere.

## Required Icon Sizes

The following icon sizes are needed for optimal PWA support:

- 72x72 (icon-72x72.png)
- 96x96 (icon-96x96.png)
- 128x128 (icon-128x128.png)
- 144x144 (icon-144x144.png)
- 152x152 (icon-152x152.png)
- 192x192 (icon-192x192.png)
- 384x384 (icon-384x384.png)
- 512x512 (icon-512x512.png)

## How to Generate Icons

You can use online tools to generate these icons from a single high-resolution image:

1. **PWA Asset Generator** - https://www.pwabuilder.com/imageGenerator
2. **Favicon Generator** - https://realfavicongenerator.net/
3. **PWA Builder** - https://www.pwabuilder.com/

### Using ImageMagick (Command Line)

If you have ImageMagick installed, you can generate all sizes from a single PNG file:

```bash
# From a 512x512 source image
convert source.png -resize 72x72 icon-72x72.png
convert source.png -resize 96x96 icon-96x96.png
convert source.png -resize 128x128 icon-128x128.png
convert source.png -resize 144x144 icon-144x144.png
convert source.png -resize 152x152 icon-152x152.png
convert source.png -resize 192x192 icon-192x192.png
convert source.png -resize 384x384 icon-384x384.png
convert source.png -resize 512x512 icon-512x512.png
```

## Temporary Solution

Until custom icons are created, the favicon.ico will be used as a fallback. For the best user experience, please generate proper PWA icons as soon as possible.
