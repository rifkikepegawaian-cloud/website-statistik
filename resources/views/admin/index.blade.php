@extends('layouts.app')
@section('title','Panel Admin')
@section('content')
<div class="mb-6">
  <h2 class="text-4xl font-bold text-white drop-shadow-lg mb-2">Panel Admin</h2> 
  <p class="font-semibold text-white">Kelola data pegawai UNDIP</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <a href="{{ route('admin.uploads.index') }}" class="bg-white rounded-lg shadow-lg p-6 card-shadow hover:shadow-xl transition-shadow text-left">
    <div class="text-2xl mb-2">📊</div>
    <h3 class="text-lg font-semibold text-gray-800">Kelola Data Pegawai</h3>
    <p class="text-gray-600 text-sm">Lihat riwayat data yang telah diunggah</p>
  </a>

  @can('uploads.create')
    <a href="{{ route('admin.uploads.create') }}" class="bg-white rounded-lg shadow-lg p-6 card-shadow hover:shadow-xl transition-shadow text-left">
      <div class="text-2xl mb-2">📤</div>
      <h3 class="text-lg font-semibold text-gray-800">Tambah Data Pegawai</h3>
      <p class="text-gray-600 text-sm">Upload file Excel data pegawai</p>
    </a>
  @endcan

  <a href="{{ route('admin.password.show') }}" class="bg-white rounded-lg shadow-lg p-6 card-shadow hover:shadow-xl transition-shadow text-left">
    <div class="text-2xl mb-2">🔒</div>
    <h3 class="text-lg font-semibold text-gray-800">Ubah Password</h3>
    <p class="text-gray-600 text-sm">Ganti password</p>
  </a>
</div>
@endsection
