@extends('layouts.app')

@section('title', 'Manajemen User')

@section('actions')
<a href="{{ route('users.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-2"></i> Tambah User
</a>
@endsection

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                        <span class="badge text-white" style="background-color: #206bc4;">{{ ucfirst($role->name) }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if($user->unit)
                        <span class="badge text-dark" style="background-color: #e9ecef;">{{ $user->unit->label() }}</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($user->is_active)
                        <span class="badge text-white" style="background-color: #2fb344;">Aktif</span>
                        @else
                        <span class="badge text-white" style="background-color: #d63939;">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-list">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada data user</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
