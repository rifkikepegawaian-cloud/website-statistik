<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // boleh lihat/unduh untuk semua user yang login
        Gate::define('uploads.view', fn(User $u) => in_array($u->role, ['admin','user'], true));

        // khusus admin
        Gate::define('uploads.create', fn(User $u) => $u->role === 'admin');
        Gate::define('uploads.update', fn(User $u) => $u->role === 'admin');
        Gate::define('uploads.delete', fn(User $u) => $u->role === 'admin');
    }
}
