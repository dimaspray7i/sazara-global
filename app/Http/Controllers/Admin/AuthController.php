<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('admin_logged_in') && session('admin_role') === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password) || ! $user->isAdmin()) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak sesuai.'])
                ->withInput($request->only('email'));
        }

        // Regenerate session to prevent fixation
        $request->session()->regenerate();
        session([
            'admin_logged_in' => true,
            'admin_user_id'   => $user->id,
            'admin_name'      => $user->name,
            'admin_role'      => $user->role ?? 'admin',
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_logged_in', 'admin_user_id', 'admin_name', 'admin_role']);
        $request->session()->regenerate();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
