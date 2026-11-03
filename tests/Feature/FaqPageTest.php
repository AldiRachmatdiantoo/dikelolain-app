<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\PageSetting;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\FaqSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqPageTest extends TestCase
{
    use RefreshDatabase;

    private function editing(): static
    {
        return $this->actingAs(User::factory()->create())->withSession([EditMode::SESSION_KEY => true]);
    }

    public function test_seeded_faqs_are_shown_by_category_in_order(): void
    {
        $this->seed(FaqSeeder::class);

        $this->get('/faqs')
            ->assertOk()
            ->assertSeeInOrder([
                'FAQ Umum', 'Pertanyaan Teknis &amp; Desain', 'FAQ Manajemen Proyek', 'Bantuan Klien',
            ], false)
            ->assertSeeInOrder([
                'Layanan apa saja yang Dikelolain',
                'Siapa saja klien tipikal',
                'Format file apa saja',
                'Berapa waktu respons',
            ])
            ->assertDontSee('data-vcms', false);
    }

    public function test_hidden_faqs_and_empty_categories_are_not_shown_to_visitors(): void
    {
        Faq::create(['category' => 'general', 'question' => 'Tampil?', 'answer' => 'Ya', 'sort_order' => 1]);
        Faq::create(['category' => 'general', 'question' => 'Rahasia?', 'answer' => 'Ya', 'sort_order' => 2, 'is_active' => false]);

        $this->get('/faqs')
            ->assertOk()
            ->assertSee('Tampil?')
            ->assertDontSee('Rahasia?')
            ->assertDontSee('Bantuan Klien')
            ->assertDontSee('client_support');
    }

    public function test_edit_mode_shows_hidden_faqs_empty_categories_and_editor_controls(): void
    {
        Faq::create(['category' => 'general', 'question' => 'Rahasia?', 'answer' => 'Ya', 'sort_order' => 1, 'is_active' => false]);

        $this->editing()
            ->get('/faqs')
            ->assertOk()
            ->assertSee('Rahasia?')
            ->assertSee('vcms-hidden')
            ->assertSee('Bantuan Klien')
            ->assertSee('+ Tambah FAQ')
            ->assertSee(e(json_encode(url('/admin/api/faqs'))), false)
            ->assertSee('data-vcms-action="up"', false);
    }

    public function test_page_settings_replace_the_defaults(): void
    {
        PageSetting::create(['key' => 'faqs.header.title', 'value' => 'Tanya <span>Jawab</span>']);
        PageSetting::create(['key' => 'faqs.category.general', 'value' => 'Pertanyaan Umum']);
        PageSetting::create(['key' => 'faqs.cta.email', 'value' => 'halo@dikelolain.id']);
        Faq::create(['category' => 'general', 'question' => 'Q?', 'answer' => 'A', 'sort_order' => 1]);

        $this->get('/faqs')
            ->assertOk()
            ->assertSee('Tanya <span>Jawab</span>', false)
            ->assertSee('Pertanyaan Umum')
            ->assertSee('mailto:halo@dikelolain.id', false);
    }

    public function test_faq_text_is_escaped(): void
    {
        Faq::create(['category' => 'general', 'question' => '<script>alert(1)</script>', 'answer' => '<b>x</b>', 'sort_order' => 1]);

        $this->get('/faqs')
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_about_page_shows_the_first_four_active_faqs(): void
    {
        $this->seed();

        $this->get('/about')
            ->assertOk()
            ->assertSee('Layanan apa saja yang Dikelolain')
            ->assertSee('Bagaimana struktur harga')
            ->assertDontSee('Apakah saya bisa melihat contoh hasil kerja')
            ->assertDontSee('What services does your agency provide?');
    }
}
