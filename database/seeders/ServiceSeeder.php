<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Demo services with their detail page content (previously spread over
 * SectionContentSeeder and fallbacks in the views).
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['UI/UX Design', 'Web Development', '3D Designs', 'Digital Marketing Design', 'Motion Graphics'];

        $benefits = [
            'enhanced online presence',
            'secure platforms to protect data',
            'improved user experience (ux)',
            'seamless e-commerce integration',
            'mobile-friendly & responsive design',
            'higher user engagement & retention',
        ];

        $faqs = [
            ['What services does your agency provide?', 'We specialize in including graphic design, branding, website design and development, UX/UI design, social media marketing, digital advertising, video production, and content creation.'],
            ['Who are your typical clients?', 'We work with businesses of all sizes, from startups and small businesses to large enterprises. Our clients span industries such as technology, retail, hospitality, healthcare, and more.'],
            ['How long does a typical project take?', 'The timeline depends on the scope and complexity of the project. Smaller projects may take 2-4 weeks, while larger ones can take several months. We\'ll provide an estimated timeline after discussing your needs.'],
            ['What is your pricing structure?', 'We offer customized pricing based on the specific requirements of each project. After our initial consultation, we\'ll provide a detailed proposal with a breakdown of costs.'],
            ['Why are UX and UI important?', 'User Experience (UX) design focuses on enhancing the overall experience users have when interacting with a product or service. It involves researching user needs, creating user personas, designing user journeys, and testing how easy or efficient the product is to use.'],
        ];

        foreach ($names as $index => $name) {
            $lower = Str::lower($name);

            $service = Service::create([
                'slug' => Str::slug($name),
                'name' => $name,
                'summary' => 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.',
                'image' => 'images/service-image-' . ($index + 1) . '.jpg',
                'description' => "Our {$lower} services are designed to create dynamic, user-friendly results that drive engagement and deliver exceptional experiences. From concept to launch, we work closely with you to tailor every detail to your brand.\n\n"
                    . 'Our expertise spans a wide range of tools and platforms. Whether you need a quick refresh or a complete solution, our team makes sure the result is impactful, reliable, and ready to grow with your business.',
                'benefits_title' => "Benefits <span>{$lower}</span>",
                'benefits_intro' => "{$name} enhances your online presence, boosts engagement, and drives business growth with solutions tailored to meet your goals.",
                'sort_order' => $index + 1,
            ]);

            foreach ($benefits as $order => $text) {
                $service->benefits()->create(['text' => $text, 'sort_order' => $order + 1]);
            }

            foreach ($faqs as $order => [$question, $answer]) {
                $service->faqs()->create(['question' => $question, 'answer' => $answer, 'sort_order' => $order + 1]);
            }
        }
    }
}
