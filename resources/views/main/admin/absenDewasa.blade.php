@extends('main.admin.layout')

@section('content')
<div class="min-h-screen p-6" style="background: linear-gradient(135deg, #FF9B00 0%, #FFE100 100%);">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-center" style="color: #FF9B00">Absensi Dewasa/Lansia</h2>
        </div>

        <form action="{{ route('absen.dewasa.store') }}" method="POST" class="space-y-6" onsubmit="return validateForm(event)">
            @csrf
            
            <!-- Search Section -->
            <div class="space-y-4">
                <!-- No Registrasi Search -->
                <div class="relative">
                    <label class="block mb-2 text-sm font-medium text-gray-900">No. Registrasi</label>
                    <input type="text" id="search_reg" placeholder="Cari no registrasi..."
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5" autocomplete="off">
                    <input type="hidden" name="no_reg" id="no_reg">
                    <!-- Search Results -->
                    <div id="reg_results" class="absolute z-10 w-full bg-white border border-gray-300 shadow-lg rounded-lg mt-1 max-h-60 overflow-y-auto">
                    </div>
                </div>

                <!-- Nama -->
                <div class="relative">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
                    <input type="text" id="search_nama" name="nama" placeholder="Cari nama..."
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5" autocomplete="off">
                    <!-- Search Results -->
                    <div id="nama_results" class="absolute z-10 w-full bg-white border border-gray-300 shadow-lg rounded-lg mt-1 max-h-60 overflow-y-auto">
                    </div>
                </div>

                <!-- NIK -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">NIK</label>
                    <input type="text" name="nik" id="nik" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <!-- Usia -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Usia</label>
                    <input type="number" name="usia" id="usia" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Alamat</label>
                    <input type="text" name="alamat" id="alamat" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4 pt-4">
                <button type="submit"
                    class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 hover:shadow-lg"
                    style="background-color: #FF9B00">
                    Absen
                </button>
                <a href="{{ route('absen.dewasa') }}"
                    class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 hover:shadow-lg"
                    style="background-color: #FFE100">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let typingTimer;
    const doneTypingInterval = 300;

    // Elements for No Registrasi search
    const searchReg = document.getElementById('search_reg');
    const regResults = document.getElementById('reg_results');

    // Elements for Nama search
    const searchNama = document.getElementById('search_nama');
    const namaResults = document.getElementById('nama_results');

    // Setup search for No Registrasi
    searchReg.addEventListener('input', function() {
        clearTimeout(typingTimer);
        const search = this.value;
        
        if (search.length < 1) {
            regResults.innerHTML = '';
            return;
        }

        typingTimer = setTimeout(() => {
            performSearch(search, regResults, 'reg');
        }, doneTypingInterval);
    });

    // Setup search for Nama
    searchNama.addEventListener('input', function() {
        clearTimeout(typingTimer);
        const search = this.value;
        
        if (search.length < 1) {
            namaResults.innerHTML = '';
            return;
        }

        typingTimer = setTimeout(() => {
            performSearch(search, namaResults, 'nama');
        }, doneTypingInterval);
    });

    async function performSearch(search, resultContainer, type) {
        try {
            const response = await fetch(`/SearchDewasa?search=${search}&type=${type}`);
            const data = await response.json();
            
            resultContainer.innerHTML = '';
            if (data.length > 0) {
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200';
                    div.innerHTML = `
                        <div class="font-medium">${item.no_reg} - ${item.nama}</div>
                        <div class="text-sm text-gray-600">NIK: ${item.nik}</div>
                    `;
                    div.onclick = () => selectPerson(item);
                    resultContainer.appendChild(div);
                });
            } else {
                resultContainer.innerHTML = '<div class="p-2 text-gray-500">Tidak ada hasil</div>';
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function selectPerson(data) {
        // Fill all form fields
        document.getElementById('no_reg').value = data.no_reg;
        document.getElementById('search_reg').value = data.no_reg;
        document.getElementById('search_nama').value = data.nama;
        document.getElementById('nik').value = data.nik;
        document.getElementById('tanggal_lahir').value = data.tanggal_lahir;
        document.getElementById('usia').value = data.umur;
        document.getElementById('alamat').value = data.alamat;
        
        // Clear search results
        regResults.innerHTML = '';
        namaResults.innerHTML = '';
    }

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!regResults.contains(e.target) && e.target !== searchReg) {
            regResults.innerHTML = '';
        }
        if (!namaResults.contains(e.target) && e.target !== searchNama) {
            namaResults.innerHTML = '';
        }
    });

    // Prevent form submission on enter key
    document.getElementById('search_reg').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });

    document.getElementById('search_nama').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
});

function validateForm(event) {
    // Prevent default form submission
    event.preventDefault();
    
    // Get form data
    const formData = new FormData(event.target);
    
    // Check if person is selected
    if (!formData.get('no_reg')) {
        alert('Silakan pilih data peserta terlebih dahulu');
        return false;
    }

    // Submit form
    event.target.submit();
}
</script>

<!-- Error Alert -->
@if(session('error'))
    <script>
        alert("{{ session('error') }}");
    </script>
@endif

<!-- Success Alert -->
@if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif

@endsection