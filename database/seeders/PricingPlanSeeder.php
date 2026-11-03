<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

/**
 * The three plans that were hard-coded in pages/pricing.blade.php.
 */
class PricingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['basic plan', 'Rp 300 Rb', false, [
                'Desain Grafis (Feed, Reels, Story)',
                'Desain Interaktif',
                'Strategi Konten & Social Media Management',
            ]],
            ['standard plan', 'Rp 500 Rb', true, [
                'Konten Video & Foto untuk Instagram & TikTok',
                'Desain Grafis (Feed, Reels, Story)',
                'Strategi Konten & Social Media Management',
            ]],
            ['premium plan', 'Rp 1 Jt', false, [
                'Konten Video & Foto untuk Instagram & TikTok',
                'Desain Grafis (Feed, Reels, Story)',
                'Strategi Konten & Social Media Management',
                'Ads kreatif untuk Meta (Instagram & FB Ads)',
                'Paket manajemen konten full (1 bulan ke depan ready!)',
            ]],
        ];

        foreach ($plans as $index => [$name, $price, $featured, $features]) {
            $plan = PricingPlan::create([
                'name' => $name,
                'price' => $price,
                'period' => '/per month',
                'is_featured' => $featured,
                'button_text' => "let's get started",
                'button_link' => '#',
                'sort_order' => $index + 1,
            ]);

            foreach ($features as $order => $text) {
                $plan->features()->create(['text' => $text, 'sort_order' => $order + 1]);
            }
        }
    }
}
