const fs = require('fs');
const path = require('path');

// Simple function to create a colored square PNG icon
// This creates a basic icon without external dependencies
const createBasicIcon = (size, filepath) => {
    // Create a simple SVG as base64
    const svg = `
    <svg width="${size}" height="${size}" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
            </linearGradient>
        </defs>
        <rect width="${size}" height="${size}" rx="20%" fill="url(#grad)"/>
        <text x="50%" y="50%" font-family="Arial" font-size="${size * 0.4}" fill="white" text-anchor="middle" dominant-baseline="middle" font-weight="bold">TS</text>
    </svg>
    `;
    
    // For now, just create the SVG files
    // In production, you'd convert these to PNG
    fs.writeFileSync(filepath.replace('.png', '.svg'), svg);
    console.log(`Created ${filepath.replace('.png', '.svg')}`);
};

const iconDir = path.join(__dirname, 'public', 'icons');
const sizes = [72, 96, 128, 144, 152, 192, 384, 512];

// Ensure icons directory exists
if (!fs.existsSync(iconDir)) {
    fs.mkdirSync(iconDir, { recursive: true });
}

console.log('Generating TeamSphere PWA icons...\n');

sizes.forEach(size => {
    const filepath = path.join(iconDir, `icon-${size}x${size}.png`);
    createBasicIcon(size, filepath);
});

console.log('\n✓ SVG icons created!');
console.log('\nNote: For production, convert SVG to PNG using:');
console.log('- Online tool: https://www.pwabuilder.com/imageGenerator');
console.log('- Or install sharp: npm install sharp');
