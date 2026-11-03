<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Pages\ServiceDetailPageService;
use App\Vcms\EditMode;
use Illuminate\View\View;

class ServiceDetailPageController extends Controller
{
    public function __invoke(ServiceDetailPageService $page, string $slug): View
    {
        return view('pages.service-single', $page->data($slug, EditMode::active()));
    }
}
