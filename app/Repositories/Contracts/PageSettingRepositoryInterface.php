<?php

namespace App\Repositories\Contracts;

interface PageSettingRepositoryInterface
{
    /**
     * Every stored value, keyed by setting key.
     *
     * @return array<string, string|null>
     */
    public function all(): array;

    public function put(string $key, ?string $value): void;
}
