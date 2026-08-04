<?php
$sourcePath = 'C:\Users\Rendy Raihandanie\.gemini\antigravity\brain\ab2b2432-204c-4b15-8b6f-26738f5d66ad\airs_logo_light_1785731395798.jpg';
$outPathPng = 'c:\laragon\www\petgym\public\images\logo.png';
$outPathDarkPng = 'c:\laragon\www\petgym\public\images\logo-dark.png';

$im = imagecreatefromjpeg($sourcePath);
$width = imagesx($im);
$height = imagesy($im);

// Create transparent truecolor image
$transparentPng = imagecreatetruecolor($width, $height);
imagealphablending($transparentPng, false);
imagesavealpha($transparentPng, true);

// Transparent color fill
$transparentColor = imagecolorallocatealpha($transparentPng, 0, 0, 0, 127);
imagefill($transparentPng, 0, 0, $transparentColor);

// Process white background removal with anti-aliasing
for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        // Calculate brightness / distance from white (255, 255, 255)
        $brightness = ($r + $g + $b) / 3.0;

        // Flood-like check: if pixel is close to outer white
        if ($r > 240 && $g > 240 && $b > 240) {
            // Fully transparent
            $color = imagecolorallocatealpha($transparentPng, 255, 255, 255, 127);
        } elseif ($r > 210 && $g > 210 && $b > 210) {
            // Feathered edge for smooth anti-aliasing
            $alpha = (int) (127 * ($brightness - 210) / (255 - 210));
            $color = imagecolorallocatealpha($transparentPng, $r, $g, $b, min(127, max(0, $alpha)));
        } else {
            // Retain original color
            $color = imagecolorallocatealpha($transparentPng, $r, $g, $b, 0);
        }
        imagesetpixel($transparentPng, $x, $y, $color);
    }
}

imagepng($transparentPng, $outPathPng);
imagepng($transparentPng, $outPathDarkPng);

echo "Transparent PNG generated successfully at $outPathPng\n";
