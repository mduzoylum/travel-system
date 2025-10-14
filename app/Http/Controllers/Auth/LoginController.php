<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Role bazlı yönlendirme
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'supplier' => redirect()->route('admin.suppliers.index'), // Tedarikçi kullanıcılar
                'user' => redirect()->route('admin.suppliers.index'), // Normal kullanıcılar
                default => redirect()->route('admin.suppliers.index'),
            };
        }

        return back()->with('error', 'Giriş başarısız!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
