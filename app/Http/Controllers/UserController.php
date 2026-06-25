<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('dashboard.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
        ]);

        $temporaryPassword = Str::password(12);

        User::create([
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'title' => $validated['title'],
            'department' => $validated['department'],
            'password' => Hash::make($temporaryPassword),
            'role' => 'user',
        ]);

        $message = 'User created successfully. Temporary password: '.$temporaryPassword;

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', $message);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'role' => ['required', Rule::in(['admin', 'user'])],
            'password' => 'nullable|string|min:8|max:255',
        ]);

        $user->update([
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'title' => $validated['title'],
            'department' => $validated['department'],
            'role' => $validated['role'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $message = 'User updated successfully.';

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', $message);
    }
}
