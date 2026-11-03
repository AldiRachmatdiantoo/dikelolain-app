<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Vcms\EditMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditModeController extends Controller
{
    /**
     * Turn edit mode on and open the page to edit (`?to=/faqs`).
     */
    public function enable(Request $request): RedirectResponse
    {
        EditMode::enable();

        return redirect($this->safePath($request->query('to')));
    }

    public function disable(Request $request): RedirectResponse
    {
        EditMode::disable();

        return redirect($this->safePath($request->input('to')));
    }

    /**
     * Only same-site paths, so the link cannot send the admin elsewhere.
     */
    private function safePath(mixed $path): string
    {
        if (!is_string($path) || !str_starts_with($path, '/') || str_starts_with($path, '//') || str_contains($path, '\\')) {
            return '/';
        }

        return $path;
    }
}
