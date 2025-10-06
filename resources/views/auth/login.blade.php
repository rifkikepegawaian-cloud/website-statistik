{{-- resources/views/auth/login.blade.php --}}
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

    {{-- Username --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
      <input
        name="username"
        value="{{ old('username') }}"
        required
        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        autocomplete="username"
      />
    </div>

    {{-- Password + toggle eye --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
      <div class="relative">
        <input
          id="passwordInput"
          type="password"
          name="password"
          required
          class="w-full pr-11 pl-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          autocomplete="current-password"
        />
        <button
          id="togglePassword"
          type="button"
          class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-gray-700 focus:outline-none"
          aria-label="Tampilkan password"
          aria-pressed="false"
        >
          {{-- eye (default tampil) --}}
          <svg class="h-5 w-5 icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          {{-- eye-off (default hidden) --}}
          <svg class="h-5 w-5 icon-eye-off hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M6.61 6.61C4.74 7.77 3.34 9.5 2.46 12c1.27 4.06 5.06 7 9.54 7 1.53 0 2.98-.3 4.29-.84M17.39 17.39C19.26 16.23 20.66 14.5 21.54 12 20.27 7.94 16.48 5 12 5c-1.53 0-2.98.3-4.29.84" />
          </svg>
        </button>
      </div>
    </div>

    <button class="w-full gradient-bg text-white py-2 px-4 rounded-md hover:opacity-90 font-medium">
      Masuk
    </button>
  </form>
</div>

@push('scripts')
<script>
  (function(){
    const input  = document.getElementById('passwordInput');
    const btn    = document.getElementById('togglePassword');
    const eye    = document.querySelector('.icon-eye');
    const eyeOff = document.querySelector('.icon-eye-off');
    if (!input || !btn || !eye || !eyeOff) return;

    btn.addEventListener('click', function(){
      const willShow = input.type === 'password'; // buka jika semula password
      input.type = willShow ? 'text' : 'password';

      eye.classList.toggle('hidden',  willShow);  // sembunyikan eye saat menampilkan
      eyeOff.classList.toggle('hidden', !willShow); // tampilkan eye-off saat menampilkan

      btn.setAttribute('aria-pressed', willShow ? 'true' : 'false');
      btn.setAttribute('aria-label', willShow ? 'Sembunyikan password' : 'Tampilkan password');
    });
  })();
</script>
@endpush
@endsection
