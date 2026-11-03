<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Pages\ContactPageService;
use Illuminate\View\View;

class ContactPageController extends Controller
{
    public function __invoke(ContactPageService $page): View
    {
        return view('pages.contact', $page->data());
    }
}
