{{-- resources/views/auth/password.blade.php --}}
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

    {{-- Password Lama --}}
    <div>
      <label for="oldPass" class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
      <div class="relative">
        <input id="oldPass" type="password" name="current_password"
               required
               class="w-full px-3 py-2 pr-10 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        <button type="button"
                class="pwd-toggle absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-800"
                data-target="oldPass" aria-label="Tampilkan/sembunyikan password">
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
      @error('current_password')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
    </div>

    {{-- Password Baru --}}
    <div>
      <label for="newPass" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
      <div class="relative">
        <input id="newPass" type="password" name="new_password"
               required
               class="w-full px-3 py-2 pr-10 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        <button type="button"
                class="pwd-toggle absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-800"
                data-target="newPass" aria-label="Tampilkan/sembunyikan password">
          <svg class="h-5 w-5 icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <svg class="h-5 w-5 icon-eye-off hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M6.61 6.61C4.74 7.77 3.34 9.5 2.46 12c1.27 4.06 5.06 7 9.54 7 1.53 0 2.98-.3 4.29-.84M17.39 17.39C19.26 16.23 20.66 14.5 21.54 12 20.27 7.94 16.48 5 12 5c-1.53 0-2.98.3-4.29.84" />
          </svg>
        </button>
      </div>
      @error('new_password')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
    </div>

    {{-- Konfirmasi Password Baru --}}
    <div>
      <label for="newPass2" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
      <div class="relative">
        <input id="newPass2" type="password" name="new_password_confirmation"
               required
               class="w-full px-3 py-2 pr-10 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        <button type="button"
                class="pwd-toggle absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-800"
                data-target="newPass2" aria-label="Tampilkan/sembunyikan password">
          <svg class="h-5 w-5 icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <svg class="h-5 w-5 icon-eye-off hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M6.61 6.61C4.74 7.77 3.34 9.5 2.46 12c1.27 4.06 5.06 7 9.54 7 1.53 0 2.98-.3 4.29-.84M17.39 17.39C19.26 16.23 20.66 14.5 21.54 12 20.27 7.94 16.48 5 12 5c-1.53 0-2.98.3-4.29.84" />
          </svg>
        </button>
      </div>
    </div>

    <button class="gradient-bg text-white px-4 py-2 rounded-md hover:opacity-90">Ubah Password</button>
  </form>
</div>
@endsection

@push('scripts')
<script>
  // Inisialisasi toggle untuk semua tombol berkela 'pwd-toggle'
  document.querySelectorAll('.pwd-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.target;
      const input = document.getElementById(targetId);
      if (!input) return;

      const isHidden = (input.type === 'password'); // saat ini tertutup?
      input.type = isHidden ? 'text' : 'password';  // ubah tipe

      // swap ikon: jika password kini terlihat (text), tampilkan eye-off, sembunyikan eye
      const eye    = btn.querySelector('.icon-eye');
      const eyeOff = btn.querySelector('.icon-eye-off');
      if (eye && eyeOff) {
        eye.classList.toggle('hidden', isHidden);     // hidden saat baru dibuka
        eyeOff.classList.toggle('hidden', !isHidden); // tampil saat baru dibuka
      }
    });
  });
</script>
@endpush
