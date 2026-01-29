<?php
/**
 * CAPTCHA Image Generator
 */
session_start();

// Generate random CAPTCHA code
function generateCaptcha($length = 6) {
    $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $captcha = '';
    for ($i = 0; $i < $length; $i++) {
        $captcha .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $captcha;
}

// Create CAPTCHA code and store in session
$captchaCode = generateCaptcha();
$_SESSION['captcha_code'] = $captchaCode;

// Create image
$width = 150;
$height = 50;
$image = imagecreatetruecolor($width, $height);

// Colors
$bgColor = imagecolorallocate($image, 248, 249, 250);
$textColor = imagecolorallocate($image, 51, 51, 51);
$lineColor = imagecolorallocate($image, 200, 200, 200);
$noiseColor = imagecolorallocate($image, 150, 150, 150);

// Fill background
imagefill($image, 0, 0, $bgColor);

// Add noise lines
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
}

// Add noise dots
for ($i = 0; $i < 100; $i++) {
    imagesetpixel($image, rand(0, $width), rand(0, $height), $noiseColor);
}

// Add text
$fontSize = 20;
$x = 20;
$y = 35;

for ($i = 0; $i < strlen($captchaCode); $i++) {
    $angle = rand(-15, 15);
    imagettftext($image, $fontSize, $angle, $x, $y, $textColor, __DIR__ . '/arial.ttf', $captchaCode[$i]);
    $x += 20;
}

// If font file doesn't exist, use built-in font
if (!file_exists(__DIR__ . '/arial.ttf')) {
    $x = 20;
    for ($i = 0; $i < strlen($captchaCode); $i++) {
        imagestring($image, 5, $x, 15, $captchaCode[$i], $textColor);
        $x += 20;
    }
}

// Output image
header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

imagepng($image);
imagedestroy($image);
