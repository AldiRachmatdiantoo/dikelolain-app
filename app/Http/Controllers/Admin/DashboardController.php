<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContactMessageService;
use App\Vcms\EditablePages;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(ContactMessageService $messages): View
    {
        return view('admin.dashboard', [
            'pages' => EditablePages::all(),
            'unreadMessages' => $messages->unreadCount(),
        ]);
    }
}
