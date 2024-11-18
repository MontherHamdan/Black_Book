<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function listUsers()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => 1,

        ]);

        return redirect()->route('admin.users.list')->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id, // Ignore current user's email
        ]);
    
        $user = User::findOrFail($id);
        $user->update($request->only('name', 'email'));
    
        return response()->json(['success' => 'User updated successfully.']);
    }
    

public function deleteUser($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return response()->json(['success' => 'User deleted successfully.']);
}

}
