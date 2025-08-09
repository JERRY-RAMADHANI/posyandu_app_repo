@extends('main.admin.layout')

@section('content')
<div class="container mx-auto py-6 grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- ABSENSI BALITA --}}
    <div class="bg-white rounded-lg shadow">
        <h2 class="bg-orange-600 text-white text-lg font-semibold p-3 rounded-t-lg text-center">ABSENSI BALITA</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs uppercase bg-orange-600 text-white">
                    <tr>
                        <th class="px-3 py-2 text-center w-12">NO</th>
                        <th class="px-3 py-2 text-center w-20">NO REG</th>
                        <th class="px-3 py-2">NAMA</th>
                        <th class="px-3 py-2">ALAMAT</th>
                        <th class="px-3 py-2 text-center w-14">USIA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balita as $index => $b)
                        <tr class="border-b hover:bg-orange-100">
                            <td class="px-3 py-2 text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 text-center">{{ $b->no_reg }}</td>
                            <td class="px-3 py-2">{{ $b->nama }}</td>
                            <td class="px-3 py-2">{{ $b->alamat }}</td>
                            <td class="px-3 py-2 text-center">{{ $b->usia }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ABSENSI DEWASA --}}
    <div class="bg-white rounded-lg shadow">
        <h2 class="bg-orange-600 text-white text-lg font-semibold p-3 rounded-t-lg text-center">ABSENSI DEWASA</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs uppercase bg-orange-600 text-white">
                    <tr>
                        <th class="px-3 py-2 text-center w-12">NO</th>
                        <th class="px-3 py-2 text-center w-20">NO REG</th>
                        <th class="px-3 py-2">NAMA</th>
                        <th class="px-3 py-2">ALAMAT</th>
                        <th class="px-3 py-2 text-center w-14">USIA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dewasa as $index => $d)
                        <tr class="border-b hover:bg-orange-100">
                            <td class="px-3 py-2 text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 text-center">{{ $d->no_reg }}</td>
                            <td class="px-3 py-2">{{ $d->nama }}</td>
                            <td class="px-3 py-2">{{ $d->alamat }}</td>
                            <td class="px-3 py-2 text-center">{{ $d->usia }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection