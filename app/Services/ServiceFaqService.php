<?php

namespace App\Services;

use App\Repositories\Contracts\ServiceFaqRepositoryInterface;

/**
 * Ordered per service.
 */
class ServiceFaqService extends ListService
{
    public function __construct(ServiceFaqRepositoryInterface $items, ImageUploadService $uploads)
    {
        parent::__construct($items, $uploads);
    }
}
