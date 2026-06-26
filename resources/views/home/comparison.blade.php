@extends('layouts.app')

@section('title', 'Perbandingan Tahun')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Perbandingan Multi-Tahun</h1>

    {{-- Personal Comparison --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Perbandingan JPL Pribadi</h3>
        @if(count($personalData) > 0)
        <canvas id="personalChart" height="120"></canvas>
        @else
        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Belum ada data</p>
        @endif
    </div>

    @if(auth()->user()->isAdmin() && count($comparisonData) > 0)
    {{-- Org Comparison --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Perbandingan Organisasi per Tahun</h3>
        <canvas id="orgChart" height="120"></canvas>
    </div>

    {{-- Comparison Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tahun</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total Pegawai</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Rata-rata JPL</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Terpenuhi</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total Sertifikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($comparisonData as $cd)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 font-bold text-primary-600">{{ $cd['tahun'] }}</td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $cd['total_pegawai'] }}</td>
                        <td class="px-4 py-3 text-center font-medium text-gray-900 dark:text-white">{{ $cd['avg_jpl'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                {{ $cd['terpenuhi'] }} / {{ $cd['total_pegawai'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $cd['total_sertifikat'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(count($personalData) > 0)
    new Chart(document.getElementById('personalChart'), {
        type: 'bar',
        data: {
            labels: @json(collect($personalData)->pluck('tahun')),
            datasets: [{
                label: 'JPL',
                data: @json(collect($personalData)->pluck('jpl')),
                backgroundColor: 'rgba(79, 70, 229, 0.7)',
                borderRadius: 6,
            }, {
                label: 'Sertifikat',
                data: @json(collect($personalData)->pluck('sertifikat')),
                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } },
            plugins: {
                datalabels: {
                    display: function(ctx) { return ctx.dataset.data[ctx.dataIndex] > 0; },
                    anchor: 'end', align: 'top', offset: 2,
                    font: { size: 11, weight: 'bold' },
                    color: function(ctx) { return ctx.datasetIndex === 0 ? '#4338CA' : '#16A34A'; },
                }
            }
        },
        plugins: [ChartDataLabels]
    });
    @endif

    @if(auth()->user()->isAdmin() && count($comparisonData) > 0)
    new Chart(document.getElementById('orgChart'), {
        type: 'line',
        data: {
            labels: @json(collect($comparisonData)->pluck('tahun')),
            datasets: [{
                label: 'Rata-rata JPL',
                data: @json(collect($comparisonData)->pluck('avg_jpl')),
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#4F46E5',
            }, {
                label: 'Terpenuhi',
                data: @json(collect($comparisonData)->pluck('terpenuhi')),
                borderColor: '#22C55E',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#22C55E',
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } },
            plugins: {
                datalabels: {
                    display: true,
                    anchor: 'end', align: 'top', offset: 2,
                    font: { size: 11, weight: 'bold' },
                    color: function(ctx) { return ctx.datasetIndex === 0 ? '#4338CA' : '#16A34A'; },
                }
            }
        },
        plugins: [ChartDataLabels]
    });
    @endif
});
</script>
@endsection
