<html>
<head>
    <title>Detail {{ $jenis }} {{ $tahun }}</title>
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
    <h2>DAFTAR PEGAWAI - {{ strtoupper($jenis) }}</h2>
    <p class="sub">BKK KELAS I TARAKAN - TAHUN {{ $tahun }} - {{ $periodeLabel }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama Pegawai</th>
                <th>Jabatan</th>
                <th>Nama Pelatihan</th>
                <th>Penyelenggara</th>
                <th>Tanggal</th>
                <th>JPL</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $grandJpl = 0; @endphp
            @foreach($grouped as $row)
                @foreach($row['sertifikat'] as $si => $s)
                <tr>
                    @if($si === 0)
                        <td rowspan="{{ $row['jumlah_pelatihan'] }}">{{ $no++ }}</td>
                        <td rowspan="{{ $row['jumlah_pelatihan'] }}">{{ $row['pegawai']->nip ?? '-' }}</td>
                        <td rowspan="{{ $row['jumlah_pelatihan'] }}" style="text-align: left;">{{ $row['pegawai']->nama ?? '-' }}</td>
                        <td rowspan="{{ $row['jumlah_pelatihan'] }}">{{ $row['pegawai']->jabatan ?? '-' }}</td>
                    @endif
                    <td style="text-align: left;">{{ $s->nama_pelatihan }}</td>
                    <td>{{ $s->penyelenggara ?? '-' }}</td>
                    <td>
                        {{ $s->tanggal ? $s->tanggal->format('d/m/Y') : '-' }}
                        @if($s->tanggal_akhir && $s->tanggal_akhir != $s->tanggal)
                            - {{ $s->tanggal_akhir->format('d/m/Y') }}
                        @endif
                    </td>
                    <td>{{ $s->jpl }}</td>
                </tr>
                @php $grandJpl += $s->jpl; @endphp
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7">Total</th>
                <th>{{ $grandJpl }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
