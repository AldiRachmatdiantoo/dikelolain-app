<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMessageRequest;
use App\Services\ContactMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Saves the Contact page form. The page script then opens WhatsApp with the same message.
 */
class ContactMessageController extends Controller
{
    public function __invoke(ContactMessageRequest $request, ContactMessageService $messages): JsonResponse|RedirectResponse
    {
        // Spam gets the same answer, so bots cannot tell it was dropped.
        if (!$request->isSpam()) {
            $messages->receive($request->validated());
        }

        return $request->expectsJson()
            ? response()->json(['message' => 'Pesan terkirim.'], 201)
            : redirect()->route('contact');
    }
}
