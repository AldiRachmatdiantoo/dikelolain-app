<?php

namespace Database\Seeders;

use App\Models\PageListItem;
use Illuminate\Database\Seeder;

/**
 * The bullet lists that were defaults in pages/about.blade.php and pages/pricing.blade.php.
 */
class PageListItemSeeder extends Seeder
{
    public function run(): void
    {
        $values = ['Creativity and Innovation', 'Client-Centricity', 'Integrity and Transparency', 'Excellence and Quality'];

        foreach (['about', 'vision', 'mission'] as $list) {
            foreach ($values as $index => $text) {
                PageListItem::create(['list_key' => $list, 'text' => $text, 'sort_order' => $index + 1]);
            }
        }

        $benefits = ['Get 30 day free trial', 'No any hidden fees pay', 'You can cancel anytime'];
        foreach ($benefits as $index => $text) {
            PageListItem::create([
                'list_key' => 'pricing_benefits',
                'text' => $text,
                'icon' => 'images/icon-pricing-benefit-' . ($index + 1) . '.svg',
                'sort_order' => $index + 1,
            ]);
        }
    }
}
