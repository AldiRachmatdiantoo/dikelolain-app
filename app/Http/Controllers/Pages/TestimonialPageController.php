<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Pages\TestimonialPageService;
use App\Vcms\EditMode;
use Illuminate\View\View;

class TestimonialPageController extends Controller
{
    public function __invoke(TestimonialPageService $page): View
    {
        return view('pages.testimonials', $page->data(EditMode::active()));
    }
}
