<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The Contact page form is saved to the database (admin inbox) before the page opens WhatsApp.
 */
class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'first_name' => 'Sari',
            'last_name' => 'Wulandari',
            'email' => 'sari@example.test',
            'phone' => '0812-3456-7890',
            'message' => "Halo,\nsaya mau buat logo.",
            ...$overrides,
        ];
    }

    private function message(array $attributes = []): ContactMessage
    {
        return ContactMessage::create([...$this->payload(), ...$attributes]);
    }

    public function test_contact_form_posts_to_the_site_and_still_knows_the_whatsapp_number(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('action="' . route('contact.send') . '"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="first_name"', false)
            ->assertSee('name="website"', false)
            ->assertSee('data-whatsapp="', false)
            ->assertSee('https://wa.me/', false)
            ->assertDontSee('validator.min.js', false);

        $this->assertStringNotContainsString('form-process.php', file_get_contents(public_path('js/function.js')));
    }

    public function test_message_is_saved(): void
    {
        $this->postJson('/contact', $this->payload())
            ->assertCreated()
            ->assertJsonPath('message', 'Pesan terkirim.');

        $message = ContactMessage::sole();
        $this->assertSame('Sari Wulandari', $message->name);
        $this->assertSame('sari@example.test', $message->email);
        $this->assertSame("Halo,\nsaya mau buat logo.", $message->message);
        $this->assertNull($message->read_at);
    }

    public function test_message_text_is_optional(): void
    {
        $this->postJson('/contact', $this->payload(['message' => '']))->assertCreated();

        $this->assertNull(ContactMessage::sole()->message);
    }

    public function test_invalid_input_is_explained_in_indonesian(): void
    {
        $this->postJson('/contact', $this->payload(['first_name' => '', 'email' => 'bukan-email', 'phone' => 'telepon saya']))
            ->assertStatus(422)
            ->assertJsonPath('errors.first_name.0', 'Nama depan wajib diisi.')
            ->assertJsonPath('errors.email.0', 'Format email tidak valid.')
            ->assertJsonPath('errors.phone.0', 'Nomor HP hanya boleh berisi angka, spasi, +, -, dan tanda kurung.');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_bots_filling_the_hidden_field_are_ignored_quietly(): void
    {
        $this->postJson('/contact', $this->payload(['website' => 'https://spam.example']))->assertCreated();

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_a_visitor_can_send_at_most_five_messages_a_minute(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/contact', $this->payload())->assertCreated();
        }

        $this->postJson('/contact', $this->payload())->assertStatus(429);
        $this->assertDatabaseCount('contact_messages', 5);
    }

    public function test_form_without_javascript_returns_to_the_contact_page(): void
    {
        $this->post('/contact', $this->payload())->assertRedirect(route('contact'));

        $this->assertDatabaseCount('contact_messages', 1);
    }

    public function test_inbox_needs_a_login(): void
    {
        $message = $this->message();

        $this->get('/admin/messages')->assertRedirect(route('admin.login'));
        $this->patch("/admin/messages/{$message->id}", ['read' => 1])->assertRedirect(route('admin.login'));
        $this->delete("/admin/messages/{$message->id}")->assertRedirect(route('admin.login'));
        $this->assertNull($message->fresh()->read_at);
    }

    public function test_inbox_lists_messages_newest_first_and_escapes_them(): void
    {
        $this->message(['first_name' => 'Lama', 'read_at' => now()]);
        $this->message(['first_name' => 'Baru', 'message' => '<script>alert(1)</script>']);

        $this->actingAs(User::factory()->create())
            ->get('/admin/messages')
            ->assertOk()
            ->assertSeeInOrder(['Baru Wulandari', 'Lama Wulandari'])
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('href="https://wa.me/6281234567890"', false)
            ->assertSee('href="mailto:sari@example.test"', false);
    }

    public function test_unread_messages_are_counted_in_the_sidebar_and_dashboard(): void
    {
        $this->message();
        $this->message();
        $this->message(['read_at' => now()]);
        $admin = User::factory()->create();

        $this->actingAs($admin)->get('/admin/messages')
            ->assertSee('<span class="menu-badge" title="2 pesan belum dibaca">2</span>', false);

        $this->actingAs($admin)->get('/admin/dashboard')
            ->assertSee(route('admin.messages.index'), false)
            ->assertSee('2 baru');
    }

    public function test_no_badge_when_everything_is_read(): void
    {
        $this->message(['read_at' => now()]);

        $this->actingAs(User::factory()->create())
            ->get('/admin/dashboard')
            ->assertDontSee('menu-badge', false)
            ->assertDontSee(' baru</span>', false);
    }

    public function test_message_can_be_marked_read_and_unread(): void
    {
        $message = $this->message();
        $admin = User::factory()->create();

        $this->actingAs($admin)->from('/admin/messages')
            ->patch("/admin/messages/{$message->id}", ['read' => 1])
            ->assertRedirect('/admin/messages')
            ->assertSessionHas('status', 'Pesan ditandai sudah dibaca.');
        $this->assertNotNull($message->fresh()->read_at);

        $this->actingAs($admin)->patch("/admin/messages/{$message->id}", ['read' => 0]);
        $this->assertNull($message->fresh()->read_at);

        $this->actingAs($admin)->patch("/admin/messages/{$message->id}", ['read' => 'mungkin'])
            ->assertSessionHasErrors('read');
    }

    public function test_message_can_be_deleted(): void
    {
        $message = $this->message();
        $admin = User::factory()->create();

        $this->actingAs($admin)->delete("/admin/messages/{$message->id}")
            ->assertSessionHas('status', 'Pesan dihapus.');
        $this->assertDatabaseCount('contact_messages', 0);

        $this->actingAs($admin)->delete("/admin/messages/{$message->id}")->assertNotFound();
    }

    public function test_whatsapp_reply_link_handles_local_and_international_numbers(): void
    {
        $this->assertSame('https://wa.me/6281234567890', $this->message(['phone' => '0812-3456-7890'])->whatsapp_url);
        $this->assertSame('https://wa.me/6281234567890', $this->message(['phone' => '+62 812 3456 7890'])->whatsapp_url);
        $this->assertNull($this->message(['phone' => '123'])->whatsapp_url);
    }
}
