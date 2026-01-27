<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    // tampilkan user pending
    public function index()
    {
        $users = User::where('status', 'pending')->get();

        return view('admin.users', compact('users'));
    }

    // approve user
    public function approve($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'status' => 'approved',
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'User berhasil di-approve');
    }
}
