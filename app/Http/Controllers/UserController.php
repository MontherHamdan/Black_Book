<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(8);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'role' => 'required|in:' . User::ROLE_ADMIN . ',' . User::ROLE_DESIGNER,
            'base_order_price' => 'nullable|numeric|min:0',
            'decoration_price' => 'nullable|numeric|min:0',
            'custom_gift_price' => 'nullable|numeric|min:0',
            'internal_image_price' => 'nullable|numeric|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
        }

        $role = $request->role;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'title' => $request->title,
            'image' => $imagePath,
            'role' => $role,
            // للتوافق مع الكود القديم:
            'is_admin' => $role === User::ROLE_ADMIN,
            'base_order_price' => $request->base_order_price ?? 0,
            'decoration_price' => $request->decoration_price ?? 0,
            'custom_gift_price' => $request->custom_gift_price ?? 0,
            'internal_image_price' => $request->internal_image_price ?? 0,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }


    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'role' => 'required|in:' . User::ROLE_ADMIN . ',' . User::ROLE_DESIGNER,
            'base_order_price' => 'nullable|numeric|min:0',
            'decoration_price' => 'nullable|numeric|min:0',
            'custom_gift_price' => 'nullable|numeric|min:0',
            'internal_image_price' => 'nullable|numeric|min:0',
        ]);

        $role = $request->role;

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'title' => $request->title,
            'role' => $role,
            // برضه نحافظ على is_admin
            'is_admin' => $role === User::ROLE_ADMIN,
            'base_order_price' => $request->base_order_price ?? 0,
            'decoration_price' => $request->decoration_price ?? 0,
            'custom_gift_price' => $request->custom_gift_price ?? 0,
            'internal_image_price' => $request->internal_image_price ?? 0,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
            $data['image'] = $imagePath;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }


    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
