<?php

namespace App\Services;

use App\Repositories\Contracts\ProcessStepRepositoryInterface;

class ProcessStepService extends ListService
{
    public function __construct(ProcessStepRepositoryInterface $steps, ImageUploadService $uploads)
    {
        parent::__construct($steps, $uploads);
    }
}
