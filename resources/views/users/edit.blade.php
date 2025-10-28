@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit User</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('users.update', $user) }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')



                        <div class="form-floating mb-3">
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" placeholder="Full Name"
                                value="{{ old('name', $user->name) }}" required>
                            <label for="name">Full Name</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" placeholder="Email Address"
                                value="{{ old('email', $user->email) }}" required>
                            <label for="email">Email Address</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Password">
                            <label for="password">Password (leave blank to keep current)</label>
                        </div>

                        <div class="form-floating mb-4">
                            <select class="form-select @error('role') is-invalid @enderror"
                                id="role" name="role" required>
                                <option value="" disabled>Select a role</option>
                                <option value="user" {{ (old('role', $user->role) === 'user') ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ (old('role', $user->role) === 'admin') ? 'selected' : '' }}>Admin</option>
                            </select>
                            <label for="role">Role</label>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Users
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .card-header {
        background: var(--primary-color);
        padding: 1rem;
    }

    .form-floating {
        position: relative;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 1rem;
        height: auto;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-color-rgb), 0.15);
    }

    .btn {
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: var(--primary-color);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(var(--primary-color-rgb), 0.2);
    }

    .alert {
        border-radius: 10px;
    }


</style>
@endsection


