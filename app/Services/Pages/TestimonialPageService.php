<?php

namespace App\Services\Pages;

use App\Models\Section;
use App\Services\TestimonialService;
use App\Vcms\Fields\TestimonialFields;

class TestimonialPageService
{
    public function __construct(private TestimonialService $testimonials)
    {
    }

    /**
     * @param  bool  $editing  also list hidden testimonials, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        return [
            // Old key-value content for the sections not migrated yet (Epic 3).
            'sections' => Section::with('contents.media')->active()->ordered()->get()->keyBy('key'),
            'testimonials' => $this->testimonials->all(activeOnly: !$editing),
            'testimonialFields' => $editing ? TestimonialFields::definition() : [],
        ];
    }
}
