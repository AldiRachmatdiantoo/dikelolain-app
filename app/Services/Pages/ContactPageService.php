<?php

namespace App\Services\Pages;

class ContactPageService
{
    public function __construct(private SectionData $sections)
    {
    }

    /**
     * The Contact page only shows settings (texts, contact details, form target, map).
     */
    public function data(): array
    {
        return $this->sections->settings();
    }
}
