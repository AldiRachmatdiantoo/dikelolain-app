<?php

namespace App\Services;

use App\Repositories\Contracts\CounterRepositoryInterface;

class CounterService extends ListService
{
    public function __construct(CounterRepositoryInterface $counters, ImageUploadService $uploads)
    {
        parent::__construct($counters, $uploads);
    }
}
