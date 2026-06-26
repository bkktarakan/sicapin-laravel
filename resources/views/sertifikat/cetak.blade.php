<html>
<head>
    <title>Rekap JPL {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px 10px; }
        th { background-color: #f0f0f0; text-align: center; }
        td { text-align: center; }
        h2 { text-align: center; margin-bottom: 0; }
        p.sub { text-align: center; margin-top: 4px; color: #555; }
    </style>
</head>
<body>
    <h2>MONITORING PENINGKATAN KOMPETENSI ASN</h2>
    <p class="sub">BKK KELAS I TARAKAN - TAHUN {{ $tahun }} - {{ $periodeLabel }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama Pegawai</th>
                <th>Jumlah JPL</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $index => $r)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $r->pegawai->nip ?? '-' }}</td>
                <td style="text-align: left;">{{ $r->pegawai->nama ?? '-' }}</td>
                <td>{{ $r->jumlah_jpl }}</td>
                <td>{{ $r->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
