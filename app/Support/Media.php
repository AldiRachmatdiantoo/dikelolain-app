<?php

namespace App\Support;

/**
 * Turns a stored image/video value into a URL.
 *
 * Values are either a full URL (https://...), a template asset under public/
 * (images/hero-img-1.jpg) or an upload (storage/uploads/...); the last two are
 * both served from public/, so asset() covers them.
 */
class Media
{
    public static function url(?string $value): string
    {
        if (blank($value)) {
            return '';
        }

        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }

        return asset($value);
    }
}
