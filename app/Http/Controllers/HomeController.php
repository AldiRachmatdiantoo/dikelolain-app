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

    public static function getContent($sections, $sectionKey, $contentKey, $default = '')
    {
        if (!isset($sections[$sectionKey])) {
            return $default;
        }

        $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);

        return $content ? $content->value : $default;
    }
}
