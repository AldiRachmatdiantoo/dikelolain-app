<?php

namespace App\Repositories\Eloquent;

use App\Models\Testimonial;
use App\Repositories\Contracts\TestimonialRepositoryInterface;

class EloquentTestimonialRepository extends EloquentListRepository implements TestimonialRepositoryInterface
{
    protected string $model = Testimonial::class;
}
