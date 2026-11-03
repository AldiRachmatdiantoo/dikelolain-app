<?php

namespace Tests\Feature;

use App\Models\ProcessStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class ProcessStepApiTest extends TestCase
{
    use ListApiContract;
    use RefreshDatabase;

    protected function listUri(): string
    {
        return '/admin/api/process-steps';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return ProcessStep::create([
            'title' => "Langkah {$sortOrder}",
            'description' => 'Penjelasan langkah.',
            'sort_order' => $sortOrder,
            ...$attributes,
        ]);
    }

    protected function validPayload(): array
    {
        return ['title' => 'Survei lokasi', 'description' => 'Kami datang ke bengkel Anda.', 'is_active' => true];
    }

    protected function requiredField(): array
    {
        return ['description', 'Penjelasan baru.'];
    }
}
