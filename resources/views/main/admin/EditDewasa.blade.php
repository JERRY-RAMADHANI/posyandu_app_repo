@extends('main.admin.layout')

@section('content')
<div class="min-h-screen p-6" style="background: linear-gradient(135deg, #FF9B00 0%, #FFE100 100%);">
    <div class="w-full mx-auto bg-white rounded-xl shadow-md p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-center" style="color: #FF9B00">Data Dewasa/Lansia</h2>
        </div>

        <!-- Search Bar -->
        <div class="mb-4">
            <form action="{{ route('edit.dewasa') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
                    placeholder="Cari berdasarkan nama, NIK, atau No. Registrasi...">
                <button type="submit" 
                    class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    Cari
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-white uppercase bg-orange-500">
                    <tr>
                        <th scope="col" class="py-3 px-6">No. Reg</th>
                        <th scope="col" class="py-3 px-6">NIK</th>
                        <th scope="col" class="py-3 px-6">Nama</th>
                        <th scope="col" class="py-3 px-6">Tgl Lahir</th>
                        <th scope="col" class="py-3 px-6">Umur</th>
                        <th scope="col" class="py-3 px-6">Jenis Kelamin</th>
                        <th scope="col" class="py-3 px-6">Alamat</th>
                        <th scope="col" class="py-3 px-6">RT/RW</th>
                        <th scope="col" class="py-3 px-6">Status</th>
                        <th scope="col" class="py-3 px-6">BMI</th>
                        <th scope="col" class="py-3 px-6">Keterangan</th>
                        <th scope="col" class="py-3 px-6">Note</th>
                        <th scope="col" class="py-3 px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataDewasa as $data)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="py-4 px-6">{{ $data->no_reg }}</td>
                        <td class="py-4 px-6">{{ $data->nik }}</td>
                        <td class="py-4 px-6">{{ $data->nama }}</td>
                        <td class="py-4 px-6">{{ $data->tanggal_lahir }}</td>
                        <td class="py-4 px-6">{{ $data->umur }}</td>
                        <td class="py-4 px-6">{{ $data->jenis_kelamin }}</td>
                        <td class="py-4 px-6">{{ $data->alamat }}</td>
                        <td class="py-4 px-6">{{ $data->rt }}/{{ $data->rw }}</td>
                        <td class="py-4 px-6">{{ $data->status }}</td>
                        <td class="py-4 px-6">{{ $data->bmi ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $data->keterangan ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $data->note ?? '-' }}</td>
                        <td class="py-4 px-6">
                            <a href="{{ route('daftar.dewasa.edit', $data->id) }}" 
                                class="text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 transition-colors">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr class="bg-white border-b">
                        <td colspan="12" class="py-4 px-6 text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $dataDewasa->links() }}
        </div>
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif

<script>
    // Auto-hide success alert after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.remove();
            }, 3000);
        }
    });
</script>
@endsection