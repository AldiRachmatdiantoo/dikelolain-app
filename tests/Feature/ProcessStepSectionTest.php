<?php

namespace Tests\Feature;

use App\Models\PageSetting;
use App\Models\ProcessStep;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\ProcessStepSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * "How we work" section on Home.
 */
class ProcessStepSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_steps_are_numbered_and_only_the_first_is_open(): void
    {
        $this->seed(ProcessStepSeeder::class);

        $html = $this->get('/')
            ->assertOk()
            ->assertSee('Our methodology for <span>success</span>', false)
            ->assertSee(asset('images/how-it-work-image.jpg'), false)
            ->assertSeeInOrder(['01. Discovery &amp; Strategy', '02. Concept Development', '04. Development &amp; Execution'], false)
            ->getContent();

        $this->assertSame(4, substr_count($html, 'class="work-accordion-item'));
        $this->assertSame(1, substr_count($html, 'accordion-collapse collapse show'));
    }

    public function test_numbers_follow_the_order_and_hidden_steps_are_skipped(): void
    {
        ProcessStep::create(['title' => 'Survei', 'description' => 'Datang ke lokasi.', 'sort_order' => 1]);
        ProcessStep::create(['title' => 'Langkah Rahasia', 'description' => 'x', 'sort_order' => 2, 'is_active' => false]);
        ProcessStep::create(['title' => 'Pengerjaan', 'description' => 'Dikerjakan tim.', 'sort_order' => 3]);

        $this->get('/')
            ->assertSee('01. Survei')
            ->assertSee('02. Pengerjaan')
            ->assertDontSee('Langkah Rahasia');

        $this->actingAs(User::factory()->create())
            ->withSession([EditMode::SESSION_KEY => true])
            ->get('/')
            ->assertSee('02. Langkah Rahasia')
            ->assertSee('+ Tambah langkah');
    }

    public function test_settings_and_escaping(): void
    {
        PageSetting::create(['key' => 'process.cta_link', 'value' => 'https://wa.me/628123']);
        ProcessStep::create(['title' => '<b>Tebal</b>', 'description' => '<script>alert(1)</script>', 'sort_order' => 1]);

        $this->get('/')
            ->assertSee('href="https://wa.me/628123"', false)
            ->assertSee('&lt;b&gt;Tebal&lt;/b&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
