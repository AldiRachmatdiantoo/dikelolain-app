<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Binds every repository interface to its implementation (ARCHITECTURE.md §2).
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [];
}
