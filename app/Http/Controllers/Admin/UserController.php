<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    // tampilkan user pending
    public function index()
    {
        $pendingUsers = User::where('status', 'pending')->get();
        $approvedUsers = User::where('status', 'approved')->get();

        return view('admin.users', compact(
            'pendingUsers',
            'approvedUsers'
        ));
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
