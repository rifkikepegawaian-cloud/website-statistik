@extends('layouts.app')

@section('title','Statistik Pegawai')
@section('meta_description', 'Portal resmi visualisasi data dan statistik kepegawaian Universitas Diponegoro. Menyajikan informasi dan grafik analitik Dosen serta Tenaga Kependidikan berdasarkan unit kerja, fakultas, jabatan, jenjang pendidikan, dan golongan.')

@section('content')
<div class="mb-8 text-center">
  <h2 class="text-4xl font-bold text-white mb-4 drop-shadow-lg">Statistik Pegawai</h2>
  <p class="text-3xl font-semibold text-white drop-shadow-md">Universitas Diponegoro</p>

  <div class="mt-4">
    <label class="block text-sm font-medium text-white mb-2 drop-shadow-sm">Pilih Data Berdasarkan Tanggal Upload:</label>
    <select id="dateSelector" class="border border-gray-300 rounded-md px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      @foreach($uploads as $u)
        <option value="{{ $u->id }}" {{ $loop->first ? 'selected' : '' }}>
          {{ $u->uploaded_at?->timezone('Asia/Jakarta')->translatedFormat('d F Y') ?? 'Tanpa tanggal' }}
        </option>
      @endforeach
    </select>
  </div>
</div>

@include('partials.section_umum')
@include('partials.section_dosen')
@include('partials.section_tendik')
@include('partials.section_fakultas')

@endsection

@push('scripts')
<script>
const STATS_URL = (id) => `{{ url('/api/uploads') }}/${id}/stats`;
async function loadAndRender() {
  const id = document.getElementById('dateSelector').value;
  const res = await fetch(STATS_URL(id));
  const stats = await res.json();
  window.renderAllCharts(stats);
}
document.getElementById('dateSelector').addEventListener('change', loadAndRender);
window.addEventListener('DOMContentLoaded', loadAndRender);
</script>
@endpush
