<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pengukuran</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 w-full bg-white shadow-md px-4 py-3 flex justify-between items-center z-50">
        <!-- Judul -->
        <h1 class="text-lg font-bold" style="color: #FF9B00">Input Pengukuran</h1>

        <!-- Tombol Navigasi Balita / Dewasa -->
        <div class="flex space-x-4">
            <a href="{{ route('formAnak') }}"
                class="px-4 py-2 rounded-lg font-medium transition-colors
           @if (request()->routeIs('formBalita')) bg-orange-500 text-white shadow-md
           @else
                bg-gray-100 text-gray-700 hover:bg-gray-200 @endif">
                Balita
            </a>
            <a href="{{ route('formDewasa') }}"
                class="px-4 py-2 rounded-lg font-medium transition-colors
           @if (request()->routeIs('formDewasa')) bg-orange-500 text-white shadow-md
           @else
                bg-gray-100 text-gray-700 hover:bg-gray-200 @endif">
                Dewasa
            </a>
        </div>

        <!-- Logout -->
        @auth
            <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                @csrf
                <button type="submit"
                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                    Logout
                </button>
            </form>
        @endauth
    </nav>


    <!-- Main Content -->
    <div class="min-h-screen p-6 pt-20" style="background: linear-gradient(135deg, #FF9B00 0%, #FFE100 100%);">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-center" style="color: #FF9B00">
                    Input Pengukuran - {{ \Carbon\Carbon::parse($tanggalAktif)->format('d/m/Y') }}
                </h2>
            </div>

            <!-- Search Section -->
            <div class="mb-6 space-y-4">
                <div class="relative">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Cari Peserta</label>
                    <input type="text" id="search_input" placeholder="Cari berdasarkan No. Registrasi atau Nama..."
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-white uppercase bg-orange-500">
                        <tr>
                            <th scope="col" class="py-3 px-6">No. Reg</th>
                            <th scope="col" class="py-3 px-6">Nama</th>
                            <th scope="col" class="py-3 px-6">Data Kosong</th>
                            <th scope="col" class="py-3 px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($absenKosong as $absen)
                            <tr class="bg-white border-b hover:bg-gray-50 search-row">
                                <td class="py-4 px-6">{{ $absen->no_reg }}</td>
                                <td class="py-4 px-6">{{ $absen->nama }}</td>
                                <td class="py-4 px-6">
                                    @php
                                        $kosong = [];
                                        if (is_null($absen->sistole)) {
                                            $kosong[] = 'Sistole';
                                        }
                                        if (is_null($absen->diastole)) {
                                            $kosong[] = 'Diastole';
                                        }
                                        if (is_null($absen->bb)) {
                                            $kosong[] = 'BB';
                                        }
                                        if (is_null($absen->tb)) {
                                            $kosong[] = 'TB';
                                        }
                                        if (is_null($absen->lp)) {
                                            $kosong[] = 'LP';
                                        }
                                        if (is_null($absen->lila)) {
                                            $kosong[] = 'LILA';
                                        }
                                    @endphp
                                    <span class="text-red-500">{{ implode(', ', $kosong) }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <button
                                        onclick="showInputForm(
                                        '{{ $absen->id }}',
                                        '{{ $absen->nama }}',
                                        '{{ $absen->sistole }}',
                                        '{{ $absen->diastole }}',
                                        '{{ $absen->bb }}',
                                        '{{ $absen->tb }}',
                                        '{{ $absen->lp }}',
                                        '{{ $absen->lila }}',
                                    )"
                                        class="text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 transition-colors">
                                        Input Data
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b">
                                <td colspan="4" class="py-4 px-6 text-center">Tidak ada data pengukuran yang kosong
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="inputModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modalTitle">Input Pengukuran</h3>
                <form id="measurementForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sistole</label>
                        <input type="number" step="1" name="sistole" id="sistoleInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="sistoleCheck"
                                class="rounded border-gray-300 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Kosong</span>
                        </label>
                    </div>

                    <!-- Diastole Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Diastole</label>
                        <input type="number" step="1" name="diastole" id="diastoleInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="diastoleCheck"
                                class="rounded border-gray-300 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Kosong</span>
                        </label>
                    </div>

                    <!-- BB Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">BB (kg)</label>
                        <input type="number" step="0.1" name="bb" id="bbInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="bbCheck"
                                class="rounded border-gray-300 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Kosong</span>
                        </label>
                    </div>

                    <!-- TB Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">TB (cm)</label>
                        <input type="number" step="0.1" name="tb" id="tbInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="tbCheck"
                                class="rounded border-gray-300 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Kosong</span>
                        </label>
                    </div>

                    <!-- LP Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">LP (cm)</label>
                        <input type="number" step="0.1" name="lp" id="lpInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="lpCheck"
                                class="rounded border-gray-300 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Kosong</span>
                        </label>
                    </div>

                    <!-- LILA Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">LILA (cm)</label>
                        <input type="number" step="0.1" name="lila" id="lilaInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="lilaCheck"
                                class="rounded border-gray-300 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Kosong</span>
                        </label>
                    </div>

                    <!-- Sistole Field -->


                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeModal()"
                            class="bg-gray-200 px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-orange-500 px-4 py-2 rounded-md text-sm font-medium text-white hover:bg-orange-600">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('search_input');
            const rows = document.querySelectorAll('.search-row');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                rows.forEach(row => {
                    const no_reg = row.children[0].textContent.toLowerCase();
                    const nama = row.children[1].textContent.toLowerCase();
                    row.style.display = (no_reg.includes(searchTerm) || nama.includes(searchTerm)) ?
                        '' : 'none';
                });
            });

            // Form handling
            const form = document.getElementById('measurementForm');
            const inputs = {
                bb: document.getElementById('bbInput'),
                tb: document.getElementById('tbInput'),
                lp: document.getElementById('lpInput'),
                lila: document.getElementById('lilaInput'),
                sistole: document.getElementById('sistoleInput'),
                diastole: document.getElementById('diastoleInput'),
            };
            const checks = {
                bb: document.getElementById('bbCheck'),
                tb: document.getElementById('tbCheck'),
                lp: document.getElementById('lpCheck'),
                lila: document.getElementById('lilaCheck'),
                sistole: document.getElementById('sistoleCheck'),
                diastole: document.getElementById('diastoleCheck'),
            };

            // Add event listeners for each checkbox
            Object.keys(checks).forEach(key => {
                checks[key].addEventListener('change', function() {
                    inputs[key].disabled = this.checked;
                    if (this.checked) {
                        inputs[key].value = '';
                    }
                });
            });

            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Object.keys(checks).forEach(key => {
                    if (checks[key].checked) {
                        inputs[key].disabled = false;
                        inputs[key].value = '0';
                    }
                });

                this.submit();
            });

            // Modal functionality
            const modal = document.getElementById('inputModal');

            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Attach showInputForm to window object so it's globally available
            window.showInputForm = function(id, nama,sistole, diastole, bb, tb, lp, lila) {
                const title = document.getElementById('modalTitle');
                
                // Reset form and checkboxes
                form.reset();
                Object.keys(checks).forEach(key => {
                    checks[key].checked = false;
                    inputs[key].disabled = false;
                });

                // Set form data
                title.textContent = `Input Pengukuran - ${nama}`;
                form.action = `/formDewasa/${id}/isi-bb`;

                // Prefill existing data
                inputs.bb.value = bb || '';
                inputs.tb.value = tb || '';
                inputs.lp.value = lp || '';
                inputs.lila.value = lila || '';x
                inputs.sistole.value = sistole || '';
                inputs.diastole.value = diastole || '';

                modal.classList.remove('hidden');
            };

            // Make closeModal globally available
            window.closeModal = function() {
                modal.classList.add('hidden');
            };
        });

        // Success Alert
        @if (session('success'))
            alert("{{ session('success') }}");
        @endif
    </script>

</body>

</html>
