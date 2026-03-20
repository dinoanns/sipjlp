@extends('layouts.app')

@section('title', 'Master Lokasi')

@section('actions')
<a href="{{ route('master.lokasi.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-2"></i> Tambah Lokasi
</a>
@endsection

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Nama Lokasi</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($lokasi as $lok)
                <tr>
                    <td>{{ $lok->nama }}</td>
                    <td>{{ $lok->keterangan ?? '-' }}</td>
                    <td>
                        @if($lok->is_active)
                        <span class="badge bg-success">Aktif</span>
                        @else
                        <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-list">
                            <a href="{{ route('master.lokasi.edit', $lok) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('master.lokasi.destroy', $lok) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus lokasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Tidak ada data lokasi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
