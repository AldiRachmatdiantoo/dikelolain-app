<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

/**
 * The admin's own account: login, logout, profile and password.
 */
class AccountService
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    /**
     * @param  array{email: string, password: string}  $credentials
     */
    public function login(array $credentials, bool $remember = false): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * A new email address has to be verified again.
     *
     * @param  array{name: string, email: string}  $data
     */
    public function updateProfile(User $user, array $data): void
    {
        if ($data['email'] !== $user->email) {
            $data['email_verified_at'] = null;
        }

        $this->users->update($user, $data);
    }

    /**
     * Stored as a hash by the model's "hashed" cast.
     */
    public function updatePassword(User $user, string $password): void
    {
        $this->users->update($user, ['password' => $password]);
    }
}
