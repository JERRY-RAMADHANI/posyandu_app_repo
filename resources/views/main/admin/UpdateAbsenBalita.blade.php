@extends('main.admin.layout')

@section('content')
    <div class="min-h-screen p-6" style="background: linear-gradient(135deg, #FF9B00 0%, #FFE100 100%);">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-center" style="color: #FF9B00">Edit Data Absensi Balita</h2>
        </div>

        <form action="{{ route('absen.balita.update', $absenBalita->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Info -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">No. Registrasi</label>
                    <input type="text" name="no_reg" value="{{ $absenBalita->no_reg }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">NIK</label>
                    <input type="text" name="nik" value="{{ $absenBalita->nik }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
                    <input type="text" name="nama" value="{{ $absenBalita->nama }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                        value="{{ $absenBalita->tanggal_lahir }}" onchange="calculateAge(this.value)"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Usia</label>
                    <input type="number" name="usia" id="usia" value="{{ $absenBalita->usia }}" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Alamat</label>
                    <input type="text" name="alamat" value="{{ $absenBalita->alamat }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
            </div>

            <!-- Measurements -->
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Berat Badan (kg)</label>
                    <input type="number" name="bb" value="{{ $absenBalita->bb }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tinggi Badan (cm)</label>
                    <input type="number" name="tb" value="{{ $absenBalita->tb }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Lingkar Kepala (cm)</label>
                    <input type="number" name="lk" value="{{ $absenBalita->lk }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Lingkar Lengan (cm)</label>
                    <input type="number" name="ll" value="{{ $absenBalita->ll }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
                <div class="col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Keterangan</label>
                    <input type="text" name="ket" value="{{ $absenBalita->ket }}" 
                        oninput="this.value = this.value.toUpperCase()"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4 pt-4">
                <button type="submit"
                    class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 hover:shadow-lg"
                    style="background-color: #FF9B00">
                    Update
                </button>
                <a href="{{ route('edit.absen.balita') }}"
                    class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 hover:shadow-lg"
                    style="background-color: #FFE100">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>


    <!-- Success Alert -->
    @if (session('success'))
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

                // Calculate total months
                let months = (today.getFullYear() - birth.getFullYear()) * 12;
                months += today.getMonth() - birth.getMonth();

                // Adjust for day of month
                if (today.getDate() < birth.getDate()) {
                    months--;
                }

                // Handle edge cases for very recent dates
                if (months === 0) {
                    // Calculate days for very recent dates
                    const diffTime = Math.abs(today - birth);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    // If at least one day old but less than a month, count as 1 month
                    if (diffDays > 0) {
                        months = 1;
                    }
                }

                // Ensure we never return negative months
                months = Math.max(0, months);

                // Update the umur input field
                const umurInput = document.getElementById('usia');
                if (umurInput) {
                    umurInput.value = months;
                } else {
                    console.error('Umur input field not found');
                }
            } catch (error) {
                console.error('Error calculating age:', error);
            }
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
