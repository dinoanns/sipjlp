<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Cuti - {{ $periode }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header h2 { margin: 5px 0; font-size: 14px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RSUD CIPAYUNG JAKARTA TIMUR</h1>
        <h2>Laporan Pengajuan Cuti PJLP</h2>
        <h2>Periode: {{ $periode }}</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal Pengajuan</th>
                <th>Nama PJLP</th>
                <th>Unit</th>
                <th>Jenis Cuti</th>
                <th>Periode Cuti</th>
                <th class="text-center">Hari</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->tanggal_permohonan->format('d/m/Y') }}</td>
                <td>{{ $item->pjlp->nama }}</td>
                <td>{{ $item->pjlp->unit->label() }}</td>
                <td>{{ $item->jenisCuti->nama }}</td>
                <td>{{ $item->tgl_mulai->format('d/m/Y') }} - {{ $item->tgl_selesai->format('d/m/Y') }}</td>
                <td class="text-center">{{ $item->jumlah_hari }}</td>
                <td class="text-center">{{ $item->status->label() }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
