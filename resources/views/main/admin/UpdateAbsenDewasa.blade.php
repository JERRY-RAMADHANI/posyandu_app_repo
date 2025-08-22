@extends('main.admin.layout')

@section('content')
<div class="min-h-screen p-6" style="background: linear-gradient(135deg, #FF9B00 0%, #FFE100 100%);">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-center" style="color: #FF9B00">Edit Data Absensi Dewasa</h2>
        </div>

        <form action="{{ route('absen.dewasa.update', $absenDewasa->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Info -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">No. Registrasi</label>
                    <input type="text" name="no_reg" value="{{ $absenDewasa->no_reg }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">NIK</label>
                    <input type="text" name="nik" value="{{ $absenDewasa->nik }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
                    <input type="text" name="nama" value="{{ $absenDewasa->nama }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ $absenDewasa->tanggal_lahir }}"
                        onchange="calculateAge(this.value)"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Usia</label>
                    <input type="number" name="usia" id="usia" value="{{ $absenDewasa->usia }}" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Alamat</label>
                    <input type="text" name="alamat" value="{{ $absenDewasa->alamat }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
            </div>

            <!-- Measurements -->
            <div class="grid grid-cols-5 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Berat Badan (kg)</label>
                    <input type="number" name="bb" value="{{ $absenDewasa->bb }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tinggi Badan (cm)</label>
                    <input type="number" name="tb" value="{{ $absenDewasa->tb }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Lingkar Perut (cm)</label>
                    <input type="number" name="lp" value="{{ $absenDewasa->lp }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">BMI</label>
                    <input type="number" name="bmi" id="bmi" value="{{ $absenDewasa->bmi }}" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Hasil</label>
                    <input type="text" name="hasil" id="hasil" value="{{ $absenDewasa->hasil }}" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">LILA (cm)</label>
                    <input type="number" name="lila" value="{{ $absenDewasa->lila }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Sistole</label>
                    <input type="number" name="sistole" value="{{ $absenDewasa->sistole }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Diastole</label>
                    <input type="number" name="diastole" value="{{ $absenDewasa->diastole }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Asam Urat</label>
                    <input type="number" name="au" value="{{ $absenDewasa->au }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">GDA</label>
                    <input type="number" name="gda" value="{{ $absenDewasa->gda }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Kolesterol</label>
                    <input type="number" name="kol" value="{{ $absenDewasa->kol }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
            </div>

            <!-- Additional Info -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Keterangan</label>
                    <input type="text" name="ket" value="{{ $absenDewasa->ket }}" oninput="this.value = this.value.toUpperCase()"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Note</label>
                    <textarea name="note" rows="1" oninput="this.value = this.value.toUpperCase()"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">{{ $absenDewasa->note }}</textarea>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4 pt-4">
                <button type="submit"
                    class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 hover:shadow-lg"
                    style="background-color: #FF9B00">
                    Update
                </button>
                <a href="{{ route('edit.absen.dewasa') }}"
                    class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 hover:shadow-lg"
                    style="background-color: #FFE100">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif

<script>
function calculateAge(birthDate) {
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    
    document.getElementById('usia').value = age;
}

// Calculate age when page loads
document.addEventListener('DOMContentLoaded', function() {
    const birthDate = document.getElementById('tanggal_lahir').value;
    if (birthDate) {
        calculateAge(birthDate);
    }
});
</script>
@endsection