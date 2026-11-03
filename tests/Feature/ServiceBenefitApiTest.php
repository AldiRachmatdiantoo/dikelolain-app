<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceBenefit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class ServiceBenefitApiTest extends TestCase
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
        return '/admin/api/service-benefits';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return ServiceBenefit::create(['service_id' => $this->service()->id, 'text' => "Manfaat {$sortOrder}", 'sort_order' => $sortOrder, ...$attributes]);
    }

    protected function validPayload(): array
    {
        return ['service_id' => $this->service()->id, 'text' => 'Bergaransi', 'is_active' => true];
    }

    protected function requiredField(): array
    {
        return ['text', 'Murah'];
    }

    public function test_benefits_are_ordered_per_service_and_need_an_existing_service(): void
    {
        $this->makeItem(4);
        $other = Service::create(['slug' => 'cuci', 'name' => 'Cuci', 'summary' => 'x', 'image' => 'images/service-image-2.jpg']);
        $this->actingAs(User::factory()->create());

        $this->postJson($this->listUri(), [...$this->validPayload(), 'service_id' => $other->id])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);

        $this->postJson($this->listUri(), [...$this->validPayload(), 'service_id' => 999])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['service_id' => 'Layanan tidak ditemukan.']);
    }
}
