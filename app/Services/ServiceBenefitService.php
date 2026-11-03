<?php

namespace App\Services;

use App\Repositories\Contracts\ServiceBenefitRepositoryInterface;

/**
 * Ordered per service.
 */
class ServiceBenefitService extends ListService
{
    public function __construct(ServiceBenefitRepositoryInterface $items, ImageUploadService $uploads)
    {
        parent::__construct($items, $uploads);
    }
}
