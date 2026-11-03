<?php

namespace Tests\Feature;

use App\Models\PageSetting;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_guest_gets_401(): void
    {
        $this->patch('/admin/api/settings/faqs.cta.text', ['value' => 'x'])->assertUnauthorized();
        $this->assertDatabaseCount('page_settings', 0);
    }

    public function test_unset_setting_returns_its_default(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/api/settings/faqs.cta.button_text')
            ->assertOk()
            ->assertJsonPath('data.value', 'Hubungi Kami Sekarang')
            ->assertJsonPath('data.type', 'text');
    }

    public function test_unknown_key_is_404(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/api/settings/nope.nothing')->assertNotFound();
        $this->patch('/admin/api/settings/nope.nothing', ['value' => 'x'])->assertNotFound();
        $this->assertDatabaseCount('page_settings', 0);
    }

    public function test_text_is_saved_and_shown(): void
    {
        $this->actingAs(User::factory()->create())
            ->patch('/admin/api/settings/faqs.cta.button_text', ['value' => 'Konsultasi Gratis'])
            ->assertOk()
            ->assertJsonPath('data.value', 'Konsultasi Gratis');

        $this->assertSame('Konsultasi Gratis', PageSetting::where('key', 'faqs.cta.button_text')->value('value'));
    }

    public function test_clearing_a_value_brings_back_the_default(): void
    {
        $this->actingAs(User::factory()->create());
        $this->patch('/admin/api/settings/faqs.cta.button_text', ['value' => 'Sementara']);

        $this->patch('/admin/api/settings/faqs.cta.button_text', ['value' => ''])
            ->assertOk()
            ->assertJsonPath('data.value', 'Hubungi Kami Sekarang');
    }

    public function test_clearing_rich_text_brings_back_the_default(): void
    {
        $this->actingAs(User::factory()->create());
        $this->patch('/admin/api/settings/faqs.header.title', ['value' => '<strong>Sementara</strong>']);

        $this->patch('/admin/api/settings/faqs.header.title', ['value' => '<script></script>'])
            ->assertOk()
            ->assertJsonPath('data.value', 'FAQs');
    }

    public function test_rich_text_keeps_allowed_tags_without_attributes(): void
    {
        $this->actingAs(User::factory()->create())
            ->patch('/admin/api/settings/faqs.header.title', [
                'value' => '<span onclick="steal()">Tanya</span> <script>alert(1)</script><b>Jawab</b>',
            ])
            ->assertOk()
            ->assertJsonPath('data.value', '<span>Tanya</span> alert(1)Jawab');
    }

    public function test_invalid_email_is_422_and_not_saved(): void
    {
        $this->actingAs(User::factory()->create())
            ->patchJson('/admin/api/settings/faqs.cta.email', ['value' => 'bukan-email'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('value');

        $this->assertDatabaseCount('page_settings', 0);
    }

    public function test_url_must_be_a_link(): void
    {
        $this->defineSetting('test.link', ['type' => 'url', 'label' => 'Link', 'default' => '#']);
        $this->actingAs(User::factory()->create());

        $this->patchJson('/admin/api/settings/test.link', ['value' => 'javascript:alert(1)'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('value');

        $this->patchJson('/admin/api/settings/test.link', ['value' => 'https://wa.me/62812'])->assertOk();
    }

    public function test_image_upload_replaces_the_previous_file(): void
    {
        $this->defineSetting('test.logo', ['type' => 'image', 'label' => 'Logo', 'default' => 'images/logo.svg']);
        $this->actingAs(User::factory()->create());

        $first = $this->patch('/admin/api/settings/test.logo', ['file' => UploadedFile::fake()->image('a.png')])
            ->assertOk()
            ->json('data.value');
        Storage::disk('public')->assertExists(substr($first, strlen('storage/')));

        $second = $this->patch('/admin/api/settings/test.logo', ['file' => UploadedFile::fake()->image('b.jpg')])
            ->assertOk()
            ->assertJsonPath('data.url', asset(app(SettingService::class)->get('test.logo')))
            ->json('data.value');

        $this->assertStringStartsWith('storage/uploads/settings/', $second);
        Storage::disk('public')->assertMissing(substr($first, strlen('storage/')));
    }

    public function test_image_setting_refuses_a_script(): void
    {
        $this->defineSetting('test.logo', ['type' => 'image', 'label' => 'Logo', 'default' => 'images/logo.svg']);

        $this->actingAs(User::factory()->create())
            ->patch('/admin/api/settings/test.logo', ['file' => $this->uploadedFile('logo.png', '<?php echo 1;')])
            ->assertStatus(422);

        $this->assertDatabaseCount('page_settings', 0);
    }

    /**
     * Adds a setting to the registry for one test (keys contain dots, so not via config dot notation).
     */
    private function defineSetting(string $key, array $definition): void
    {
        config(['page_settings' => [...config('page_settings'), $key => $definition]]);
    }
}
