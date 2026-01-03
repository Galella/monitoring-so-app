<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrap();

        // Implicitly grant "Super Admin" role all permissions
        // This works in concert with Gate::define.
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->role && ($user->role->name === 'Super Admin' || $user->role->name === 'admin') ? true : null;
        });

        try {
            // Fetch all permissions if permissions table exists
            if (\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
                $permissions = \App\Models\Permission::all();

                foreach ($permissions as $permission) {
                    \Illuminate\Support\Facades\Gate::define($permission->name, function ($user) use ($permission) {
                        return $user->hasPermission($permission->name);
                    });
                }
            }
        } catch (\Exception $e) {
            // Fallback or log if needed during migration issues
        }
    }
}
