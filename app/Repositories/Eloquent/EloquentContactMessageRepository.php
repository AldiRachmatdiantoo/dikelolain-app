<?php

namespace App\Repositories\Eloquent;

use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentContactMessageRepository implements ContactMessageRepositoryInterface
{
    public function create(array $data): ContactMessage
    {
        return ContactMessage::create($data);
    }

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return ContactMessage::latest('id')->paginate($perPage);
    }

    public function unreadCount(): int
    {
        return ContactMessage::whereNull('read_at')->count();
    }

    public function find(int $id): ContactMessage
    {
        return ContactMessage::findOrFail($id);
    }

    public function update(ContactMessage $message, array $data): void
    {
        $message->update($data);
    }

    public function delete(ContactMessage $message): void
    {
        $message->delete();
    }
}
