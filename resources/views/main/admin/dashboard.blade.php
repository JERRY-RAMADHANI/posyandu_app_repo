@extends('main.admin.layout')

@php
    use Carbon\Carbon;
    use App\Models\TanggalAktif;

    $nomor = session('nomor', 1);
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

    <div class="min-h-screen flex flex-col items-center justify-center space-y-8 p-4"
        style="background: linear-gradient(135deg, #FF9B00 0%, #FFE100 100%);">

        <!-- Tanggal Container -->
        <div class="w-full max-w-3xl bg-white rounded-2xl shadow-lg p-6 border-2" style="border-color: #FFC900">
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

        <!-- Nomor Control Container -->
        <div class="w-full max-w-3xl bg-white rounded-2xl shadow-lg p-8 border-2 space-y-6" style="border-color: #FFC900">
            <!-- Number Display -->
            <div class="flex justify-center mb-8">
                <div class="text-8xl font-black py-8 px-16 rounded-2xl shadow-lg border-4 transition-transform hover:scale-105"
                    style="background-color: #FFE100; border-color: #FF9B00; color: #FF9B00">
                    {{ $nomor }}
                </div>
            </div>

            <!-- Control Buttons -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <form method="POST" action="{{ route('nomor.next') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full py-4 rounded-xl text-white font-bold text-lg transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
                        style="background-color: #FF9B00">
                        NEXT
                    </button>
                </form>

                <button onclick="callNumber({{ $nomor }})"
                    class="w-full py-4 rounded-xl text-white font-bold text-lg transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
                    style="background-color: #FFC900">
                    CALL
                </button>

                <form method="POST" action="{{ route('nomor.reset') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full py-4 rounded-xl bg-red-500 text-white font-bold text-lg transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        RESET
                    </button>
                </form>

                <form method="POST" action="{{ route('nomor.set') }}" id="setForm" class="w-full">
                    @csrf
                    <input type="hidden" name="nomor">
                    <button type="button" onclick="setNumber()"
                        class="w-full py-4 rounded-xl text-white font-bold text-lg transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
                        style="background-color: #EBE389">
                        SETTING
                    </button>
                </form>
            </div>
        </div>

        <!-- Audio element -->
        <audio id="callSound"></audio>
    </div>

    <!-- Script -->
    <script>
        function callNumber(nomor, skipIntro = false) {
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
                form.submit(); // biar backend yang set skip_intro
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
