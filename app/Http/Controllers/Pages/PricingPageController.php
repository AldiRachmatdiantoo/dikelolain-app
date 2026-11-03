<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Pages\PricingPageService;
use App\Vcms\EditMode;
use Illuminate\View\View;

class PricingPageController extends Controller
{
    public function __invoke(PricingPageService $page): View
    {
        return view('pages.pricing', $page->data(EditMode::active()));
    }
}
