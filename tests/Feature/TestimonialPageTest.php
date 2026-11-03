<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\TestimonialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_testimonials_are_shown_in_order(): void
    {
        $this->seed(TestimonialSeeder::class);

        $this->get('/testimonials')
            ->assertOk()
            ->assertSeeInOrder(['eleanor pena', 'esther howard', 'jane cooper'])
            ->assertSee(asset('images/author-1.jpg'), false)
            ->assertDontSee('data-vcms', false);
    }

    public function test_hidden_testimonials_only_show_in_edit_mode(): void
    {
        Testimonial::create(['name' => 'Tampil', 'content' => 'A', 'rating' => 3, 'sort_order' => 1]);
        Testimonial::create(['name' => 'Rahasia', 'content' => 'B', 'rating' => 5, 'sort_order' => 2, 'is_active' => false]);

        $this->get('/testimonials')->assertSee('Tampil')->assertDontSee('Rahasia');

        $this->actingAs(User::factory()->create())
            ->withSession([EditMode::SESSION_KEY => true])
            ->get('/testimonials')
            ->assertSee('Rahasia')
            ->assertSee('vcms-hidden')
            ->assertSee('+ Tambah testimoni');
    }

    public function test_rating_and_missing_images(): void
    {
        Testimonial::create(['name' => 'Tanpa Foto', 'content' => '<b>isi</b>', 'rating' => 3, 'sort_order' => 1]);

        $response = $this->get('/testimonials')->assertOk()->assertSee('&lt;b&gt;isi&lt;/b&gt;', false);

        // 3 of 5 stars lit, no empty <img src="">.
        $this->assertSame(2, substr_count($response->getContent(), 'fa-star star-empty'));
        $this->assertStringNotContainsString('src=""', $response->getContent());
    }
}
