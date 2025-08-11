@extends('main.admin.layout')

@section('content')
<div class="min-h-screen p-6" style="background: linear-gradient(135deg, #FF9B00 0%, #FFE100 100%);">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-center" style="color: #FF9B00">Absensi Dewasa/Lansia</h2>
        </div>

        <form action="{{ route('absen.dewasa.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Search Section -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <!-- No Registrasi Search -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">No. Registrasi</label>
                    <div class="relative">
                        <input type="text" id="search_reg" placeholder="Cari no registrasi..."
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                        <input type="hidden" name="no_reg" id="no_reg">
                        <div id="reg_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-40 overflow-y-auto hidden">
                        </div>
                    </div>
                </div>

                <!-- Nama Search -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
                    <input type="text" name="nama" id="nama" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>
            </div>

            <!-- Data Display Section -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">NIK</label>
                    <input type="text" name="nik" id="nik" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Usia</label>
                    <input type="number" name="usia" id="usia" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Alamat</label>
                    <input type="text" name="alamat" id="alamat" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4">
                <button type="submit"
                    class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 hover:shadow-lg"
                    style="background-color: #FF9B00">
                    Absen
                </button>
                <a href="{{ route('daftar.dewasa') }}"
                    class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 hover:shadow-lg"
                    style="background-color: #FFE100">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchReg = document.getElementById('search_reg');
    const regResults = document.getElementById('reg_results');

    searchReg.addEventListener('input', async function() {
        const search = this.value;
        if (search.length < 2) {
            regResults.classList.add('hidden');
            return;
        }

        try {
            const response = await fetch(`/search-dewasa?search=${search}`);
            const data = await response.json();
            
            regResults.innerHTML = '';
            data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                div.textContent = `${item.no_reg} - ${item.nama}`;
                div.onclick = () => selectPerson(item);
                regResults.appendChild(div);
            });
            
            regResults.classList.remove('hidden');
        } catch (error) {
            console.error('Error:', error);
        }
    });

    function selectPerson(data) {
        document.getElementById('no_reg').value = data.no_reg;
        document.getElementById('search_reg').value = data.no_reg;
        document.getElementById('nama').value = data.nama;
        document.getElementById('nik').value = data.nik;
        document.getElementById('tanggal_lahir').value = data.tanggal_lahir;
        document.getElementById('usia').value = data.umur;
        document.getElementById('alamat').value = data.alamat;
        regResults.classList.add('hidden');
    }

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!regResults.contains(e.target) && e.target !== searchReg) {
            regResults.classList.add('hidden');
        }
    });
});
</script>
@endpush

@endsection