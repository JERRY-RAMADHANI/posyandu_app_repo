@extends('main.admin.layout')

@php
    use Carbon\Carbon;
    use App\Models\TanggalAktif;

    $nomor = session('nomor', 0);
    $tanggalAktif = TanggalAktif::first();
    $tanggalSession = session('tanggal', $tanggalAktif ? $tanggalAktif->tanggal : now()->format('Y-m-d'));
    $tanggalDisplay = Carbon::parse($tanggalSession)->format('Y-m-d');
@endphp

@section('content')
    @if (session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
    @endif

    <div class="min-h-screen p-4" style="background: linear-gradient(135deg, #FF9B00 0%, #FFE100 100%);">
        
        <!-- 1. Tanggal Container -->
        <div class="w-full max-w-4xl mx-auto mb-6 bg-white rounded-2xl shadow-lg p-6 border-2" style="border-color: #FFC900">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" style="color: #FF9B00" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-bold" style="color: #FF9B00">Tanggal Kegiatan:</span>
                        <span class="px-4 py-2 rounded-lg font-bold text-white" style="background-color: #FF9B00">
                            {{ $tanggalDisplay }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('tanggal.set') }}" class="flex items-center gap-3">
                    @csrf
                    <input type="date" name="tanggal" value="{{ $tanggalSession }}"
                        class="border-2 rounded-lg px-4 py-2 focus:ring-2 focus:outline-none"
                        style="border-color: #FFC900; focus:ring-color: #FF9B00">
                    <button type="submit"
                        class="px-6 py-2 rounded-lg text-white font-bold transition-all duration-300 hover:shadow-lg"
                        style="background-color: #FF9B00">
                        Simpan
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. Call Number Container -->
        <div class="w-full max-w-4xl mx-auto mb-6 bg-white rounded-2xl shadow-lg p-6 border-2" style="border-color: #FFC900">
            <div class="flex flex-col items-center">
                <!-- Number Display -->
                <div class="mb-6">
                    <div class="text-8xl font-black py-6 px-12 rounded-2xl shadow-lg border-4 transition-transform hover:scale-105"
                        style="background-color: #FFE100; border-color: #FF9B00; color: #FF9B00">
                        {{ $nomor }}
                    </div>
                </div>

                <!-- Control Buttons -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 w-full max-w-2xl">
                    <form method="POST" action="{{ route('nomor.next') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full py-3 rounded-xl text-white font-bold text-lg transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
                            style="background-color: #FF9B00">
                            NEXT
                        </button>
                    </form>

                    <button onclick="callNumber({{ $nomor }})"
                        class="w-full py-3 rounded-xl text-white font-bold text-lg transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
                        style="background-color: #FFC900">
                        CALL
                    </button>

                    <form method="POST" action="{{ route('nomor.reset') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full py-3 rounded-xl bg-red-500 text-white font-bold text-lg transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                            RESET
                        </button>
                    </form>

                    <form method="POST" action="{{ route('nomor.set') }}" id="setForm" class="w-full">
                        @csrf
                        <input type="hidden" name="nomor">
                        <button type="button" onclick="setNumber()"
                            class="w-full py-3 rounded-xl text-white font-bold text-lg transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
                            style="background-color: #EBE389">
                            SETTING
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 3. Chart Container -->
        @if($chartData['hasData'])
        <div class="w-full max-w-4xl mx-auto mb-6 bg-white rounded-2xl shadow-lg p-6 border-2" style="border-color: #FFC900">
            <h2 class="text-2xl font-bold text-center mb-6" style="color: #FF9B00">Grafik Kehadiran {{ date('Y') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Chart Dewasa/Lansia -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-4 rounded-xl">
                    <h3 class="text-lg font-semibold text-center mb-4" style="color: #8B4513">KEHADIRAN DEWASA/LANSIA</h3>
                    <div style="position: relative; height: 200px;">
                        <canvas id="dewasaChart"></canvas>
                    </div>
                </div>

                <!-- Chart Balita/Remaja -->
                <div class="bg-gradient-to-br from-orange-50 to-yellow-50 p-4 rounded-xl">
                    <h3 class="text-lg font-semibold text-center mb-4" style="color: #FF6B35">KEHADIRAN BALITA/REMAJA</h3>
                    <div style="position: relative; height: 200px;">
                        <canvas id="balitaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Audio element -->
        <audio id="callSound"></audio>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Chart Script -->
    @if($chartData['hasData'])
    <script>
        // Chart data from backend
        const chartData = @json($chartData);

        // Calculate trend-based averages (simple moving average)
        function calculateTrendAverage(data) {
            if (data.length <= 1) return data;
            
            let trendAverage = [];
            
            for (let i = 0; i < data.length; i++) {
                if (i === 0) {
                    // First point: average of first 2 points
                    trendAverage.push((data[0] + (data[1] || data[0])) / 2);
                } else if (i === data.length - 1) {
                    // Last point: average of last 2 points
                    trendAverage.push((data[i - 1] + data[i]) / 2);
                } else {
                    // Middle points: average of 3 points (previous, current, next)
                    trendAverage.push((data[i - 1] + data[i] + data[i + 1]) / 3);
                }
            }
            
            return trendAverage;
        }

        // Common chart options
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        font: {
                            size: 10,
                            weight: 'bold'
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 10,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 9,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        };

        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            
            // Dewasa Chart - HANYA JIKA ADA DATA DEWASA
            if (chartData.dewasa.hasData) {
                const dewasaCtx = document.getElementById('dewasaChart');
                if (dewasaCtx) {
                    const dewasaSimpleAverage = chartData.dewasa.data.reduce((a, b) => a + b, 0) / chartData.dewasa.data.length;
                    const dewasaTrendAverage = calculateTrendAverage(chartData.dewasa.data);

                    new Chart(dewasaCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: chartData.dewasa.months, // Pakai bulan dewasa
                            datasets: [
                                {
                                    type: 'bar',
                                    label: 'Kehadiran',
                                    data: chartData.dewasa.data,
                                    backgroundColor: '#8B4513',
                                    borderColor: '#654321',
                                    borderWidth: 1,
                                    borderRadius: 3,
                                    borderSkipped: false,
                                    order: 2
                                },
                                {
                                    type: 'line',
                                    label: `Trend (avg: ${Math.round(dewasaSimpleAverage)})`,
                                    data: dewasaTrendAverage,
                                    borderColor: '#FF0000',
                                    backgroundColor: 'rgba(255, 0, 0, 0.1)',
                                    borderWidth: 2,
                                    borderDash: [5, 5],
                                    pointRadius: 3,
                                    pointHoverRadius: 5,
                                    pointBackgroundColor: '#FF0000',
                                    pointBorderColor: '#FFFFFF',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.4,
                                    order: 1
                                }
                            ]
                        },
                        options: {
                            ...commonOptions,
                            plugins: {
                                ...commonOptions.plugins,
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            if (context.dataset.type === 'bar') {
                                                return `Kehadiran: ${context.parsed.y} orang`;
                                            } else {
                                                return `Trend: ${Math.round(context.parsed.y)} orang`;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Balita Chart - HANYA JIKA ADA DATA BALITA
            if (chartData.balita.hasData) {
                const balitaCtx = document.getElementById('balitaChart');
                if (balitaCtx) {
                    const balitaSimpleAverage = chartData.balita.data.reduce((a, b) => a + b, 0) / chartData.balita.data.length;
                    const balitaTrendAverage = calculateTrendAverage(chartData.balita.data);

                    new Chart(balitaCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: chartData.balita.months, // Pakai bulan balita
                            datasets: [
                                {
                                    type: 'bar',
                                    label: 'Kehadiran',
                                    data: chartData.balita.data,
                                    backgroundColor: '#FF6B35',
                                    borderColor: '#E55A2B',
                                    borderWidth: 1,
                                    borderRadius: 3,
                                    borderSkipped: false,
                                    order: 2
                                },
                                {
                                    type: 'line',
                                    label: `Trend (avg: ${Math.round(balitaSimpleAverage)})`,
                                    data: balitaTrendAverage,
                                    borderColor: '#FF0000',
                                    backgroundColor: 'rgba(255, 0, 0, 0.1)',
                                    borderWidth: 2,
                                    borderDash: [5, 5],
                                    pointRadius: 3,
                                    pointHoverRadius: 5,
                                    pointBackgroundColor: '#FF0000',
                                    pointBorderColor: '#FFFFFF',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.4,
                                    order: 1
                                }
                            ]
                        },
                        options: {
                            ...commonOptions,
                            plugins: {
                                ...commonOptions.plugins,
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            if (context.dataset.type === 'bar') {
                                                return `Kehadiran: ${context.parsed.y} orang`;
                                            } else {
                                                return `Trend: ${Math.round(context.parsed.y)} orang`;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
    @endif

    <!-- Existing Script -->
    <script>
        @if (session('call_with_intro'))
            document.addEventListener('DOMContentLoaded', () => {
                callNumber({{ session('nomor') }}, false);
            });
        @endif

        function callNumber(nomor, skipIntro = true) {
            const audio = document.getElementById("callSound");
            audio.onended = null;

            if (skipIntro) {
                audio.src = `/sound/${nomor}.wav`;
                audio.play().catch(() => {
                    alert(`Gagal memutar suara: ${nomor}.wav`);
                });
                return;
            }

            audio.src = "/sound/intro.wav";
            audio.play()
                .then(() => {
                    audio.onended = () => {
                        audio.onended = null;
                        audio.src = `/sound/${nomor}.wav`;
                        audio.play().catch(() => {
                            alert(`Gagal memutar suara: ${nomor}.wav`);
                        });
                    };
                })
                .catch(() => {
                    alert("Gagal memutar intro.wav");
                });
        }

        function setNumber() {
            const input = prompt("Masukkan nomor baru:");
            const parsed = parseInt(input);
            if (!isNaN(parsed) && parsed >= 0) {
                const form = document.getElementById("setForm");
                form.nomor.value = parsed;
                form.submit();
            } else {
                alert("Input tidak valid.");
            }
        }

        // Auto play kalau skip_intro dari backend
        @if (session('skip_intro'))
            document.addEventListener('DOMContentLoaded', () => {
                callNumber({{ session('nomor') }}, true);
            });
        @endif
    </script>
@endsection
