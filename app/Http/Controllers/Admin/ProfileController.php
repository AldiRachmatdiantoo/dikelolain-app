<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Services\AccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * The signed-in admin's own name, email and password.
 */
class ProfileController extends Controller
{
    public function __construct(private AccountService $account)
    {
    }

    public function show(): View
    {
        return view('admin.pages.profile-detail');
    }

    public function editPassword(): View
    {
        return view('admin.auth.reset-password');
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $this->account->updateProfile($request->user(), $request->validated());

        return back()->with('status', 'Detail profil berhasil diperbarui.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $this->account->updatePassword($request->user(), $request->validated('password'));

        return back()->with('status', 'Password berhasil diubah!');
    }
}
