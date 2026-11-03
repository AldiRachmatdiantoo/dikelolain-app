<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Pages\AboutPageService;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    public function __invoke(AboutPageService $page): View
    {
        return view('pages.about', $page->data());
    }
}
