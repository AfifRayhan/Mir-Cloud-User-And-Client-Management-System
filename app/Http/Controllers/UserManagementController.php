<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserDepartment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $this->authorizeAdmin();

        $users = User::with(['role', 'department', 'creator'])
            ->orderBy('name')
            ->paginate(12);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorizeAdmin();

        $roles = Role::orderBy('role_name')->get();
        $departments = UserDepartment::orderBy('department_name')->get();

        return view('users.create', compact('roles', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'exists:roles,id'],
            'department_id' => ['required', 'exists:user_departments,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'designation' => $validated['designation'] ?? null,
            'email' => $validated['email'],
            'username' => $validated['username'],
            'phone' => $validated['phone'] ?? null,
            'role_id' => $validated['role_id'],
            'department_id' => $validated['department_id'],
            'password' => Hash::make($validated['password']),
            'user_created_by' => Auth::id(),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $this->authorizeAdmin();

        $roles = Role::orderBy('role_name')->get();
        $departments = UserDepartment::orderBy('department_name')->get();

        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'exists:roles,id'],
            'department_id' => ['required', 'exists:user_departments,id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'designation' => $validated['designation'] ?? null,
            'email' => $validated['email'],
            'username' => $validated['username'],
            'phone' => $validated['phone'] ?? null,
            'role_id' => $validated['role_id'],
            'department_id' => $validated['department_id'],
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->withErrors([
                'user' => 'You cannot delete your own account.',
            ]);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User removed successfully.');
    }

    private function authorizeAdmin(): void
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Only administrators can manage users.');
        }
    }
}
