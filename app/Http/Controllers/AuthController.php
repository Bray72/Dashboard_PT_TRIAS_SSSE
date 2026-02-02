<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserApprovalNotification;

class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login
   public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {

            // 1️⃣ Cek status user
            if (Auth::user()->status !== 'approved') {
                Auth::logout();

                return back()->with(
                    'error',
                    'Akun kamu belum disetujui admin.'
                );
            }

            // 2️⃣ Regenerate session
            $request->session()->regenerate();

            // 3️⃣ Update last_login_at (DI SINI)
            Auth::user()->update([
                'last_login_at' => now(),
            ]);

            // 4️⃣ Redirect
            return redirect()->route('dashboard.safety')->with('success', 'Login successful!');
            }
            return back()->with('error', 'Invalid email or password')->onlyInput('email');
    }

    // Show register form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'pending',
        ]);

        Notification::route('mail', config('mail.admin_email'))
        ->notify(new UserApprovalNotification($user));

        return redirect('/login')
            ->with('success', 'Akun kamu berhasil dibuat dan menunggu persetujuan admin.');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logged out successfully!');
    }
}
