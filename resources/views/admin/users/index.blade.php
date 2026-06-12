@extends('layouts.app')

@section('header')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
        <h1 class="m-0">Pengurusan Pengguna</h1>
        <span class="badge text-bg-light border">Jumlah Pengguna: {{ $users->total() }}</span>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terdapat ralat semasa memproses maklumat.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card card-outline card-primary h-100">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pengguna</h3>
                </div>
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">No. Telefon</label>
                            <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Laluan</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Sahkan Kata Laluan</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role Pengguna</label>
                            <select id="role" name="role" class="form-select" required>
                                <option value="">-- Pilih role --</option>
                                @foreach ($roles as $roleName)
                                    <option value="{{ $roleName }}" @selected(old('role') === $roleName)>{{ ucfirst($roleName) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Direct Permission</label>
                            <div class="border rounded p-2" style="max-height: 220px; overflow-y: auto;">
                                @foreach ($permissions as $permissionName)
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="permission-create-{{ $loop->index }}"
                                            name="direct_permissions[]"
                                            value="{{ $permissionName }}"
                                            @checked(in_array($permissionName, old('direct_permissions', []), true))
                                        >
                                        <label class="form-check-label" for="permission-create-{{ $loop->index }}">
                                            {{ $permissionName }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-1"></i> Daftar Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Senarai Pengguna</h3>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telefon</th>
                                    <th>Role</th>
                                    <th>Direct Permission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $managedUser)
                                    <tr>
                                        <td>{{ $managedUser->name }}</td>
                                        <td>{{ $managedUser->email }}</td>
                                        <td>{{ $managedUser->phone ?: '-' }}</td>
                                        <td><span class="badge text-bg-info">{{ $managedUser->role }}</span></td>
                                        <td>{{ $managedUser->permissions->pluck('name')->implode(', ') ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="bg-light">
                                            <details>
                                                <summary class="fw-semibold">Kemaskini Pengguna: {{ $managedUser->name }}</summary>
                                                <div class="pt-3">
                                                    <form method="POST" action="{{ route('admin.users.update', $managedUser) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Nama</label>
                                                                <input type="text" name="name" class="form-control" value="{{ old('name_'.$managedUser->id, $managedUser->name) }}" required>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Email</label>
                                                                <input type="email" name="email" class="form-control" value="{{ old('email_'.$managedUser->id, $managedUser->email) }}" required>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">No. Telefon</label>
                                                                <input type="text" name="phone" class="form-control" value="{{ old('phone_'.$managedUser->id, $managedUser->phone) }}">
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Role Pengguna</label>
                                                                <select name="role" class="form-select" required>
                                                                    @foreach ($roles as $roleName)
                                                                        <option value="{{ $roleName }}" @selected($managedUser->role === $roleName)>{{ ucfirst($roleName) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Kata Laluan Baru (Opsyenal)</label>
                                                                <input type="password" name="password" class="form-control">
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Sahkan Kata Laluan Baru</label>
                                                                <input type="password" name="password_confirmation" class="form-control">
                                                            </div>

                                                            <div class="col-12">
                                                                <label class="form-label">Direct Permission</label>
                                                                <div class="border rounded p-2" style="max-height: 220px; overflow-y: auto;">
                                                                    @php
                                                                        $directPermissionNames = $managedUser->permissions->pluck('name');
                                                                    @endphp
                                                                    @foreach ($permissions as $permissionName)
                                                                        <div class="form-check">
                                                                            <input
                                                                                class="form-check-input"
                                                                                type="checkbox"
                                                                                id="permission-{{ $managedUser->id }}-{{ $loop->index }}"
                                                                                name="direct_permissions[]"
                                                                                value="{{ $permissionName }}"
                                                                                @checked($directPermissionNames->contains($permissionName))
                                                                            >
                                                                            <label class="form-check-label" for="permission-{{ $managedUser->id }}-{{ $loop->index }}">
                                                                                {{ $permissionName }}
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <button type="submit" class="btn btn-warning">
                                                                    <i class="fas fa-pen-to-square me-1"></i> Kemaskini Pengguna
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </details>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Tiada pengguna dijumpai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
