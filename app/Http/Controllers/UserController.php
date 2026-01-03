<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

use App\Models\Wilayah;
use App\Models\Area;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'wilayah', 'area'])->paginate(10);
        return view('user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $wilayahs = Wilayah::all();
        $areas = Area::all();
        return view('user.create', compact('roles', 'wilayahs', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'wilayah_id' => 'nullable|exists:wilayahs,id',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'wilayah_id' => $request->wilayah_id,
            'area_id' => $request->area_id,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $wilayahs = Wilayah::all();
        $areas = Area::all();
        return view('user.edit', compact('user', 'roles', 'wilayahs', 'areas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'wilayah_id' => 'nullable|exists:wilayahs,id',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'wilayah_id' => $request->wilayah_id,
            'area_id' => $request->area_id,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Jangan mengizinkan penghapusan user admin jika jumlah admin tinggal 1
        if ($user->role->name === 'admin' && User::where('role_id', $user->role_id)->count() <= 1) {
            return redirect()->route('users.index')->with('error', 'Cannot delete the only admin user.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
