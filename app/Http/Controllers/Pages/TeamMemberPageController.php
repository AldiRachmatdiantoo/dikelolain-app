<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Pages\TeamMemberPageService;
use App\Vcms\EditMode;
use Illuminate\View\View;

class TeamMemberPageController extends Controller
{
    public function __invoke(TeamMemberPageService $page, string $slug): View
    {
        return view('pages.team-single', $page->data($slug, EditMode::active()));
    }
}
