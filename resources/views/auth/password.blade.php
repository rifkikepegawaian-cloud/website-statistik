@extends('layouts.app')
@section('title','Ubah Password')
@section('content')
<a href="{{ route('admin.dashboard') }}"
    class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-900 hover:scale-105 transition text-sm font-semibold shadow mb-4">
  &larr; Kembali
</a>
<div class="bg-white rounded-lg shadow-lg p-6 card-shadow max-w-md mx-auto">
  <h3 class="text-xl font-semibold text-gray-800 mb-4">Ubah Password Admin</h3>
  @if(session('status'))
    <div class="bg-green-50 text-green-700 p-3 rounded mb-4 text-sm">{{ session('status') }}</div>
  @endif
  <form method="post" action="{{ route('admin.password.update') }}" class="space-y-4">
    @csrf
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
      <input type="password" name="current_password" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"/>
      @error('current_password')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
      <input type="password" name="new_password" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"/>
      @error('new_password')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
      <input type="password" name="new_password_confirmation" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"/>
    </div>
    <button class="gradient-bg text-white px-4 py-2 rounded-md hover:opacity-90">Ubah Password</button>
  </form>
</div>
@endsection
