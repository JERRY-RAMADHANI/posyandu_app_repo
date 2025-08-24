<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pemeriksaan Darah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 w-full bg-white shadow-md px-4 py-3 flex justify-between items-center z-50">
        <h1 class="text-lg font-bold" style="color: #FF9B00">Input Pemeriksaan Darah</h1>
        @auth
            <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
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
                    Input Pemeriksaan Darah - {{ \Carbon\Carbon::parse($tanggalAktif)->format('d/m/Y') }}
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
                                    if(is_null($absen->au)) $kosong[] = 'Asam Urat';
                                    if(is_null($absen->gda)) $kosong[] = 'GDA';
                                    if(is_null($absen->kol)) $kosong[] = 'Kolesterol';
                                @endphp
                                <span class="text-red-500">{{ implode(', ', $kosong) }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <button onclick="showInputForm('{{ $absen->id }}', '{{ $absen->nama }}')"
                                    class="text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 transition-colors">
                                    Input Data
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr class="bg-white border-b">
                            <td colspan="4" class="py-4 px-6 text-center">Tidak ada data pemeriksaan darah yang kosong</td>
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
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modalTitle">Input Pemeriksaan Darah</h3>
                <form id="measurementForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <!-- Asam Urat Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Asam Urat</label>
                        <input type="number" step="0.1" name="au" id="auInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="auCheck" 
                                class="rounded border-gray-300 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Kosong</span>
                        </label>
                    </div>

                    <!-- GDA Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">GDA</label>
                        <input type="number" step="1" name="gda" id="gdaInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="gdaCheck"
                                class="rounded border-gray-300 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Kosong</span>
                        </label>
                    </div>

                    <!-- Kolesterol Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kolesterol</label>
                        <input type="number" step="1" name="kol" id="kolInput"
                            class="block w-full rounded-md border-gray-300 shadow-sm mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="kolCheck"
                                class="rounded border-gray-300 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Kosong</span>
                        </label>
                    </div>

                    <!-- Keterangan Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <input type="text" name="ket" id="ketInput" oninput="this.value = this.value.toUpperCase()"
                            class="block w-full rounded-md border-gray-300 shadow-sm mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="ketCheck"
                                class="rounded border-gray-300 text-orange-500 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Kosong</span>
                        </label>
                    </div>

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
            
            if (no_reg.includes(searchTerm) || nama.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Form handling
    initializeForm();
});

function initializeForm() {
    const form = document.getElementById('measurementForm');
    const inputs = {
        au: document.getElementById('auInput'),
        gda: document.getElementById('gdaInput'),
        kol: document.getElementById('kolInput'),
        ket: document.getElementById('ketInput')
    };
    const checks = {
        au: document.getElementById('auCheck'),
        gda: document.getElementById('gdaCheck'),
        kol: document.getElementById('kolCheck'),
        ket: document.getElementById('ketCheck')
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
        
        // Set values for checked boxes
        Object.keys(checks).forEach(key => {
            if (checks[key].checked) {
                inputs[key].disabled = false;
                if (key === 'ket') {
                    inputs[key].value = 'KOSONG';
                } else {
                    inputs[key].value = '0';
                }
            }
        });

        // Submit the form
        this.submit();
    });
}

function showInputForm(id, nama) {
    const modal = document.getElementById('inputModal');
    const form = document.getElementById('measurementForm');
    const title = document.getElementById('modalTitle');

    // Reset form
    form.reset();
    
    // Enable all inputs
    ['auInput', 'gdaInput', 'kolInput', 'ketInput'].forEach(id => {
        const input = document.getElementById(id);
        if (input) input.disabled = false;
    });

    // Update modal content
    title.textContent = `Input Pemeriksaan Darah - ${nama}`;
    form.action = `/formDarah/${id}/isi-darah`;
    
    // Show modal
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('inputModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('inputModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Success Alert
@if(session('success'))
    alert("{{ session('success') }}");
@endif
</script>
</body>
</html>