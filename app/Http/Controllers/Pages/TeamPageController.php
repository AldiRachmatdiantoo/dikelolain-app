<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Pages\TeamPageService;
use App\Vcms\EditMode;
use Illuminate\View\View;

class TeamPageController extends Controller
{
    public function __invoke(TeamPageService $page): View
    {
        return view('pages.team', $page->data(EditMode::active()));
    }
}
