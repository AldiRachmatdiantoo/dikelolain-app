<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\View\View;

/**
 * The Looker Studio report; its link and height are page settings (analytics.*).
 */
class AnalyticsController extends Controller
{
    public function __invoke(SettingService $settings): View
    {
        return view('admin.pages.analytics', compact('settings'));
    }
}
