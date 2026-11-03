<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceFaq;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class ServiceFaqApiTest extends TestCase
{
    use ListApiContract;
    use RefreshDatabase;

    private ?Service $service = null;

    private function service(): Service
    {
        return $this->service ??= Service::create(['slug' => 'servis', 'name' => 'Servis', 'summary' => 'x', 'image' => 'images/service-image-1.jpg']);
    }

    protected function listUri(): string
    {
        return '/admin/api/service-faqs';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return ServiceFaq::create(['service_id' => $this->service()->id, 'question' => "Tanya {$sortOrder}?", 'answer' => 'Jawab.', 'sort_order' => $sortOrder, ...$attributes]);
    }

    protected function validPayload(): array
    {
        return ['service_id' => $this->service()->id, 'question' => 'Berapa lama servis?', 'answer' => 'Sekitar satu jam.', 'is_active' => true];
    }

    protected function requiredField(): array
    {
        return ['answer', 'Dua jam.'];
    }
}
