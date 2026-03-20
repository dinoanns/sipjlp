@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('audit-log.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Riwayat Aktivitas</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aksi</th>
                    <th>Model</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>{{ $log->aksi }}</td>
                    <td>
                        @if($log->model_type)
                        <span class="badge bg-secondary">{{ class_basename($log->model_type) }}</span>
                        @if($log->model_id)
                        <small class="text-muted">#{{ $log->model_id }}</small>
                        @endif
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td><code>{{ $log->ip_address }}</code></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        Tidak ada data audit log
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $logs->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
