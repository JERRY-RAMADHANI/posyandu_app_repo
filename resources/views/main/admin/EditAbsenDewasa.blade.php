@extends('main.admin.layout')

@section('content')
<div class="min-h-screen p-6" style="background: linear-gradient(135deg, #FF9B00 0%, #FFE100 100%);">
    <div class="w-full mx-auto bg-white rounded-xl shadow-md p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-center" style="color: #FF9B00">Edit Data Absensi Dewasa - {{ date('d/m/Y', strtotime(session('tanggal_absen'))) }}</h2>
        </div>

        <!-- Search Bar -->
        <div class="mb-4">
            <form action="{{ route('edit.absen.dewasa') }}" method="GET" class="flex gap-2">
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
                        <th scope="col" class="py-3 px-6">Tanggal Lahir</th>
                        <th scope="col" class="py-3 px-6">Usia</th>
                        <th scope="col" class="py-3 px-6" style="min-width: 130px;">Alamat</th>
                        <th scope="col" class="py-3 px-6">BB</th>
                        <th scope="col" class="py-3 px-6">TB</th>
                        <th scope="col" class="py-3 px-6">LP</th>
                        <th scope="col" class="py-3 px-6">LILA</th>
                        <th scope="col" class="py-3 px-6">Tensi</th>
                        <th scope="col" class="py-3 px-6">AU</th>
                        <th scope="col" class="py-3 px-6">GDA</th>
                        <th scope="col" class="py-3 px-6">KOL</th>
                        <th scope="col" class="py-3 px-6">BMI</th>
                        <th scope="col" class="py-3 px-6">Status</th>
                        <th scope="col" class="py-3 px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataAbsenDewasa as $data)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="py-4 px-6">{{ $data->no_reg }}</td>
                        <td class="py-4 px-6">{{ $data->nik }}</td>
                        <td class="py-4 px-6">{{ $data->nama }}</td>
                        <td class="py-4 px-6">{{ $data->tanggal_lahir }}</td>
                        <td class="py-4 px-6">{{ $data->usia }}</td>
                        <td class="py-4 px-6">{{ $data->alamat }}</td>
                        <td class="py-4 px-6">{{ $data->bb ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $data->tb ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $data->lp ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $data->lila ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $data->sistole ?? '-' }}/{{ $data->diastole ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $data->au ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $data->gda ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $data->kol ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $data->bmi ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $data->hasil ?? '-' }}</td>
                        <td class="py-4 px-6">
                            <a href="{{ route('absen.dewasa.edit', $data->id) }}" 
                                class="text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 transition-colors">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr class="bg-white border-b">
                        <td colspan="14" class="py-4 px-6 text-center">Tidak ada data absensi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $dataAbsenDewasa->links() }}
        </div>
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif

@endsection