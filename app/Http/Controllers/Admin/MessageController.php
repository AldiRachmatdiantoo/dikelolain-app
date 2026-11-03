<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MessageReadRequest;
use App\Services\ContactMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Inbox for the Contact page form ("Pesan Masuk").
 */
class MessageController extends Controller
{
    public function __construct(private ContactMessageService $messages)
    {
    }

    public function index(): View
    {
        return view('admin.pages.messages', ['messages' => $this->messages->inbox()]);
    }

    public function update(MessageReadRequest $request, int $id): RedirectResponse
    {
        $read = $request->boolean('read');
        $this->messages->markRead($id, $read);

        return back()->with('status', $read ? 'Pesan ditandai sudah dibaca.' : 'Pesan ditandai belum dibaca.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->messages->delete($id);

        return back()->with('status', 'Pesan dihapus.');
    }
}
