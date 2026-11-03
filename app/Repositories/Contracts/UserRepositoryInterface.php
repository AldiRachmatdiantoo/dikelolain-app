<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Saves the given attributes, also those that are not mass assignable (email_verified_at).
     * Only pass validated data.
     */
    public function update(User $user, array $data): void;
}
