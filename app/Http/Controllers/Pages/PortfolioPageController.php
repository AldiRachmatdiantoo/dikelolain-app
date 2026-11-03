<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Pages\PortfolioPageService;
use App\Vcms\EditMode;
use Illuminate\View\View;

class PortfolioPageController extends Controller
{
    public function index(PortfolioPageService $page): View
    {
        return view('pages.portfolio', $page->data(EditMode::active()));
    }

    public function experiment(PortfolioPageService $page): View
    {
        return view('pages.portfolio-experiment', $page->data(EditMode::active()));
    }
}
