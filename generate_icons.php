<?php

// Create 192x192 icon
$img192 = imagecreatetruecolor(192, 192);
$blue = imagecolorallocate($img192, 79, 70, 229);
$white = imagecolorallocate($img192, 255, 255, 255);

// Fill background
imagefilledrectangle($img192, 0, 0, 192, 192, $blue);

// Draw triangle
$triangle = [
    96, 40,   // top
    136, 120, // bottom right
    56, 120   // bottom left
];
imagefilledpolygon($img192, $triangle, $white);

// Add text
$font = 5; // Built-in font
imagestring($img192, $font, 82, 130, 'TS', $white);

// Save
imagepng($img192, 'public/icons/icon-192.png');
imagedestroy($img192);

echo "Created icon-192.png\n";

// Create 512x512 icon
$img512 = imagecreatetruecolor(512, 512);
$blue = imagecolorallocate($img512, 79, 70, 229);
$white = imagecolorallocate($img512, 255, 255, 255);

// Fill background
imagefilledrectangle($img512, 0, 0, 512, 512, $blue);

// Draw triangle
$triangle = [
    256, 100,   // top
    356, 300,   // bottom right
    156, 300    // bottom left
];
imagefilledpolygon($img512, $triangle, $white);

// Add text (larger)
imagestring($img512, $font, 240, 350, 'TEAMSPHERE', $white);

// Save
imagepng($img512, 'public/icons/icon-512.png');
imagedestroy($img512);

echo "Created icon-512.png\n";

// Create screenshots directory
if (!file_exists('public/screenshots')) {
    mkdir('public/screenshots', 0755, true);
}

// Create mobile screenshot placeholder
$mobile = imagecreatetruecolor(390, 844);
$bg = imagecolorallocate($mobile, 15, 23, 42);
$text = imagecolorallocate($mobile, 255, 255, 255);
imagefilledrectangle($mobile, 0, 0, 390, 844, $bg);
imagestring($mobile, $font, 100, 400, 'TeamSphere Mobile', $text);
imagepng($mobile, 'public/screenshots/mobile-dashboard.png');
imagedestroy($mobile);

echo "Created mobile-dashboard.png\n";

// Create desktop screenshot placeholder
$desktop = imagecreatetruecolor(1280, 720);
$bg = imagecolorallocate($desktop, 15, 23, 42);
$text = imagecolorallocate($desktop, 255, 255, 255);
imagefilledrectangle($desktop, 0, 0, 1280, 720, $bg);
imagestring($desktop, $font, 550, 350, 'TeamSphere Desktop', $text);
imagepng($desktop, 'public/screenshots/desktop-dashboard.png');
imagedestroy($desktop);

echo "Created desktop-dashboard.png\n";

echo "\nAll PWA assets created successfully!\n";
