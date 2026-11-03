<?php

namespace App\View\Composers;

use App\Services\ContactMessageService;
use Illuminate\View\View;

/**
 * Unread count next to "Pesan Masuk" in the admin sidebar.
 */
class AdminSidebarComposer
{
    public function __construct(private ContactMessageService $messages)
    {
    }

    public function compose(View $view): void
    {
        $view->with('unreadMessages', $this->messages->unreadCount());
    }
}
