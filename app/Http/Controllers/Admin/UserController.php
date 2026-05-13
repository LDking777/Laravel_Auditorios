<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                ];
            });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'canManage' => auth()->user()->role === 'admin',
        ]);
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Solo administradores pueden cambiar roles.');
        }

        $validated = $request->validate([
            'role' => 'required|in:user,admin,staff'
        ]);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        $user->update(['role' => $validated['role']]);

        return back()->with('message', 'Rol actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Solo administradores pueden eliminar usuarios.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $user->delete();

        return back()->with('message', 'Usuario eliminado exitosamente.');
    }
}
