@extends('layouts.app')
@section('title','Login Admin')
@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8 card-shadow">
  <h2 class="text-3xl font-bold text-gray-900 mb-2">Login Admin</h2>
  <p class="text-gray-600 mb-6">Masuk untuk mengelola data pegawai</p>

  @if($errors->any())
    <div class="bg-red-50 text-red-700 p-3 rounded mb-4 text-sm">{{ $errors->first() }}</div>
  @endif
  @if(session('error'))
    <div class="bg-red-50 text-red-700 p-3 rounded mb-4 text-sm">{{ session('error') }}</div>
  @endif

  <form method="post" action="{{ route('login.post') }}" class="space-y-4">
    @csrf
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
      <input name="username" value="{{ old('username') }}" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"/>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
      <input type="password" name="password" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"/>
    </div>
    <button class="w-full gradient-bg text-white py-2 px-4 rounded-md hover:opacity-90 font-medium">Masuk</button>
  </form>
  <div class="mt-4 text-sm text-gray-600 text-center">
    <p>Demo: username = admin, password = admin123</p>
  </div>
</div>
@endsection
