@extends('layouts.app')
@section('title','Detail Data Pegawai')
@section('content')
<div class="bg-white rounded-lg shadow-lg p-6 card-shadow">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold">Detail Data Pegawai</h3>
    <a href="{{ route('admin.uploads.index') }}" class="text-blue-600">Kembali</a>
  </div>
  <p class="text-sm text-gray-600 mb-4">
    File: <strong>{{ $upload->original_name }}</strong> &middot; {{ $upload->row_count }} baris
  </p>

  {{-- Controls: Search + per-page --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
    <div class="w-full md:w-1/2">
      <input id="searchInput" type="text" placeholder="Cari pada semua kolom..."
             class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="flex items-center gap-2">
      <span class="text-sm text-gray-600">Baris per halaman:</span>
      <select id="rowsPerPage" class="border rounded px-2 py-1">
        <option>10</option>
        <option selected>25</option>
        <option>50</option>
        <option>100</option>
      </select>
    </div>
  </div>

  <div class="overflow-x-auto max-h-[70vh] border rounded">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      @php
        $rows = $preview;
        $header = $rows[0] ?? [];
      @endphp
      @if(!empty($header))
        <thead class="bg-gray-50 sticky top-0 z-10">
          <tr>
            @foreach($header as $cell)
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ $cell }}</th>
            @endforeach
          </tr>
        </thead>
      @endif
      <tbody id="tableBody" class="divide-y divide-gray-200">
        {{-- di-render via JS --}}
      </tbody>
    </table>
  </div>

  {{-- Pagination footer --}}
  <div class="mt-3 flex items-center justify-between">
    <div class="text-sm text-gray-600">
      Menampilkan <span id="rangeText">0</span> dari <span id="totalText">0</span> baris
    </div>
    <div class="inline-flex items-center gap-2">
      <button id="prevBtn" class="px-3 py-1 border rounded disabled:opacity-50">Sebelumnya</button>
      <span id="pageInfo" class="text-sm text-gray-700">Hal 1/1</span>
      <button id="nextBtn" class="px-3 py-1 border rounded disabled:opacity-50">Berikutnya</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- SheetJS (CDN) untuk parse xlsx/xls/csv di browser -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
(function(){
  // Data awal dari PHP (preview termasuk header di index 0)
  let ALL_ROWS = @json($rows);
  let HEADER   = ALL_ROWS[0] || [];
  let ROWS     = ALL_ROWS.slice(1); // tanpa header

  // Elemen DOM
  const tbody      = document.getElementById('tableBody');
  const searchEl   = document.getElementById('searchInput');
  const perPageEl  = document.getElementById('rowsPerPage');
  const prevBtn    = document.getElementById('prevBtn');
  const nextBtn    = document.getElementById('nextBtn');
  const pageInfo   = document.getElementById('pageInfo');
  const rangeText  = document.getElementById('rangeText');
  const totalText  = document.getElementById('totalText');

  // State
  let filtered = ROWS.slice();
  let currentPage = 1;

  function render() {
    const perPage = parseInt(perPageEl.value, 10) || 25;
    const total   = filtered.length;
    const pages   = Math.max(1, Math.ceil(total / perPage));
    if (currentPage > pages) currentPage = pages;

    const start = (currentPage - 1) * perPage;
    const end   = Math.min(start + perPage, total);

    // Render body
    tbody.innerHTML = '';
    for (let i = start; i < end; i++) {
      const tr = document.createElement('tr');
      const row = filtered[i] || [];
      // pastikan jumlah kolom konsisten dengan header
      const cols = Math.max(HEADER.length, row.length);
      for (let c = 0; c < cols; c++) {
        const td = document.createElement('td');
        td.className = 'px-3 py-2 text-sm text-gray-900';
        td.textContent = row[c] ?? '';
        tr.appendChild(td);
      }
      tbody.appendChild(tr);
    }

    // UI info
    rangeText.textContent = total === 0 ? '0' : `${start + 1}-${end}`;
    totalText.textContent = total;
    pageInfo.textContent  = `Hal ${currentPage}/${pages}`;
    prevBtn.disabled = currentPage <= 1;
    nextBtn.disabled = currentPage >= pages;
  }

  function applyFilter() {
    const q = (searchEl.value || '').toLowerCase();
    if (!q) {
      filtered = ROWS.slice();
    } else {
      filtered = ROWS.filter(r => r.some(cell => String(cell ?? '').toLowerCase().includes(q)));
    }
    currentPage = 1;
    render();
  }

  // Events
  searchEl.addEventListener('input', applyFilter);
  perPageEl.addEventListener('change', () => { currentPage = 1; render(); });
  prevBtn.addEventListener('click', () => { if (currentPage > 1) { currentPage--; render(); } });
  nextBtn.addEventListener('click', () => {
    const perPage = parseInt(perPageEl.value, 10) || 25;
    const pages   = Math.max(1, Math.ceil(filtered.length / perPage));
    if (currentPage < pages) { currentPage++; render(); }
  });

  // 1) Render awal pakai preview dari DB (cepat)
  render();

  // 2) Ambil file asli -> parse semua baris -> render ulang penuh
  const fileUrl = @json(route('admin.uploads.file', $upload));
  fetch(fileUrl, { credentials: 'same-origin' })
    .then(r => {
      if (!r.ok) throw new Error('Gagal mengambil file asli');
      return r.arrayBuffer();
    })
    .then(buf => {
      const wb = XLSX.read(new Uint8Array(buf), { type: 'array' });
      const ws = wb.Sheets[wb.SheetNames[0]];
      const all = XLSX.utils.sheet_to_json(ws, { header: 1 }) || [];

      if (all.length > 0) {
        ALL_ROWS = all;
        HEADER   = ALL_ROWS[0] || [];
        ROWS     = ALL_ROWS.slice(1);
        filtered = ROWS.slice();
        currentPage = 1;
        render(); // ← sekarang tabel menampilkan SEMUA baris
      }
    })
    .catch(err => {
      console.warn('Gagal memuat penuh, gunakan preview dari DB.', err);
    });
})();
</script>
@endpush

