<div class="mb-10">
  <div class="section-header p-6 rounded-2xl mb-8">
    <h3 class="section-title flex items-center"><span class="icon-container text-white px-4 py-2 rounded-full text-lg mr-4 shadow-lg">👨‍🏫</span>Grafik Tenaga Dosen</h3>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 mb-8">
    <div class="rounded-2xl p-8 card-shadow card-premium"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Jenis Kelamin</h4><div class="w-12 h-12 icon-container rounded-2xl flex items-center justify-center"><span class="text-white text-lg">👥</span></div></div><div class="chart-container"><canvas id="dosenGenderChart"></canvas></div></div>
    <div class="rounded-2xl p-8 card-shadow card-premium"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Jabatan</h4><div class="w-12 h-12 icon-container-orange rounded-2xl flex items-center justify-center"><span class="text-white text-lg">💼</span></div></div><div class="chart-container"><canvas id="dosenJabatanChart"></canvas></div></div>
    <div class="rounded-2xl p-8 card-shadow card-premium"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Pendidikan</h4><div class="w-12 h-12 icon-container-purple rounded-2xl flex items-center justify-center"><span class="text-white text-lg">🎓</span></div></div><div class="chart-container"><canvas id="dosenPendidikanChart"></canvas></div></div> 
  </div>
  
  <div class="rounded-2xl p-8 card-shadow card-premium mb-8">
    <div class="flex items-center justify-between mb-6">
      <h4 class="card-title">Jabatan × Jenis Kelamin (Dosen)</h4>
      <div class="w-12 h-12 icon-container rounded-2xl flex items-center justify-center">
        <span class="text-white text-lg">⚖️</span>
      </div>
    </div>

    <!-- wrapper agar bisa scroll bila jabatannya banyak -->
    <div id="dosenJabatanGenderWrap" style="max-height:520px; overflow-y:auto">
      <canvas id="dosenJabatanGenderChart"></canvas>
    </div>
  </div>

  <div class="rounded-2xl p-8 card-shadow card-premium mb-8">
    <div class="flex items-center justify-between mb-6">
      <h4 class="card-title">Jumlah Dosen per Jabatan × Fakultas/Sekolah</h4>
      <div class="w-12 h-12 icon-container rounded-2xl flex items-center justify-center">
        <span class="text-white text-lg">🏫</span>
      </div>
    </div>

    {{-- wrapper agar bisa scroll vertikal bila banyak fakultas --}}
    <div id="dosenJabFakWrap" style="max-height:520px; overflow-y:auto">
      <canvas id="dosenJabatanFakultasChart"></canvas>
    </div>
  </div>

  <div class="rounded-2xl p-8 card-shadow card-premium mb-8">
    <div class="flex items-center justify-between mb-6">
      <h4 class="card-title">Pendidikan × Fakultas/Sekolah (Dosen)</h4>
      <div class="w-12 h-12 icon-container rounded-2xl flex items-center justify-center">
        <span class="text-white text-lg">🎓</span>
      </div>
    </div>
    <div id="dosenPendFakWrap" style="max-height:520px; overflow-y:auto">
      <canvas id="dosenPendidikanFakultasChart"></canvas>
    </div>
  </div>

  {{-- <div class="rounded-2xl p-8 card-shadow card-premium mb-8"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Jabatan × Jenis Kelamin (Dosen)</h4><div class="w-12 h-12 icon-container-green rounded-2xl flex items-center justify-center"><span class="text-white text-lg">⚖️</span></div></div><div class="chart-container"><canvas id="dosenJabatanGenderChart"></canvas></div></div> --}}
  <div class="rounded-2xl p-8 card-shadow card-premium mb-8"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Golongan</h4><div class="w-12 h-12 icon-container-green rounded-2xl flex items-center justify-center"><span class="text-white text-lg">🏆</span></div></div><div class="chart-container"><canvas id="dosenGolonganChart"></canvas></div></div>
  <div class="rounded-2xl p-8 card-shadow card-premium mb-8"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Status Kepegawaian Dosen</h4><div class="w-12 h-12 icon-container-purple rounded-2xl flex items-center justify-center"><span class="text-white text-lg">📋</span></div></div><div class="chart-container"><canvas id="dosenStatusChart"></canvas></div></div>
  <div class="rounded-2xl p-8 card-shadow card-premium mb-8"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Jumlah Guru Besar Per Fakultas/Sekolah</h4><div class="w-12 h-12 icon-container-orange rounded-2xl flex items-center justify-center"><span class="text-white text-lg">👑</span></div></div><div style="height:400px"><canvas id="guruBesarFakultasChart"></canvas></div></div>
  <div class="rounded-2xl p-8 card-shadow card-premium mb-8"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Jumlah Dosen Pendidikan S3 Per Fakultas/Sekolah</h4><div class="w-12 h-12 icon-container-green rounded-2xl flex items-center justify-center"><span class="text-white text-lg">🎓</span></div></div><div style="height:400px"><canvas id="dosenS3FakultasChart"></canvas></div></div>
  <div class="rounded-2xl p-8 card-shadow card-premium"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Jumlah Total Dosen Per Fakultas/Sekolah</h4><div class="w-12 h-12 icon-container rounded-2xl flex items-center justify-center"><span class="text-white text-lg">📊</span></div></div><div style="height:400px"><canvas id="totalDosenFakultasChart"></canvas></div></div>
</div>