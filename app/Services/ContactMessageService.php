<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Messages from the Contact page form, read in the admin inbox.
 */
class ContactMessageService
{
    private const PER_PAGE = 20;

    public function __construct(private ContactMessageRepositoryInterface $messages)
    {
    }

    /**
     * @param  array{first_name: string, last_name: string, email: string, phone: string, message?: ?string}  $data
     */
    public function receive(array $data): ContactMessage
    {
        return $this->messages->create($data);
    }

    public function inbox(): LengthAwarePaginator
    {
        return $this->messages->paginate(self::PER_PAGE);
    }

    public function unreadCount(): int
    {
        return $this->messages->unreadCount();
    }

    public function markRead(int $id, bool $read = true): void
    {
        $message = $this->messages->find($id);
        $this->messages->update($message, ['read_at' => $read ? ($message->read_at ?? now()) : null]);
    }

    public function delete(int $id): void
    {
        $this->messages->delete($this->messages->find($id));
    }
}
