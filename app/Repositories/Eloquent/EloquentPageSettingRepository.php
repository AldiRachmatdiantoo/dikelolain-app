<?php

namespace App\Repositories\Eloquent;

use App\Models\PageSetting;
use App\Repositories\Contracts\PageSettingRepositoryInterface;

class EloquentPageSettingRepository implements PageSettingRepositoryInterface
{
    public function all(): array
    {
        return PageSetting::pluck('value', 'key')->all();
    }

    public function put(string $key, ?string $value): void
    {
        PageSetting::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
