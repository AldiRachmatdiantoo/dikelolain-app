<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Demo testimonials (previously hardcoded in pages/testimonials.blade.php).
 */
class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $exceeded = 'The creativity and professionalism shown by Dikelolain exceeded our expectations. Their design transformed our brand, and the results speak for themselves.';
        $pleasure = 'Working with Dikelolain has been an absolute pleasure. Their creativity and attention to detail truly brought our vision to life.';
        $happier = 'We couldn\'t be happier with the final result and have received glowing feedback from both our team and users. Dikelolain is a true professional.';

        $testimonials = [
            ['eleanor pena', 'co founder', $exceeded, 'images/client-logo-1.svg', 'images/author-1.jpg'],
            ['esther howard', 'director', $pleasure, 'images/client-logo-2.svg', 'images/author-2.jpg'],
            ['arlene mccoy', 'CEO', $happier, 'images/client-logo-3.svg', 'images/author-3.jpg'],
            ['savannah nguyen', 'co founder', $exceeded, 'images/client-logo-1.svg', 'images/author-4.jpg'],
            ['kristin watson', 'director', $pleasure, 'images/client-logo-2.svg', 'images/author-5.jpg'],
            ['jane cooper', 'CEO', $happier, 'images/client-logo-3.svg', 'images/author-6.jpg'],
        ];

        foreach ($testimonials as $index => [$name, $role, $content, $logo, $photo]) {
            Testimonial::create([
                'name' => $name,
                'role' => $role,
                'content' => $content,
                'rating' => 5,
                'logo' => $logo,
                'photo' => $photo,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
