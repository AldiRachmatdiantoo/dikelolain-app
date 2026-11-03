<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Pages\HomePageService;
use App\Vcms\EditMode;
use Illuminate\View\View;

class HomePageController extends Controller
{
    public function __invoke(HomePageService $page): View
    {
        return view('pages.home', $page->data(EditMode::active()));
    }
}
