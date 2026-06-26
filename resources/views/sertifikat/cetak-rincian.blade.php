<html>
<head>
    <title>Rincian JPL {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background-color: #f0f0f0; text-align: center; font-size: 9px; }
        td { text-align: center; }
        h2 { text-align: center; margin-bottom: 0; }
        p.sub { text-align: center; margin-top: 4px; color: #555; }
    </style>
</head>
<body>
    <h2>RINCIAN PENINGKATAN KOMPETENSI ASN</h2>
    <p class="sub">BKK KELAS I TARAKAN - TAHUN {{ $tahun }} - {{ $periodeLabel }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                @foreach($jenisPelatihan as $jenis)
                    <th>{{ $jenis }}</th>
                @endforeach
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left;">{{ $row['pegawai']->nama }}</td>
                @foreach($row['jpl_per_jenis'] as $jpl)
                    <td>{{ $jpl > 0 ? $jpl : '' }}</td>
                @endforeach
                <td>{{ $row['jumlah_jpl'] }}</td>
                <td>{{ $row['keterangan'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
