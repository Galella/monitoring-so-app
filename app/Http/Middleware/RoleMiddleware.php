<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Menangani role mapping: 'Super Admin' adalah setara dengan 'admin'
        $actualRole = $user->role->name;
        $requestedRole = $role;

        // Mapping role 'Super Admin', 'Admin Wilayah', 'Admin Area' sebagai 'admin' untuk keperluan middleware
        if (in_array($actualRole, ['Super Admin', 'Admin Wilayah', 'Admin Area'])) {
            $actualRole = 'admin';
        }

        if ($actualRole !== $requestedRole) {
            abort(403, 'Unauthorized access. You do not have the required role.');
        }

        return $next($request);
    }
}
