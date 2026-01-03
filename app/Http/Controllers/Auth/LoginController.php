<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    public function index(){
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan role - mendukung 'Super Admin' juga sebagai admin
            if ($user->role && in_array($user->role->name, ['admin', 'Super Admin', 'Admin Wilayah', 'Admin Area'])) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role && $user->role->name === 'user') {
                return redirect()->intended(route('user.dashboard'));
            } else {
                // Jika user tidak memiliki role atau bukan admin/user, bisa diarahkan ke halaman default
                return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
