<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi - {{ $periode }}</title>
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
        <h2>Laporan Rekap Absensi PJLP</h2>
        <h2>Periode: {{ $periode }}</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Nama PJLP</th>
                <th>Unit</th>
                <th class="text-center">Hadir</th>
                <th class="text-center">Terlambat</th>
                <th class="text-center">Alpha</th>
                <th class="text-center">Izin</th>
                <th class="text-center">Cuti</th>
                <th class="text-center">Total Keterlambatan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($data as $pjlpId => $items)
            @php
                $pjlp = $items->first()->pjlp;
                $hadir = $items->where('status', 'hadir')->count();
                $terlambat = $items->where('status', 'terlambat')->count();
                $alpha = $items->where('status', 'alpha')->count();
                $izin = $items->where('status', 'izin')->count();
                $cuti = $items->where('status', 'cuti')->count();
                $totalTerlambat = $items->sum('menit_terlambat');
            @endphp
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $pjlp->nama }}</td>
                <td>{{ $pjlp->unit->label() }}</td>
                <td class="text-center">{{ $hadir }}</td>
                <td class="text-center">{{ $terlambat }}</td>
                <td class="text-center">{{ $alpha }}</td>
                <td class="text-center">{{ $izin }}</td>
                <td class="text-center">{{ $cuti }}</td>
                <td class="text-center">{{ $totalTerlambat > 0 ? $totalTerlambat . ' menit' : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
