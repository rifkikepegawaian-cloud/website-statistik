<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Uploads
        Gate::define('uploads.view',   fn($u) => in_array($u->role, ['admin','user'], true));
        Gate::define('uploads.create', fn($u) => $u->role === 'admin');
        Gate::define('uploads.update', fn($u) => $u->role === 'admin');
        Gate::define('uploads.delete', fn($u) => $u->role === 'admin');

        // Users
        Gate::define('users.manage',   fn($u) => $u->role === 'admin');
    }
}
