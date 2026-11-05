<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReleaseTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // The proxy list is static on the request class, so it would leak into the next test.
        Request::setTrustedProxies([], -1);

        parent::tearDown();
    }

    public function test_the_seeders_run_without_the_dev_packages(): void
    {
        foreach (glob(database_path('seeders/*.php')) as $seeder) {
            $code = file_get_contents($seeder);

            $this->assertStringNotContainsString('factory(', $code, basename($seeder));
            $this->assertStringNotContainsString('fake(', $code, basename($seeder));
        }
    }

    public function test_an_https_address_makes_every_asset_https(): void
    {
        config(['app.url' => 'https://dikelolain.test']);
        $this->app->getProvider(AppServiceProvider::class)->boot();

        $this->get('/')
            ->assertSee('href="https://localhost/css/site.min.css', false)
            ->assertDontSee('http://localhost/css/site.min.css', false);
    }

    public function test_a_trusted_proxy_passes_on_the_visitor(): void
    {
        config(['trustedproxy.proxies' => '*']);

        $this->fromProxy()->assertSee('https://localhost/css/site.min.css', false);

        $this->assertSame('203.0.113.7', request()->ip());
    }

    public function test_an_unknown_proxy_cannot_fake_the_visitor(): void
    {
        $this->fromProxy()->assertSee('http://localhost/css/site.min.css', false);

        $this->assertSame('10.0.0.1', request()->ip());
    }

    public function test_the_release_template_keeps_debug_off_and_holds_no_secrets(): void
    {
        $template = file_get_contents(base_path('.env.production.example'));

        $this->assertMatchesRegularExpression('/^APP_ENV=production$/m', $template);
        $this->assertMatchesRegularExpression('/^APP_DEBUG=false$/m', $template);
        $this->assertMatchesRegularExpression('/^SESSION_SECURE_COOKIE=true$/m', $template);
        $this->assertMatchesRegularExpression('/^APP_KEY=$/m', $template);
        $this->assertMatchesRegularExpression('/^ADMIN_PASSWORD=$/m', $template);
    }

    private function fromProxy()
    {
        return $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->withHeaders(['X-Forwarded-For' => '203.0.113.7', 'X-Forwarded-Proto' => 'https'])
            ->get('/');
    }
}
