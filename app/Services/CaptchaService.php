<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;


class CaptchaService
{
    /**
     * The character set for the CAPTCHA text.
     * Excludes ambiguous characters like 0, O, 1, I, l.
     */
    private const CHARS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * The length of the CAPTCHA string.
     */
    private const LENGTH = 6;

    /**
     * Generate a new CAPTCHA, store it in the session, and return the SVG image.
     *
     * @return string The raw SVG markup for the CAPTCHA image.
     */

    public function generate(): string
    {
        $captchaText = '';
        $charLength = strlen(self::CHARS);

        // 1. Generate the random text
        for ($i = 0; $i < self::LENGTH; $i++) {
            $captchaText .= self::CHARS[random_int(0, $charLength - 1)];
        }

        // 2. Store the plain text in the session for later validation
        // The key is case-insensitive for user-friendliness
        Session::put('captcha', $captchaText);

        // 3. Generate the SVG image
        return $this->createSvg($captchaText);
    }

    /**
     * Validate the user's input against the stored CAPTCHA text.
     *
     * @param string|null $userInput The text entered by the user.
     * @return bool True if the input is valid, false otherwise.
     */
    public function validate(?string $userInput): bool
    {
        // Get the stored CAPTCHA text from the session
        $storedCaptcha = Session::get('captcha');

        // Immediately forget the session key to prevent replay attacks
        Session::forget('captcha');

        if (!$userInput || !$storedCaptcha) {
            return false;
        }

        // Perform a case-insensitive comparison
        return strcasecmp($userInput, $storedCaptcha) === 0;
    }
    /**
     * Creates the SVG markup for the given text.
     *
     * @param string $text The CAPTCHA text.
     * @return string The raw SVG markup.
     */
    private function createSvg(string $text): string
    {
        $width = 150;
        $height = 50;
        $font_size = 24;

        $svg = "<svg width='{$width}' height='{$height}' xmlns='http://www.w3.org/2000/svg' style='background-color: #f0f0f0; border-radius: 5px;'>";

        // Add some random noise lines to make it harder for bots
        for ($i = 0; $i < 5; $i++) {
            $x1 = random_int(0, $width);
            $y1 = random_int(0, $height);
            $x2 = random_int(0, $width);
            $y2 = random_int(0, $height);
            $color = "rgb(" . random_int(150, 220) . "," . random_int(150, 220) . "," . random_int(150, 220) . ")";
            $svg .= "<line x1='{$x1}' y1='{$y1}' x2='{$x2}' y2='{$y2}' stroke='{$color}' stroke-width='1'/>";
        }

        // Add the text characters with random transformations
        $letter_spacing = 22;
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            $x = 15 + ($i * $letter_spacing);
            $y = random_int($font_size, $height - 5);
            $angle = random_int(-15, 15);
            $color = "rgb(" . random_int(0, 100) . "," . random_int(0, 100) . "," . random_int(0, 100) . ")";

            $svg .= "<text x='{$x}' y='{$y}' font-size='{$font_size}' font-family='Arial, sans-serif' font-weight='bold' fill='{$color}' transform='rotate({$angle}, {$x}, {$y})'>{$char}</text>";
        }

        $svg .= "</svg>";

        return $svg;
    }
}
