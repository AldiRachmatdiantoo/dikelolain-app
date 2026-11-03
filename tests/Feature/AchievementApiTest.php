<?php

namespace Tests\Feature;

use App\Models\Achievement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class AchievementApiTest extends TestCase
{
    use ListApiContract;
    use RefreshDatabase;

    protected function listUri(): string
    {
        return '/admin/api/achievements';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return Achievement::create([
            'period' => '2024',
            'title' => "Pencapaian {$sortOrder}",
            'sort_order' => $sortOrder,
            ...$attributes,
        ]);
    }

    protected function validPayload(): array
    {
        return ['period' => '2023 - 2024', 'title' => 'Bengkel Terbaik', 'description' => 'Penghargaan kota.', 'is_active' => true];
    }

    protected function requiredField(): array
    {
        return ['period', '2025'];
    }
}
