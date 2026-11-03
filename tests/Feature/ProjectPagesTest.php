<?php

namespace Tests\Feature;

use App\Models\PageSetting;
use App\Models\Project;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\ProjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Our work section on Home, the Portfolio page and the slider experiment.
 */
class ProjectPagesTest extends TestCase
{
    use RefreshDatabase;

    private function video(int $sortOrder, array $attributes = []): Project
    {
        return Project::create([
            'title' => "Video {$sortOrder}",
            'media_type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'sort_order' => $sortOrder,
            ...$attributes,
        ]);
    }

    private function design(int $sortOrder, array $attributes = []): Project
    {
        return Project::create([
            'title' => "Desain {$sortOrder}",
            'media_type' => 'image',
            'image' => 'images/work-image-1.jpg',
            'sort_order' => $sortOrder,
            ...$attributes,
        ]);
    }

    private function editor(): static
    {
        return $this->actingAs(User::factory()->create())->withSession([EditMode::SESSION_KEY => true]);
    }

    public function test_home_shows_six_active_videos_then_six_active_designs(): void
    {
        $this->design(1);
        foreach (range(2, 9) as $order) {
            $this->video($order);
        }
        $this->video(10, ['title' => 'Video Rahasia', 'is_active' => false]);
        foreach (range(11, 18) as $order) {
            $this->design($order);
        }
        PageSetting::create(['key' => 'work.title', 'value' => 'Karya <span>terbaru</span>']);

        $html = $this->get('/')
            ->assertOk()
            ->assertSee('Karya <span>terbaru</span>', false)
            ->assertSeeInOrder(['Video 2', 'Video 7', 'Desain 1', 'Desain 15'])
            ->assertDontSee('Video 8')
            ->assertDontSee('Desain 16')
            ->assertDontSee('Video Rahasia')
            ->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', false)
            ->getContent();

        $this->assertSame(12, substr_count($html, 'class="work-item wow fadeInUp'));
    }

    public function test_portfolio_lists_projects_with_filter_counts_and_edit_mode_shows_hidden_ones(): void
    {
        $this->seed(ProjectSeeder::class);
        $this->design(30, ['title' => 'Desain Rahasia', 'is_active' => false]);

        $html = $this->get('/portfolio')
            ->assertOk()
            ->assertSee('Our <span>portfolio</span>', false)
            ->assertSee('All projects <span class="count-badge">18</span>', false)
            ->assertSee('Video <span class="count-badge">12</span>', false)
            ->assertSee('Design <span class="count-badge">6</span>', false)
            ->assertDontSee('Desain Rahasia')
            ->getContent();
        $this->assertSame(12, substr_count($html, 'portfolio-item" data-page="1"'));
        $this->assertSame(6, substr_count($html, 'portfolio-item" data-page="2"'));
        $this->assertSame(6, substr_count($html, 'work-item-box image portfolio-item'));

        $this->editor()->get('/portfolio')
            ->assertSee('Desain Rahasia')
            ->assertSee('+ Tambah project');
    }

    public function test_experiment_uses_the_same_projects(): void
    {
        $this->video(1, ['title' => 'Video Satu']);
        $this->design(2, ['title' => 'Desain Dua', 'image' => 'images/work-image-2.jpg']);
        $this->design(3, ['title' => 'Desain Rahasia', 'is_active' => false]);

        $html = $this->get('/portfolio-experiment')
            ->assertOk()
            ->assertSee('Portfolio Experiment')
            ->assertSeeInOrder(['Video Satu', 'Desain Dua'])
            ->assertSee(asset('images/work-image-2.jpg'), false)
            ->assertDontSee('Desain Rahasia')
            ->getContent();
        $this->assertSame(2, substr_count($html, 'class="swiper-slide portfolio-slide'));
    }

    public function test_modal_data_is_escaped_and_carries_server_built_media(): void
    {
        $this->design(1, [
            'title' => '<img src=x onerror=alert(1)>',
            'description' => '"><script>alert(2)</script>',
            'link' => 'https://example.com/karya?a=1&b=2',
        ]);

        $this->get('/portfolio')
            ->assertDontSee('<img src=x onerror=alert(1)>', false)
            ->assertDontSee('<script>alert(2)</script>', false)
            ->assertSee('data-title="&lt;img src=x onerror=alert(1)&gt;"', false)
            ->assertSee('data-link="https://example.com/karya?a=1&amp;b=2"', false)
            ->assertSee('data-category="Design"', false)
            ->assertSee('data-media-src="' . asset('images/work-image-1.jpg') . '"', false);
    }

    public function test_empty_description_and_link_render_nothing(): void
    {
        $this->video(1, ['description' => null, 'link' => null]);

        $this->get('/portfolio')
            ->assertSee('data-link=""', false)
            ->assertDontSee('<p></p>', false)
            ->assertSee('data-media-src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ"', false);
    }

    public function test_home_items_cannot_be_moved_but_portfolio_items_can(): void
    {
        $this->video(1);

        $this->editor()->get('/')
            ->assertSee('title="Edit project"', false)
            ->assertDontSee('+ Tambah project')
            ->assertSeeInOrder(['title="Edit project"', 'title="Hapus project"'], false);
        $this->editor()->get('/portfolio')->assertSeeInOrder(['title="Edit project"', 'title="Naikkan"', 'title="Hapus project"'], false);
    }
}
