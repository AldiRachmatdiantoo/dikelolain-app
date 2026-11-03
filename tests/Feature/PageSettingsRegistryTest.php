<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * config/page_settings.php: every default must be usable as it is.
 */
class PageSettingsRegistryTest extends TestCase
{
    public function test_local_image_and_video_defaults_exist(): void
    {
        $missing = collect(config('page_settings'))
            ->filter(fn ($setting) => in_array($setting['type'], ['image', 'video'], true))
            ->reject(fn ($setting) => preg_match('#^https?://#i', $setting['default']))
            ->reject(fn ($setting) => file_exists(public_path($setting['default'])))
            ->map(fn ($setting) => $setting['default'])
            ->all();

        $this->assertSame([], $missing);
    }
}
