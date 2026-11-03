<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Vcms\EditablePages;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', ['pages' => EditablePages::all()]);
    }
}
