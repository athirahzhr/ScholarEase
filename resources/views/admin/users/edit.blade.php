@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i> Edit User: {{ $user->name }}
                    </h5>
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to User
                    </a>
                </div>

                <div class="card-body">

                    {{-- 🔵 MAIN UPDATE FORM --}}
                    <form id="update-user-form"
                          action="{{ route('admin.users.update', $user->id) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            {{-- Name --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name"
                                       class="form-control"
                                       value="{{ old('name', $user->name) }}" required>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email"
                                       class="form-control"
                                       value="{{ old('email', $user->email) }}" required>
                            </div>

                            {{-- Password --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password"
                                       class="form-control">
                                <small class="text-muted">
                                    Leave blank to keep current password
                                </small>
                            </div>

                            {{-- Password Confirm --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation"
                                       class="form-control">
                            </div>

                            {{-- Role --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">User Role *</label>
                                <select name="role" class="form-select" required>
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                            </div>

                            {{-- Active --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Account Status</label>
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="is_active"
                                           class="form-check-input"
                                           value="1"
                                           {{ $user->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        Active Account
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- 🔴 DANGER ZONE (SEPARATE FORM) --}}
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Danger Zone</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Warning:</strong> This action is irreversible.
                            </div>

                            <form id="delete-user-form"
                                  action="{{ route('admin.users.destroy', $user->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you ABSOLUTELY sure? This will permanently delete the user.')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-2"></i> Delete User Permanently
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- 🔘 FOOTER ACTIONS --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Cancel
                        </a>

                        <button type="submit"
                                form="update-user-form"
                                class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
