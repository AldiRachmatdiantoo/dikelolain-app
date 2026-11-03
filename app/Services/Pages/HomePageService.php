<?php

namespace App\Services\Pages;

class HomePageService
{
    public function __construct(private SectionData $sections)
    {
    }

    /**
     * @param  bool  $editing  also list hidden items, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        return [
            ...$this->sections->legacy(),
            ...$this->sections->settings(),
            ...$this->sections->clientLogos($editing),
            ...$this->sections->tools($editing),
        ];
    }
}
