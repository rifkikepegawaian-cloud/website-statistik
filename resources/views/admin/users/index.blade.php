{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')
@section('title','Kelola User')
@section('content')

<a href="{{ route('admin.dashboard') }}" class="inline-block mb-4 px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-900 text-sm font-semibold shadow">
  &larr; Kembali
</a>

<div class="bg-white rounded-lg shadow-lg p-6 card-shadow">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-xl font-semibold text-gray-800">Kelola User</h3>
    <button onclick="openCreateModal()"
            class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-semibold shadow">
      + Tambah User
    </button>
  </div>

  @if(session('status'))
    <div class="bg-green-50 text-green-700 p-3 rounded mb-4 text-sm">{{ session('status') }}</div>
  @endif
  @if($errors->any())
    <div class="bg-red-50 text-red-700 p-3 rounded mb-4 text-sm">
      <ul class="list-disc pl-4">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($users as $u)
          <tr>
            <td class="px-4 py-2">{{ $u->username }}</td>
            <td class="px-4 py-2">
              <span class="inline-block px-2 py-1 rounded text-white text-xs {{ $u->role==='admin'?'bg-purple-600':'bg-gray-600' }}">
                {{ strtoupper($u->role) }}
              </span>
            </td>
            <td class="px-4 py-2">{{ $u->created_at?->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
            <td class="px-4 py-2 space-x-2">
              <button class="px-3 py-1 rounded bg-yellow-500 text-white"
                      onclick="openEditModal({{ $u->id }}, '{{ $u->username }}', '{{ $u->role }}', '{{ route('admin.users.update',$u) }}')">
                Edit
              </button>
              @if(auth()->id() !== $u->id)
              <form action="{{ route('admin.users.destroy',$u) }}" method="post" class="inline" onsubmit="return confirm('Hapus user ini?')">
                @csrf @method('DELETE')
                <button class="px-3 py-1 rounded bg-red-600 text-white">Hapus</button>
              </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td class="px-4 py-3" colspan="4">Belum ada user.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Modal Tambah --}}
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
    <button onclick="closeCreateModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-xl">&times;</button>
    <h4 class="text-lg font-semibold mb-4">Tambah User</h4>
    <form action="{{ route('admin.users.store') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
        <input type="text" name="username" class="border rounded px-3 py-2 w-full" required>
      </div>

      <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <div class="relative">
          <input type="password" name="password" id="cPass" class="border rounded px-3 py-2 w-full pr-10" required>
          <button type="button"
                  class="pw-toggle absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-800"
                  data-target="cPass" aria-label="Tampilkan password" aria-pressed="false">
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

      <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
        <div class="relative">
          <input type="password" name="password_confirmation" id="cPass2" class="border rounded px-3 py-2 w-full pr-10" required>
          <button type="button"
                  class="pw-toggle absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-800"
                  data-target="cPass2" aria-label="Tampilkan password" aria-pressed="false">
            {{-- eye --}}
            <svg class="h-5 w-5 icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{-- eye-off --}}
            <svg class="h-5 w-5 icon-eye-off hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M6.61 6.61C4.74 7.77 3.34 9.5 2.46 12c1.27 4.06 5.06 7 9.54 7 1.53 0 2.98-.3 4.29-.84M17.39 17.39C19.26 16.23 20.66 14.5 21.54 12 20.27 7.94 16.48 5 12 5c-1.53 0-2.98.3-4.29.84" />
            </svg>
          </button>
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
        <select name="role" class="border rounded px-3 py-2 w-full" required>
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="flex justify-end gap-2">
        <button type="button" class="px-4 py-2 bg-gray-300 rounded" onclick="closeCreateModal()">Batal</button>
        <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
    <button onclick="closeEditModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-xl">&times;</button>
    <h4 class="text-lg font-semibold mb-4">Edit User</h4>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
        <input type="text" name="username" id="eUser" class="border rounded px-3 py-2 w-full" required>
      </div>

      <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (opsional)</label>
        <div class="relative">
          <input type="password" name="password" id="ePass" class="border rounded px-3 py-2 w-full pr-10" placeholder="Kosongkan jika tidak diubah">
          <button type="button"
                  class="pw-toggle absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-800"
                  data-target="ePass" aria-label="Tampilkan password" aria-pressed="false">
            {{-- eye --}}
            <svg class="h-5 w-5 icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{-- eye-off --}}
            <svg class="h-5 w-5 icon-eye-off hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M6.61 6.61C4.74 7.77 3.34 9.5 2.46 12c1.27 4.06 5.06 7 9.54 7 1.53 0 2.98-.3 4.29-.84M17.39 17.39C19.26 16.23 20.66 14.5 21.54 12 20.27 7.94 16.48 5 12 5c-1.53 0-2.98.3-4.29.84" />
            </svg>
          </button>
        </div>
      </div>

      <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
        <div class="relative">
          <input type="password" name="password_confirmation" id="ePass2" class="border rounded px-3 py-2 w-full pr-10" placeholder="Samakan jika mengubah password">
          <button type="button"
                  class="pw-toggle absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-800"
                  data-target="ePass2" aria-label="Tampilkan password" aria-pressed="false">
            {{-- eye --}}
            <svg class="h-5 w-5 icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{-- eye-off --}}
            <svg class="h-5 w-5 icon-eye-off hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M6.61 6.61C4.74 7.77 3.34 9.5 2.46 12c1.27 4.06 5.06 7 9.54 7 1.53 0 2.98-.3 4.29-.84M17.39 17.39C19.26 16.23 20.66 14.5 21.54 12 20.27 7.94 16.48 5 12 5c-1.53 0-2.98.3-4.29.84" />
            </svg>
          </button>
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
        <select name="role" id="eRole" class="border rounded px-3 py-2 w-full" required>
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="flex justify-end gap-2">
        <button type="button" class="px-4 py-2 bg-gray-300 rounded" onclick="closeEditModal()">Batal</button>
        <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  // Modal controls
  function openCreateModal(){
    const m = document.getElementById('createModal');
    m.classList.remove('hidden'); m.classList.add('flex');
  }
  function closeCreateModal(){
    const m = document.getElementById('createModal');
    m.classList.add('hidden'); m.classList.remove('flex');
  }
  function openEditModal(id, username, role, actionUrl){
    document.getElementById('eUser').value = username;
    document.getElementById('eRole').value = role || 'user';
    document.getElementById('ePass').value = '';
    document.getElementById('ePass2').value = '';
    document.getElementById('editForm').action = actionUrl;

    const m = document.getElementById('editModal');
    m.classList.remove('hidden'); m.classList.add('flex');
  }
  function closeEditModal(){
    const m = document.getElementById('editModal');
    m.classList.add('hidden'); m.classList.remove('flex');
  }

  // Toggle password (ikon eye / eye-off)
  (function(){
    const btns = document.querySelectorAll('.pw-toggle');
    btns.forEach(btn => {
      const targetId = btn.getAttribute('data-target');
      const input = document.getElementById(targetId);
      const eye = btn.querySelector('.icon-eye');
      const eyeOff = btn.querySelector('.icon-eye-off');
      if (!input || !eye || !eyeOff) return;

      btn.addEventListener('click', function(){
        const willShow = input.type === 'password';
        input.type = willShow ? 'text' : 'password';
        eye.classList.toggle('hidden', willShow);
        eyeOff.classList.toggle('hidden', !willShow);
        btn.setAttribute('aria-pressed', willShow ? 'true' : 'false');
        btn.setAttribute('aria-label', willShow ? 'Sembunyikan password' : 'Tampilkan password');
      });
    });
  })();
</script>
@endpush
@endsection
