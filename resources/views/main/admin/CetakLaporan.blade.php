@extends('main.admin.layout')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-center">
        <a href="{{ route('Export.AbsenBalita') }}" 
           class="px-6 py-4 rounded-2xl shadow-lg font-semibold text-white hover:scale-105 transition transform duration-200"
           style="background-color: #FF9B00;">
            Download File Absen Balita
        </a>
        
        <a href="{{ route('Export.AbsenDewasa') }}" 
           class="px-6 py-4 rounded-2xl shadow-lg font-semibold text-white hover:scale-105 transition transform duration-200"
           style="background-color: #FF9B00;">
            Download File Absen Dewasa
        </a>
        
        <a 
           class="px-6 py-4 rounded-2xl shadow-lg font-semibold text-black hover:scale-105 transition transform duration-200"
           style="background-color: #FFC900;">
            Download File 3
        </a>
        
        <a
           class="px-6 py-4 rounded-2xl shadow-lg font-semibold text-black hover:scale-105 transition transform duration-200"
           style="background-color: #EBE389;">
            Download File 4
        </a>
    </div>
</div>
@endsection

