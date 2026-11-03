<?php

namespace App\Services\Pages;

use App\Services\TestimonialService;
use App\Vcms\Fields\TestimonialFields;

class TestimonialPageService
{
    public function __construct(
        private SectionData $sections,
        private TestimonialService $testimonials,
    ) {
    }

    /**
     * @param  bool  $editing  also list hidden items, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        return [
            ...$this->sections->settings(),
            ...$this->sections->tools($editing),
            'testimonials' => $this->testimonials->all(activeOnly: !$editing),
            'testimonialFields' => $editing ? TestimonialFields::definition() : [],
        ];
    }
}
