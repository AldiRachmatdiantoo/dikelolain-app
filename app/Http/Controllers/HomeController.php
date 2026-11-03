<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class HomeController extends Controller
{
   
    public function index()
    {
        // Get all active sections with their contents
        $sections = Section::with('contents.media')
            ->active()
            ->ordered()
            ->get()
            ->keyBy('key'); // Make it easier to access by key

        return view('pages.home', compact('sections'));
    }
}
