<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Pages\ServicePageService;
use App\Vcms\EditMode;
use Illuminate\View\View;

class ServicePageController extends Controller
{
    public function __invoke(ServicePageService $page): View
    {
        return view('pages.services', $page->data(EditMode::active()));
    }
}
