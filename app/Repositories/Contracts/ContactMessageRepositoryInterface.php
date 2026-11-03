<?php

namespace App\Repositories\Contracts;

use App\Models\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContactMessageRepositoryInterface
{
    public function create(array $data): ContactMessage;

    /**
     * Newest first.
     */
    public function paginate(int $perPage): LengthAwarePaginator;

    public function unreadCount(): int;

    public function find(int $id): ContactMessage;

    public function update(ContactMessage $message, array $data): void;

    public function delete(ContactMessage $message): void;
}
