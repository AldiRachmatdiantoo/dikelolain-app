<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Pages\FaqPageService;
use App\Vcms\EditMode;
use Illuminate\View\View;

class FaqPageController extends Controller
{
    public function __invoke(FaqPageService $page): View
    {
        return view('pages.faqs', $page->data(EditMode::active()));
    }
}
