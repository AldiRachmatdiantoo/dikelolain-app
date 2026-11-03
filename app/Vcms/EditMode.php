<?php

namespace App\Vcms;

/**
 * Whether the current visitor is an admin editing the public pages in place.
 */
class EditMode
{
    public const SESSION_KEY = 'vcms.edit_mode';

    public static function active(): bool
    {
        return auth()->check() && session(self::SESSION_KEY, false) === true;
    }

    public static function enable(): void
    {
        session([self::SESSION_KEY => true]);
    }

    public static function disable(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}
