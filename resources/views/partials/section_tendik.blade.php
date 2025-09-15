<div class="mb-10">
  <div class="section-header p-6 rounded-2xl mb-8">
    <h3 class="section-title flex items-center"><span class="icon-container-green text-white px-4 py-2 rounded-full text-lg mr-4 shadow-lg">👥</span>Grafik Tenaga Kependidikan</h3>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
    <div class="rounded-2xl p-8 card-shadow card-premium"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Jenis Kelamin</h4><div class="w-12 h-12 icon-container rounded-2xl flex items-center justify-center"><span class="text-white text-lg">👥</span></div></div><div class="chart-container"><canvas id="tendikGenderChart"></canvas></div></div>
    <div class="rounded-2xl p-8 card-shadow card-premium"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Status Kepegawaian Tenaga Kependidikan</h4><div class="w-12 h-12 icon-container-purple rounded-2xl flex items-center justify-center"><span class="text-white text-lg">📋</span></div></div><div class="chart-container"><canvas id="tendikStatusChart"></canvas></div></div>
  </div>

  <div class="rounded-2xl p-8 card-shadow card-premium mb-8"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Golongan</h4><div class="w-12 h-12 icon-container-green rounded-2xl flex items-center justify-center"><span class="text-white text-lg">🏆</span></div></div><div class="chart-container"><canvas id="tendikGolonganChart"></canvas></div></div>
  <div class="rounded-2xl p-8 card-shadow card-premium mb-8"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Pendidikan Tenaga Kependidikan</h4><div class="w-12 h-12 icon-container-purple rounded-2xl flex items-center justify-center"><span class="text-white text-lg">🎓</span></div></div><div class="chart-container"><canvas id="tendikPendidikanChart"></canvas></div></div>

  <div class="rounded-2xl p-8 card-shadow card-premium mb-8">
    <div class="flex items-center justify-between mb-6">
      <h4 class="card-title">Pendidikan × Fakultas/Sekolah (Tendik)</h4>
      <div class="w-12 h-12 icon-container-blue rounded-2xl flex items-center justify-center">
        <span class="text-white text-lg">📚</span>
      </div>
    </div>
    <div id="tendikPendFakWrap" style="max-height:520px; overflow-y:auto">
      <canvas id="tendikPendidikanFakultasChart"></canvas>
    </div>
  </div>

  <div class="rounded-2xl p-8 card-shadow card-premium mb-8"><div class="flex items-center justify-between mb-6"><h4 class="card-title">Jabatan Tenaga Kependidikan</h4><div class="w-12 h-12 icon-container-orange rounded-2xl flex items-center justify-center"><span class="text-white text-lg">💼</span></div></div><div style="height:800px"><canvas id="tendikJabatanChart"></canvas></div></div>
  <div class="rounded-2xl p-8 card-shadow card-premium mb-8">
    <div class="flex items-center justify-between mb-6">
      <h4 class="card-title">Jumlah Tendik per Fakultas/Sekolah</h4>
      <div class="w-12 h-12 icon-container-blue rounded-2xl flex items-center justify-center">
        <span class="text-white text-lg">👥</span>
      </div>
    </div>
    <div id="tendikFakCountWrap" style="max-height:520px; overflow-y:auto">
      <canvas id="tendikFakultasCountChart"></canvas>
    </div>
  </div>

  
</div>