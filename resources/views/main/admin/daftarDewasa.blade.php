@extends('main.admin.layout')

@section('content')
<div class="min-h-screen p-6" style="background: linear-gradient(135deg, #FF9B00 0%, #FFE100 100%);">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-center" style="color: #FF9B00">Daftar Dewasa/Lansia</h2>
        </div>

        <form action="{{ route('daftar.dewasa.store') }}" method="POST" class="space-y-6" id="registrationForm">
            @csrf
            
            <!-- No Registrasi -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">No. Registrasi</label>
                <input type="text" name="no_reg" value="{{ $no_reg }}" readonly 
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <!-- Nama -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
                <input type="text" name="nama" oninput="this.value = this.value.toUpperCase()"
                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>

            <!-- NIK -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">NIK</label>
                <input type="text" name="nik" oninput="this.value = this.value.toUpperCase()"
                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>

            <!-- Tanggal Lahir -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    onchange="calculateAge(this.value)">
            </div>

            <!-- Umur -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Usia</label>
                <input type="number" name="umur" id="umur" readonly
                    class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>

            <!-- Jenis Kelamin -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Jenis Kelamin</label>
                <select name="jenis_kelamin"
                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki">LAKI-LAKI</option>
                    <option value="Perempuan">PEREMPUAN</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                <select name="status"
                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">Pilih Status</option>
                    <option value="Nikah">NIKAH</option>
                    <option value="Belum Nikah">BELUM NIKAH</option>
                    <option value="Cerai Hidup">CERAI HIDUP</option>
                    <option value="Cerai Mati">CERAI MATI</option>
                </select>
            </div>

            <!-- Alamat -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Alamat</label>
                <input type="text" name="alamat" oninput="this.value = this.value.toUpperCase()"
                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Note</label>
                <input type="text" name="note" oninput="this.value = this.value.toUpperCase()"
                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>


            <!-- RT/RW -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">RT</label>
                    <input type="text" name="rt" maxlength="3" oninput="this.value = this.value.toUpperCase()"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">RW</label>
                    <input type="text" name="rw" maxlength="3" oninput="this.value = this.value.toUpperCase()"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4">
                <button type="submit"
                    class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 hover:shadow-lg"
                    style="background-color: #FF9B00; hover:background-color: #FFC900">
                    Daftar
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

<!-- Success Alert -->
@if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif


<script>
    function calculateAge(birthDate) {
        try {
            const today = new Date();
            const birth = new Date(birthDate);
            
            // Check if date is valid
            if (isNaN(birth.getTime())) {
                console.error('Invalid date');
                return;
            }
            
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            
            // Adjust age if birthday hasn't occurred this year
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            
            // Update the umur input field
            const umurInput = document.getElementById('umur');
            if (umurInput) {
                umurInput.value = age;
            } else {
                console.error('Umur input field not found');
            }
        } catch (error) {
            console.error('Error calculating age:', error);
        }
    }

    // Add event listener when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('tanggal_lahir');
        if (dateInput) {
            dateInput.addEventListener('change', function() {
                calculateAge(this.value);
            });
        }

        // Auto-hide success alert after 3 seconds
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.remove();
            }, 3000);
        }

        // Convert select options to uppercase
        document.querySelectorAll('select').forEach(select => {
            Array.from(select.options).forEach(option => {
                option.textContent = option.textContent.toUpperCase();
            });
        });
    });
</script>
@endsection
