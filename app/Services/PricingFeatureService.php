<?php

namespace App\Services;

use App\Repositories\Contracts\PricingFeatureRepositoryInterface;

/**
 * Ordered per pricing plan.
 */
class PricingFeatureService extends ListService
{
    public function __construct(PricingFeatureRepositoryInterface $items, ImageUploadService $uploads)
    {
        parent::__construct($items, $uploads);
    }
}
