<html>
<head>
    <title>Rekap Jenis Pelatihan {{ $tahun }}</title>
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
    <h2>REKAP PER JENIS PELATIHAN</h2>
    <p class="sub">BKK KELAS I TARAKAN - TAHUN {{ $tahun }} - {{ $periodeLabel }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Pelatihan</th>
                <th>Jumlah Sertifikat</th>
                <th>Jumlah Pegawai</th>
                <th>Total JPL</th>
            </tr>
        </thead>
        <tbody>
            @php $grandSertifikat = 0; $grandPegawai = 0; $grandJpl = 0; @endphp
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left;">{{ $row['jenis'] }}</td>
                <td>{{ $row['jumlah_sertifikat'] > 0 ? $row['jumlah_sertifikat'] : '-' }}</td>
                <td>{{ $row['jumlah_pegawai'] > 0 ? $row['jumlah_pegawai'] : '-' }}</td>
                <td>{{ $row['total_jpl'] > 0 ? $row['total_jpl'] : '-' }}</td>
            </tr>
            @php $grandSertifikat += $row['jumlah_sertifikat']; $grandPegawai += $row['jumlah_pegawai']; $grandJpl += $row['total_jpl']; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total</th>
                <th>{{ $grandSertifikat }}</th>
                <th>{{ $grandPegawai }}</th>
                <th>{{ $grandJpl }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
