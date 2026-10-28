<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['employee.department', 'roles'])
            ->orderBy('name')
            ->paginate(25);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $employees = Employee::whereDoesntHave('user')->active()->get();
        $roles = Role::all();
        return view('users.create', compact('employees', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id|unique:users',
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::create([
            'employee_id' => $validated['employee_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);

        $user->syncRoles($validated['roles']);

        $this->activityLog()->log(
            "User {$user->name} created with roles: " . implode(', ', $validated['roles']),
            $user,
            'created',
            'users'
        );

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $employees = Employee::whereDoesntHave('user')->active()->get();
        if ($user->employee_id) {
            $employees->push($user->employee);
        }
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('users.edit', compact('user', 'employees', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id|unique:users,employee_id,' . $user->id,
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $userData = [
            'employee_id' => $validated['employee_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ];

        if ($validated['password']) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);
        $user->syncRoles($validated['roles']);

        $this->activityLog()->log(
            "User {$user->name} updated",
            $user,
            'updated',
            'users'
        );

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        $this->activityLog()->log(
            "User {$user->name} deleted",
            $user,
            'deleted',
            'users'
        );

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function resetPassword(User $user)
    {
        $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 12);

        $user->update(['password' => Hash::make($password)]);

        $this->activityLog()->log(
            "Password reset for user {$user->name}",
            $user,
            'password_reset',
            'users'
        );

        return back()->with('success', "Password reset to: {$password}");
    }
}
