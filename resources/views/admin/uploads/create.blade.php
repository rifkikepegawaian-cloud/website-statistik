@extends('layouts.app')
@section('title','Upload Data Pegawai')
@section('content')
<a href="{{ route('admin.dashboard') }}" class="inline-block mb-4 px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-900 hover:scale-105 transition-colors text-sm font-semibold shadow outline outline-1 outline-white">
  &larr; Kembali
</a>
<div class="bg-white rounded-lg shadow-lg p-6 card-shadow">
  <h3 class="text-xl font-semibold text-gray-800 mb-4">Upload Data Pegawai</h3>
  <div class="mb-4">
    <p class="text-sm text-gray-600 mb-2">Format Excel minimal memiliki header yang dapat dikenali: <code>JNS KEL</code>, <code>JENIS PEG</code>, <code>GOLONGAN</code>, <code>JABATAN</code>, <code>PENDIDIKAN</code>, <code>STATUS</code>/<code>STATUS BEKERJA</code>, <code>FAKULTAS/SEKOLAH</code>.</p>
  </div>

  <form id="upload_form" method="post" action="{{ route('uploads.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="file" id="fileInput" name="file" accept=".xlsx,.xls,.csv" class="hidden">
    <input type="hidden" name="stats_json" id="stats_json">
    <input type="hidden" name="row_count" id="row_count">
    <input type="hidden" name="preview_json" id="preview_json">
  </form>

  <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
    <button onclick="document.getElementById('fileInput').click()" class="gradient-bg text-white px-4 py-2 rounded-md hover:opacity-90 transition-opacity">
      Pilih File Excel
    </button>
    <p class="text-sm text-gray-500 mt-2">atau drag & drop file di sini</p>
    <p class="text-xs text-gray-400 mt-1">Format: .xlsx / .xls / .csv</p>
  </div>

  <div id="previewSection" class="hidden mt-6">
    <h4 class="text-lg font-medium text-gray-800 mb-3">Preview Data</h4>
    <div class="overflow-x-auto max-h-64 border rounded">
      <table id="previewTable" class="min-w-full divide-y divide-gray-200 text-sm"></table>
    </div>
    <div class="mt-4 flex space-x-3">
      <button id="btnSave" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors">Simpan Data</button>
      <button id="btnCancel" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">Batal</button>
    </div>
  </div>
</div>
@endsection
