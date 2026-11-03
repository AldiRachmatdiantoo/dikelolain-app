<?php

namespace App\Services;

use App\Repositories\Contracts\ToolRepositoryInterface;

class ToolService extends ListService
{
    protected array $images = ['icon' => 'tools'];

    public function __construct(ToolRepositoryInterface $tools, ImageUploadService $uploads)
    {
        parent::__construct($tools, $uploads);
    }
}
