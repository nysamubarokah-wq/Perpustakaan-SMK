<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Tidak bisa ubah role diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa mengubah role diri sendiri!');
        }

        $user->role = $user->role === 'admin' ? 'siswa' : 'admin';
        $user->save();

        return back()->with('success', 'Role '.$user->name.' berhasil diubah menjadi '.$user->role.'!');
    }
}