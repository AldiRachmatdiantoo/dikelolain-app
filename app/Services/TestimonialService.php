<?php

namespace App\Services;

use App\Repositories\Contracts\TestimonialRepositoryInterface;

class TestimonialService extends ListService
{
    protected array $images = ['logo' => 'testimonials', 'photo' => 'testimonials'];

    public function __construct(TestimonialRepositoryInterface $testimonials, ImageUploadService $uploads)
    {
        parent::__construct($testimonials, $uploads);
    }
}
