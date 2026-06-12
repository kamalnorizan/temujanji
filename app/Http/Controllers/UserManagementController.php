<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManagedUserRequest;
use App\Http\Requests\UpdateManagedUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with(['roles', 'permissions'])
            ->latest()
            ->paginate(12);

        $roles = Role::query()->orderBy('name')->pluck('name');
        $permissions = Permission::query()->orderBy('name')->pluck('name');

        return view('admin.users.index', compact('users', 'roles', 'permissions'));
    }

    public function store(StoreManagedUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles([$validated['role']]);
        $user->syncPermissions($validated['direct_permissions'] ?? []);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berjaya didaftarkan.');
    }

    public function update(UpdateManagedUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);
        $user->syncRoles([$validated['role']]);
        $user->syncPermissions($validated['direct_permissions'] ?? []);

        return redirect()->route('admin.users.index')->with('success', 'Maklumat pengguna berjaya dikemaskini.');
    }
}
