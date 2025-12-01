<?php

namespace App\Helpers;

class EmojiHelper
{
    /**
     * Convert emoji Unicode to Twemoji image tag for PDF
     * Uses Twemoji CDN which provides SVG emoji images
     */
    public static function emojiToImageTag(?string $emoji, int $size = 14): string
    {
        if (empty($emoji) || trim($emoji) === '') {
            return '';
        }

        $emoji = trim($emoji);

        // Convert emoji to Unicode code points
        $codes = [];
        $chars = mb_str_split($emoji, 1, 'UTF-8');

        foreach ($chars as $char) {
            $code = mb_ord($char, 'UTF-8');
            if ($code) {
                // Include all relevant Unicode ranges for emojis
                if ($code >= 0x1F300 && $code <= 0x1F9FF) { // Misc Symbols and Pictographs
                    $codes[] = sprintf('%x', $code);
                } elseif ($code >= 0x2600 && $code <= 0x26FF) { // Misc Symbols
                    $codes[] = sprintf('%x', $code);
                } elseif ($code >= 0x2700 && $code <= 0x27BF) { // Dingbats
                    $codes[] = sprintf('%x', $code);
                } elseif ($code >= 0x1F600 && $code <= 0x1F64F) { // Emoticons
                    $codes[] = sprintf('%x', $code);
                } elseif ($code >= 0x1F680 && $code <= 0x1F6FF) { // Transport and Map
                    $codes[] = sprintf('%x', $code);
                } elseif ($code >= 0x1F1E0 && $code <= 0x1F1FF) { // Regional Indicator Symbols
                    $codes[] = sprintf('%x', $code);
                } elseif ($code == 0x200D) { // Zero Width Joiner - skip but continue
                    continue;
                } elseif ($code == 0xFE0F) { // Variation Selector-16 - skip but continue
                    continue;
                }
            }
        }

        if (empty($codes)) {
            return '';
        }

        // Use Twemoji CDN - it provides SVG emoji images
        $unicode = implode('-', $codes);
        $imageUrl = "https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/{$unicode}.svg";

        return sprintf(
            '<img src="%s" style="width: %dpx; height: %dpx; vertical-align: middle; display: inline-block;" />',
            htmlspecialchars($imageUrl),
            $size,
            $size
        );
    }
}
