<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Any URL without a route (the footer still gets its data from FooterComposer).
 */
class NotFoundPageController extends Controller
{
    public function __invoke(): Response
    {
        return response()->view('pages.404', [], 404);
    }
}
