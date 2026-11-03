<?php

namespace Tests\Feature;

use App\Models\PricingFeature;
use App\Models\PricingPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class PricingFeatureApiTest extends TestCase
{
    use ListApiContract;
    use RefreshDatabase;

    private ?PricingPlan $plan = null;

    private function plan(): PricingPlan
    {
        return $this->plan ??= PricingPlan::create(['name' => 'Basic', 'price' => 'Rp 100 Rb']);
    }

    protected function listUri(): string
    {
        return '/admin/api/pricing-features';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return PricingFeature::create(['pricing_plan_id' => $this->plan()->id, 'text' => "Fitur {$sortOrder}", 'sort_order' => $sortOrder, ...$attributes]);
    }

    protected function validPayload(): array
    {
        return ['pricing_plan_id' => $this->plan()->id, 'text' => 'Cek rem', 'is_active' => true];
    }

    protected function requiredField(): array
    {
        return ['text', 'Cuci motor'];
    }

    public function test_features_are_ordered_per_plan_and_need_an_existing_plan(): void
    {
        $this->makeItem(4);
        $other = PricingPlan::create(['name' => 'Premium', 'price' => 'Rp 1 Jt']);
        $this->actingAs(User::factory()->create());

        $this->postJson($this->listUri(), [...$this->validPayload(), 'pricing_plan_id' => $other->id])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);

        $this->postJson($this->listUri(), [...$this->validPayload(), 'pricing_plan_id' => 999])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['pricing_plan_id' => 'Paket harga tidak ditemukan.']);
    }
}
