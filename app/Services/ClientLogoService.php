<?php

namespace App\Services;

use App\Repositories\Contracts\ClientLogoRepositoryInterface;

class ClientLogoService extends ListService
{
    protected array $images = ['logo' => 'client-logos'];

    public function __construct(ClientLogoRepositoryInterface $logos, ImageUploadService $uploads)
    {
        parent::__construct($logos, $uploads);
    }
}
