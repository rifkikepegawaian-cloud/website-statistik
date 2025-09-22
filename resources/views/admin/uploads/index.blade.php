@extends('layouts.app')
@section('title','Riwayat Data Pegawai')
@section('content')

<div class="flex items-center justify-between mb-4">
  <a href="{{ route('admin.dashboard') }}"
     class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-900 hover:scale-105 transition text-sm font-semibold shadow">
    &larr; Kembali
  </a>

  @can('uploads.create')
    <a href="{{ route('admin.uploads.create') }}"
       class="inline-block px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm font-semibold shadow">
      + Tambah Data
    </a>
  @endcan
</div>

<div class="bg-white rounded-lg shadow-lg p-6 card-shadow">
  <h3 class="text-xl font-semibold text-gray-800 mb-4">Riwayat Data Pegawai</h3>

  @if(session('status'))
    <div class="bg-green-50 text-green-700 p-3 rounded mb-4 text-sm">{{ session('status') }}</div>
  @endif

  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Upload</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Data</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse($uploads as $u)
          <tr>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ $u->uploaded_at?->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $u->row_count }}</td>
            <td class="px-6 py-4 whitespace-nowrap space-x-2">
              <a href="{{ route('admin.uploads.show', $u) }}"
                 class="px-3 py-1 rounded bg-blue-600 text-white text-sm">Lihat</a>

              <a href="{{ route('admin.uploads.download', $u) }}"
                 class="px-3 py-1 rounded bg-emerald-600 text-white text-sm">Unduh</a>

              @can('uploads.update')
                <button type="button"
                        class="px-3 py-1 rounded bg-yellow-500 text-white text-sm"
                        data-id="{{ $u->id }}"
                        data-date="{{ $u->uploaded_at?->format('Y-m-d') }}"
                        data-name="{{ $u->original_name }}"
                        data-update-url="{{ route('admin.uploads.update', $u) }}"
                        onclick="openEditModal(this)">
                  Edit
                </button>
              @endcan

              @can('uploads.delete')
                <form action="{{ route('admin.uploads.destroy', $u) }}"
                      method="post" class="inline"
                      onsubmit="return confirm('Hapus data ini?')">
                  @csrf @method('DELETE')
                  <button class="px-3 py-1 rounded bg-red-600 text-white text-sm">Hapus</button>
                </form>
              @endcan
            </td>
          </tr>
        @empty
          <tr>
            <td class="px-6 py-4" colspan="3">Belum ada data.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
    <button onclick="closeEditModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-xl">&times;</button>
    <h4 class="text-lg font-semibold mb-4">Edit Data Upload</h4>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Upload</label>
        <input type="date" name="uploaded_at" id="editUploadedAt" class="border rounded px-3 py-2 w-full" required>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama File</label>
        <input type="text" id="editFileName" class="border rounded px-3 py-2 w-full bg-gray-100 truncate" readonly>
      </div>

      <div class="flex justify-end">
        <button type="button" onclick="closeEditModal()" class="px-4 py-2 mr-2 rounded bg-gray-300 text-gray-700 hover:bg-gray-400">Batal</button>
        <button type="submit" class="px-4 py-2 rounded bg-blue-700 text-white hover:bg-blue-900">Simpan</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openEditModal(btn) {
    const date  = btn.dataset.date || '';
    const name  = btn.dataset.name || '';
    const url   = btn.dataset.updateUrl;

    document.getElementById('editUploadedAt').value = date;
    document.getElementById('editFileName').value   = name;

    // set action form ke route update yang dikirim dari blade (aman & tidak hardcode)
    document.getElementById('editForm').action = url;

    document.getElementById('editModal').classList.remove('hidden');
  }
  function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
  }
</script>
@endpush

@endsection
