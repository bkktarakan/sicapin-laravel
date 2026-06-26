<html>
<head>
    <title>Rekap JPL {{ $user->nama }} {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px 10px; }
        th { background-color: #f0f0f0; text-align: center; }
        td { text-align: center; }
        h2 { text-align: center; margin-bottom: 0; }
        p.sub { text-align: center; margin-top: 4px; color: #555; }
        .info { margin-top: 20px; }
        .info td { border: none; text-align: left; padding: 3px 8px; }
        .status { font-weight: bold; margin-top: 15px; text-align: right; }
    </style>
</head>
<body>
    <h2>REKAP PENINGKATAN KOMPETENSI ASN</h2>
    <p class="sub">BKK KELAS I TARAKAN - TAHUN {{ $tahun }}</p>

    <table class="info" style="width: auto; margin-bottom: 5px;">
        <tr><td><strong>Nama</strong></td><td>: {{ $user->nama }}</td></tr>
        <tr><td><strong>NIP</strong></td><td>: {{ $user->nip }}</td></tr>
        <tr><td><strong>Jabatan</strong></td><td>: {{ $user->jabatan ?? '-' }}</td></tr>
        <tr><td><strong>Pangkat</strong></td><td>: {{ $user->pangkat ?? '-' }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pelatihan</th>
                <th>Penyelenggara</th>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>JPL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sertifikatList as $i => $s)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="text-align: left;">{{ $s->nama_pelatihan }}</td>
                <td>{{ $s->penyelenggara ?? '-' }}</td>
                <td style="font-size: 10px;">{{ $s->jenis_pelatihan }}</td>
                <td>{{ $s->tanggal ? $s->tanggal->format('d/m/Y') : '-' }}</td>
                <td>{{ $s->jpl }}</td>
            </tr>
            @empty
            <tr><td colspan="6">Belum ada sertifikat</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5">Total JPL</th>
                <th>{{ $totalJpl }}</th>
            </tr>
        </tfoot>
    </table>

    <p class="status">Status: {{ $keterangan }} (Target: 20 JPL)</p>
</body>
</html>
