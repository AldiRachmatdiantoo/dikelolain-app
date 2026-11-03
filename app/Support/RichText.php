<?php

namespace App\Support;

/**
 * Cleans the small amount of markup allowed in titles (rich_text settings).
 */
class RichText
{
    public const ALLOWED_TAGS = ['span', 'strong', 'em', 'br'];

    public static function clean(?string $html): string
    {
        $tags = implode('', array_map(fn ($tag) => "<{$tag}>", self::ALLOWED_TAGS));
        $html = strip_tags((string) $html, $tags);

        // Allowed tags keep no attributes (no onclick, style, ...).
        $names = implode('|', self::ALLOWED_TAGS);

        return preg_replace('/<(\/?)(' . $names . ')\b[^>]*?(\/?)>/i', '<$1$2$3>', $html);
    }
}
