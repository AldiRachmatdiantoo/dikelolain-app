<?php

namespace Tests\Feature;

use App\Models\PricingPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class PricingPlanApiTest extends TestCase
{
    use ListApiContract;
    use RefreshDatabase;

    protected function listUri(): string
    {
        return '/admin/api/pricing-plans';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return PricingPlan::create(['name' => "Paket {$sortOrder}", 'price' => 'Rp 100 Rb', 'sort_order' => $sortOrder, ...$attributes]);
    }

    protected function validPayload(): array
    {
        return ['name' => 'Paket Servis', 'price' => 'Rp 250 Rb', 'period' => '/bulan', 'is_featured' => true, 'button_text' => 'Pesan', 'button_link' => '/contact', 'is_active' => true];
    }

    protected function requiredField(): array
    {
        return ['price', 'Rp 1 Jt'];
    }

    public function test_button_link_accepts_site_paths_and_web_addresses_only(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['/contact', '#', 'https://wa.me/62812', 'mailto:info@example.com', 'tel:+62812'] as $link) {
            $this->postJson($this->listUri(), [...$this->validPayload(), 'button_link' => $link])->assertCreated();
        }

        $this->postJson($this->listUri(), [...$this->validPayload(), 'button_link' => 'javascript:alert(1)'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['button_link' => 'Link harus diawali http(s)://, /, #, mailto: atau tel:.']);
    }

    public function test_deleting_a_plan_deletes_its_features(): void
    {
        $this->actingAs(User::factory()->create());
        $plan = $this->makeItem(1);
        $plan->features()->create(['text' => 'Ganti oli']);

        $this->deleteJson("{$this->listUri()}/{$plan->id}")->assertNoContent();

        $this->assertDatabaseCount('pricing_features', 0);
    }
}
