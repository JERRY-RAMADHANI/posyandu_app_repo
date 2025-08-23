@extends('main.admin.layout')

@section('content')
    <div class="min-h-screen p-6" style="background: linear-gradient(135deg, #FF9B00 0%, #FFE100 100%);">
        <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-center" style="color: #FF9B00">Update Data Balita</h2>
            </div>

            <form action="{{ route('daftar.balita.update', $dataBalita->id) }}" method="POST" class="space-y-6"
                id="updateForm">
                @csrf
                @method('PUT')

                <!-- No Registrasi -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">No. Registrasi</label>
                    <input type="text" value="{{ $dataBalita->no_reg }}" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <!-- Nama -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
                    <input type="text" name="nama" value="{{ $dataBalita->nama }}"
                        oninput="this.value = this.value.toUpperCase()"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <!-- NIK -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">NIK</label>
                    <input type="text" name="nik" value="{{ $dataBalita->nik }}"
                        oninput="this.value = this.value.toUpperCase()"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ $dataBalita->tanggal_lahir }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        onchange="calculateAge(this.value)">
                </div>

                <!-- Umur -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Usia</label>
                    <input type="number" name="usia" id="usia" value="{{ $dataBalita->usia }}" readonly
                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Jenis Kelamin</label>
                    <select name="jenis_kelamin"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ $dataBalita->jenis_kelamin == 'LAKI-LAKI' ? 'selected' : '' }}>
                            LAKI-LAKI</option>
                        <option value="Perempuan" {{ $dataBalita->jenis_kelamin == 'PEREMPUAN' ? 'selected' : '' }}>
                            PEREMPUAN</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nama Orang Tua</label>
                    <input type="text" name="nama_ortu" id="nama_ortu" value="{{ $dataBalita->nama_ortu }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Alamat</label>
                    <input type="text" name="alamat" value="{{ $dataBalita->alamat }}"
                        oninput="this.value = this.value.toUpperCase()"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Anak Ke</label>
                    <input type="number" name="anak_ke" value="{{ $dataBalita->anak_ke }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Panjang Lahir</label>
                    <input type="number" step="0.1" name="panjang_lahir" value="{{ $dataBalita->panjang_lahir }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">BB Lahir</label>
                    <input type="number" step="0.1" name="bb_lahir" value="{{ $dataBalita->bb_lahir }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Buku Kia</label>
                    <input type="number" name="buku_kia" value="{{ $dataBalita->buku_kia }}"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>



                <!-- RT/RW -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">RT</label>
                        <input type="text" name="rt" maxlength="3" value="{{ $dataBalita->rt }}"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">RW</label>
                        <input type="text" name="rw" maxlength="3" value="{{ $dataBalita->rw }}"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-4">
                    <button type="submit"
                        class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 hover:shadow-lg"
                        style="background-color: #FF9B00; hover:background-color: #FFC900">
                        Update
                    </button>
                    <a href="{{ route('edit.balita') }}"
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
