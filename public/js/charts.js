// ========== UTIL & UI CHARTS ==========
window.ChartUI = (function(){
  function slug(s){ return String(s||'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,''); }
  function inferTitle(canvas){
    const card = canvas.closest('.card-premium, .rounded-2xl, .card, .bg-white') || canvas.parentElement;
    const h = card?.querySelector('.card-title, h4, h3');
    return h?.textContent?.trim() || (Chart.getChart(canvas)?.config?.type || 'chart');
  }
  function extractMatrix(chart){
    const labels = chart?.data?.labels || [];
    const dsets  = chart?.data?.datasets || [];
    // Pie/doughnut: 1 dataset dengan banyak label
    if (['pie','doughnut'].includes(chart.config.type)) {
      const data = (dsets[0]?.data || []);
      const rows = labels.map((l,i)=> [l, Number(data[i]||0)]);
      return { headers: ['Nama','Jumlah'], rows };
    }
    // Bar/line dll:
    if (dsets.length <= 1) {
      const data = (dsets[0]?.data || []);
      const rows = labels.map((l,i)=> [l, Number(data[i]||0)]);
      const hdr  = ['Nama','Jumlah'];
      return { headers: hdr, rows };
    } else {
      const hdr = ['Nama', ...dsets.map((ds,idx)=> ds.label || `Data ${idx+1}`), 'Total'];
      const rows = labels.map((l,i)=>{
        const vals = dsets.map(ds => Number(ds.data?.[i]||0));
        const tot = vals.reduce((a,b)=>a+b,0);
        return [l, ...vals, tot];
      });
      return { headers: hdr, rows };
    }
  }
  function tableToDOM(headers, rows){
    const thead = document.getElementById('cdmThead');
    const tbody = document.getElementById('cdmTbody');
    thead.innerHTML = ''; tbody.innerHTML = '';
    const trH = document.createElement('tr');
    headers.forEach(h=>{
      const th = document.createElement('th');
      th.className = 'px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase';
      th.textContent = h;
      trH.appendChild(th);
    });
    thead.appendChild(trH);
    rows.forEach(r=>{
      const tr = document.createElement('tr');
      r.forEach(c=>{
        const td = document.createElement('td');
        td.className = 'px-3 py-2 text-gray-900';
        td.textContent = c;
        tr.appendChild(td);
      });
      tbody.appendChild(tr);
    });
  }
  function openModal(title, headers, rows, subtitle, filenameBase){
    document.getElementById('cdmTitle').textContent = title;
    document.getElementById('cdmSubtitle').textContent = subtitle || '';
    tableToDOM(headers, rows);
    const modal = document.getElementById('chartDataModal');
    modal.classList.remove('hidden');
    const fn = (slug(filenameBase||title) || 'chart-data') + '.xlsx';
    const btn = document.getElementById('cdmDownloadExcel');
    btn.onclick = () => downloadExcel(headers, rows, fn);
  }
  function closeModal(){
    document.getElementById('chartDataModal').classList.add('hidden');
  }
  function downloadExcel(headers, rows, filename){
    if (!window.XLSX) {
      alert('Library XLSX tidak tersedia. Pastikan CDN SheetJS sudah dimuat.');
      return;
    }
    const aoa = [headers, ...rows];
    const wb  = XLSX.utils.book_new();
    const ws  = XLSX.utils.aoa_to_sheet(aoa);
    XLSX.utils.book_append_sheet(wb, ws, 'Data');
    XLSX.writeFile(wb, filename);
  }
  function saveImage(chart, title){
    const a = document.createElement('a');
    a.download = (slug(title)||'chart') + '.png';
    a.href = chart.toBase64Image('image/png', 1);
    a.click();
  }

  // === Toolbar horizontal di samping icon (robust antar tanggal) ===
  function attachToolbar(canvas){
    if (!canvas) return;
    if (canvas.dataset.toolbarAttached === '1') return;

    // util lokal
    const slug = s => String(s||'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
    function extractMatrix(ch){
      const labels = ch?.data?.labels || [];
      const dsets  = ch?.data?.datasets || [];
      if (['pie','doughnut'].includes(ch.config.type)) {
        const data = dsets[0]?.data || [];
        return { headers:['Nama','Jumlah'], rows: labels.map((l,i)=>[l, Number(data[i]||0)]) };
      }
      if (dsets.length <= 1) {
        const data = dsets[0]?.data || [];
        return { headers:['Nama','Jumlah'], rows: labels.map((l,i)=>[l, Number(data[i]||0)]) };
      }
      const hdr = ['Nama', ...dsets.map((ds,idx)=> ds.label || `Data ${idx+1}`), 'Total'];
      const rows = labels.map((l,i)=>{
        const vals = dsets.map(ds => Number(ds.data?.[i]||0));
        const tot  = vals.reduce((a,b)=>a+b,0);
        return [l, ...vals, tot];
      });
      return { headers: hdr, rows };
    }
    function saveCanvasAsPNG(ch, filename){
      if (!ch) return;
      const src = ch.canvas;

      // clone + latar putih (anti transparan)
      const tmp = document.createElement('canvas');
      // high-DPI aware
      const scale = window.devicePixelRatio || 1;
      tmp.width  = src.width  * scale;
      tmp.height = src.height * scale;

      const ctx = tmp.getContext('2d');
      ctx.scale(scale, scale);
      ctx.fillStyle = 'rgba(255, 255, 255, 0)';
      ctx.fillRect(0, 0, tmp.width, tmp.height);
      ctx.drawImage(src, 0, 0);

      const dataURL = tmp.toDataURL('image/png');
      const a = document.createElement('a');
      a.href = dataURL;
      a.download = (filename || 'chart') + '.png';
      document.body.appendChild(a);
      a.click();
      a.remove();
      tmp.remove();
    }

    const card   = canvas.closest('.card-premium, .rounded-2xl, .card, .bg-white') || canvas.parentElement;
    const icon   = card?.querySelector('.icon-container, .icon-container-blue, .icon-container-orange, .icon-container-purple, .w-12.h-12.rounded-2xl');
    const header = icon?.parentElement;

    // fallback kalau tidak ada icon/header → pojok kanan atas
    if (!card || !icon || !header) {
      const title = (card?.querySelector('.card-title, h4, h3')?.textContent?.trim()) || 'chart';
      card.style.position = card.style.position || 'relative';
      const bar = document.createElement('div');
      bar.className = 'chart-toolbar-side absolute right-3 top-2 z-10 flex items-center gap-2';
      bar.innerHTML = `
        <button class="px-3 py-1.5 rounded-xl shadow text-xs font-semibold bg-slate-700/85 text-white hover:bg-slate-800">Save Image</button>
        <button class="px-3 py-1.5 rounded-xl shadow text-xs font-semibold bg-amber-500 text-white hover:bg-amber-600">Data</button>
      `;
      const [btnSave, btnData] = bar.querySelectorAll('button');

      btnSave.onclick = () => {
        const ch = Chart.getChart(canvas);             // ← AMBIL CHART TERKINI
        if (!ch) return alert('Chart belum siap.');
        saveCanvasAsPNG(ch, slug(title));
      };
      btnData.onclick = () => {
        const ch = Chart.getChart(canvas);
        if (!ch) return alert('Chart belum siap.');
        const m = extractMatrix(ch);
        openModal(title, m.headers, m.rows, `Total entri: ${m.rows.length}`, title);
      };

      (card || canvas.parentElement).appendChild(bar);
      canvas.dataset.toolbarAttached = '1';
      return;
    }

    // posisikan absolute di samping icon
    header.style.position = header.style.position || 'relative';
    header.querySelectorAll('.chart-toolbar-side').forEach(n => n.remove());

    const bar = document.createElement('div');
    bar.className = 'chart-toolbar-side absolute z-10 flex items-center gap-2';
    bar.innerHTML = `
      <button class="px-3 py-1.5 rounded-xl shadow text-xs font-semibold bg-slate-700/85 text-white hover:bg-slate-800">Save Image</button>
      <button class="px-3 py-1.5 rounded-xl shadow text-xs font-semibold bg-amber-500 text-white hover:bg-amber-600">Data</button>
    `;
    const [btnSave, btnData] = bar.querySelectorAll('button');

    const title = (card.querySelector('.card-title, h4, h3')?.textContent?.trim()) || 'chart';

    btnSave.onclick = () => {
      const ch = Chart.getChart(canvas);               // ← AMBIL CHART TERKINI
      if (!ch) return alert('Chart belum siap.');
      saveCanvasAsPNG(ch, slug(title));
    };
    btnData.onclick = () => {
      const ch = Chart.getChart(canvas);
      if (!ch) return alert('Chart belum siap.');
      const m = extractMatrix(ch);
      openModal(title, m.headers, m.rows, `Total entri: ${m.rows.length}`, title);
    };

    function place(){
      const hRect = header.getBoundingClientRect();
      const iRect = icon.getBoundingClientRect();
      const gap   = 10;
      const rightOffset = Math.max(0, Math.round(hRect.right - iRect.left + gap));
      bar.style.right = rightOffset + 'px';
      const top = icon.offsetTop + (icon.offsetHeight / 2);
      bar.style.top = top + 'px';
      bar.style.transform = 'translateY(-50%)';
    }

    header.appendChild(bar);
    place();
    window.addEventListener('resize', place, { passive: true });

    canvas.dataset.toolbarAttached = '1';
  }

  // === Force re-attach setiap render ulang ===
  function attachAllChartToolbars(force = false){
    if (force) {
      document.querySelectorAll('.chart-toolbar-side').forEach(n => n.remove());
      document.querySelectorAll('canvas').forEach(cv => { delete cv.dataset.toolbarAttached; });
    }
    document.querySelectorAll('canvas').forEach(cv => {
      const ch = Chart.getChart(cv);
      if (ch) attachToolbar(cv);
    });
  }

  function attachAll(){
    document.querySelectorAll('canvas').forEach(cv=>{
      const ch = Chart.getChart(cv);
      if (ch) attachToolbar(cv, ch);
    });
  }
  return { attachAllChartToolbars: attachAll, openModal, closeModal };
})();

/* global Chart, ChartDataLabels */
(function(){
  const charts = {};
  function destroyAll(){
    Object.values(charts).forEach(c => { try { c.destroy(); } catch(e){} });
  }
  
  function barOpts(showLegend = false, horizontal = false) {
    const mainAxis   = horizontal ? 'x' : 'y';     // sumbu nilai
    const alignLabel = horizontal ? 'right' : 'top';

    const o = {
      responsive: true,
      maintainAspectRatio: false,

      // ruang ekstra di tepi kanvas agar label tidak kepotong
      layout: { padding: { top: 24, right: 12, bottom: 8, left: 12 } },

      plugins: {
        legend: { display: showLegend },
        datalabels: {
          display: true,
          color: 'black',
          font: { weight: 'bold', size: 12 },
          anchor: 'end',
          align: alignLabel,
          offset: 4,     // dorong sedikit dari ujung bar
          clip: false,   // jangan di-clip saat melewati area chart
          formatter: v => (v > 0 ? v : '')
        }
      },

      scales: { x: {}, y: {} }
    };

    // tambah headroom 15–20% di sumbu nilai (Chart.js v3+)
    o.scales[mainAxis] = {
      beginAtZero: true,
      grace: '20%',      // ↑ atur sesuai kebutuhan (mis. '20%')
      ticks: { padding: 6 }
    };

    if (horizontal) o.indexAxis = 'y';
    return o;
  }


  function pieOpts(){
    return { responsive:true, maintainAspectRatio:false, plugins:{ legend:{position:'bottom'}, datalabels:{
      display:true, color:'white', font:{weight:'bold', size:12},
      formatter:(v,ctx)=>{
        const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
        const pct = total? (v*100/total).toFixed(1):0;
        return `${v}\n(${pct}%)`;
      }
    }}};
  }
  
  window.renderAllCharts = function(data){
    destroyAll();
    Chart.register(ChartDataLabels);
    const jenis = document.getElementById('jenisChart');
    if(jenis){
      charts.jenis = new Chart(jenis, { type:'pie',
        data:{ labels:['Tenaga Dosen','Tenaga Kependidikan'], datasets:[{ data:[data.jenisData?.dosen||0, data.jenisData?.tendik||0], backgroundColor:['#006effff','#01317aff'], borderWidth:3, borderColor:'#fff' }]},
        options: pieOpts()
      });
    }
    const gender = document.getElementById('genderChart');
    if(gender){
      charts.gender = new Chart(gender, { type:'pie',
        data:{ labels:['Laki-laki','Perempuan'], datasets:[{ data:[data.genderData?.L||0, data.genderData?.P||0], backgroundColor:['#025bffff','#02377eff'], borderWidth:3, borderColor:'#fff' }]},
        options: pieOpts()
      });
    }
    const dGen = document.getElementById('dosenGenderChart');
    if(dGen){
      charts.dGen = new Chart(dGen, { type:'doughnut',
        data:{ labels:['Laki-laki','Perempuan'], datasets:[{ data:[data.dosenGender?.L||0, data.dosenGender?.P||0], backgroundColor:['#312e81','#6366f1'] }]},
        options: pieOpts()
      });
    }
    const dGol = document.getElementById('dosenGolonganChart');
    if(dGol){
      charts.dGol = new Chart(dGol, { type:'bar',
        data:{ labels:Object.keys(data.dosenGolongan||{}), datasets:[{ data:Object.values(data.dosenGolongan||{}), backgroundColor:['#4338ca','#6366f1','#818cf8','#a5b4fc','#c7d2fe'],
        borderWidth: 1,
        borderRadius: { topLeft: 6, topRight: 6 },
        borderSkipped: false
        }]},
        options: barOpts(false,false)
      });
    }
    const dPend = document.getElementById('dosenPendidikanChart');
    if (dPend) {
      const labels = Object.keys(data.dosenPendidikan || {});
      const values = Object.values(data.dosenPendidikan || {});
      const total  = values.reduce((a,b) => a + (+b || 0), 0);

      // threshold irisan kecil → label dipindah ke bawah
      const THRESH = 0.06; // 6% (silakan ubah 0.03–0.08 sesuai kebutuhan)

      // siapkan elemen footnote di bawah canvas (sekali saja)
      let foot = document.getElementById('dPendFoot');
      if (!foot) {
        foot = document.createElement('div');
        foot.id = 'dPendFoot';
        foot.className = 'mb-3 w-full text-sm text-gray-700 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-center';
        dPend.parentElement.appendChild(foot);
      }

      // render daftar irisan kecil di bawah
      function renderFootnote() {
        const small = labels.map((label, i) => {
          const v = +values[i] || 0;
          const pct = total ? (v * 100 / total) : 0;
          return { label, v, pct, i };
        }).filter(r => r.pct > 0 && r.pct < THRESH * 100);

        if (small.length === 0) { foot.innerHTML = ''; return; }

        const colors = ['#1e3a8a','#3b82f6','#60a5fa','#a3c9ff','#93c5fd','#6366f1'];
        const chip = s => `
          <span class="inline-flex items-center gap-2 mr-3 mb-2">
            <span style="display:inline-block;width:10px;height:10px;background:${colors[s.i % colors.length]};border-radius:2px"></span>
            <span>${s.label}: <strong>${s.v}</strong> (${s.pct.toFixed(1)}%)</span>
          </span>`;
        foot.innerHTML = `<div class="flex flex-wrap">${small.map(chip).join('')}</div>`;
      }

      renderFootnote();

      charts.dPend = new Chart(dPend, {
        type: 'doughnut',
        data: {
          labels,
          datasets: [{
            data: values,
            backgroundColor: ['#1e3a8a','#3b82f6','#60a5fa','#a3c9ff','#93c5fd','#6366f1'],
            borderColor: '#ffffff',
            borderWidth: 3,
            radius: '100%' // kecilkan sedikit agar ada ruang
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          layout: { padding: { top: 16, right: 16, bottom: 12, left: 16 } },
          plugins: {
            legend: { position: 'bottom' },
            // tampilkan label HANYA untuk irisan >= THRESH, sisanya disembunyikan (dipindah ke bawah)
            datalabels: {
              display: (ctx) => {
                const v = +ctx.dataset.data[ctx.dataIndex] || 0;
                return total ? (v / total) >= THRESH : false;
              },
              formatter: (v) => {
                const p = total ? (v * 100 / total) : 0;
                return `${v}\n(${p.toFixed(1)}%)`;
              },
              color: '#ffffff',
              font: { weight: 'bold', size: 12 },
              anchor: 'center',
              align: 'center',
              offset: 6,
              clip: false
            }
          }
        }
      });
    }

    const dJab = document.getElementById('dosenJabatanChart');
    if(dJab){
      charts.dJab = new Chart(dJab, { type:'doughnut',
        data:{ labels:Object.keys(data.dosenJabatan||{}), datasets:[{ data:Object.values(data.dosenJabatan||{}), backgroundColor:['#cf5905ff','#fcab69ff','#8a4801ff','#ff8800ff','#4a2700ff'] }]},
        options: pieOpts()
      });
    }

    const djg = document.getElementById('dosenJabatanGenderChart');
    if (djg) {
      const map = data.dosenJabatanGender || {};
      const labels = Object.keys(map);

      // kalau kosong → kasih pesan dan stop
      if (!labels.length) {
        const wrap = document.getElementById('dosenJabatanGenderWrap') || djg.parentElement;
        if (wrap) {
          wrap.innerHTML = '<div class="p-6 text-sm text-gray-500">Tidak ada data dosen per jabatan × jenis kelamin untuk tanggal ini.</div>';
        }
        return;
      }

      // urutkan berdasarkan total desc
      labels.sort((a,b) => {
        const ta = (map[a]?.L||0) + (map[a]?.P||0);
        const tb = (map[b]?.L||0) + (map[b]?.P||0);
        return tb - ta;
      });

      const male   = labels.map(j => (map[j]?.L)||0);
      const female = labels.map(j => (map[j]?.P)||0);

      // tinggi kanvas menyesuaikan jumlah bar
      const perBar = 26;
      const h = Math.max(420, labels.length * perBar + 40);
      const wrap = document.getElementById('dosenJabatanGenderWrap') || djg.parentElement;
      if (wrap) { wrap.style.maxHeight = '520px'; wrap.style.overflowY = 'auto'; }
      djg.style.height = h + 'px';
      djg.height = h;

      const opts = barOpts(true, true); // legend ON, horizontal
      opts.scales.x = Object.assign({}, opts.scales.x, { beginAtZero:true, grace:'18%', ticks:{ padding:6 } });
      opts.scales.y = Object.assign({}, opts.scales.y, { ticks:{ autoSkip:false, padding:4, font:{ size:11 } }, grid:{ offset:true } });
      opts.plugins.datalabels = Object.assign({}, opts.plugins.datalabels, { align:'right', anchor:'end', offset:6, formatter:v=>v>0?v:'' });

      charts.djg = new Chart(djg, {
        type:'bar',
        data:{
          labels,
          datasets:[
            { label:'Laki-laki', data: male,   backgroundColor:'#312e81' },
            { label:'Perempuan', data: female, backgroundColor:'#6366f1' }
          ]
        },
        options: opts
      });
    }

    // === Dosen: Jabatan per Fakultas (stacked, horizontal) ===
    const djf = document.getElementById('dosenJabatanFakultasChart');
    if (djf) {
      const map = data.dosenJabatanFakultas || {};
      const labels = Object.keys(map);

      if (!labels.length) {
        const wrap = document.getElementById('dosenJabFakWrap') || djf.parentElement;
        if (wrap) wrap.innerHTML = '<div class="p-6 text-sm text-gray-500">Tidak ada data dosen per jabatan per fakultas untuk tanggal ini.</div>';
        return;
      }

      // urutan jabatan yang diutamakan
      const prefer = ['Pengajar','Asisten Ahli','Lektor','Lektor Kepala','Guru Besar'];
      const setJab = new Set();
      labels.forEach(f => Object.keys(map[f] || {}).forEach(j => setJab.add(j)));
      const jabs = [
        ...prefer.filter(j => setJab.has(j)),
        ...Array.from(setJab).filter(j => !prefer.includes(j)).sort()
      ];

      // dataset per jabatan
      const palette = ['#bfdbfe','#93c5fd','#60a5fa','#3b82f6','#1d4ed8','#1e40af','#3730a3','#312e81'];
      const datasets = jabs.map((jab, idx) => ({
        label: jab,
        data: labels.map(f => (map[f]?.[jab] || 0)),
        backgroundColor: palette[idx % palette.length],
        borderRadius: 4
      }));

      // tinggi kanvas menyesuaikan jumlah bar (fakultas)
      const perBar = 26;
      const h = Math.max(420, labels.length * perBar + 40);
      const wrap = document.getElementById('dosenJabFakWrap') || djf.parentElement;
      if (wrap) { wrap.style.maxHeight = '520px'; wrap.style.overflowY = 'auto'; }
      djf.style.height = h + 'px';
      djf.height = h;

      // opsi stacked + headroom
      const opts = barOpts(true, true); // legend ON, horizontal
      opts.scales = opts.scales || {};
      opts.scales.x = { beginAtZero: true, stacked: true, grace: '18%', ticks: { padding: 6 } };
      opts.scales.y = { stacked: true, ticks: { autoSkip: false, padding: 4, font: { size: 11 } }, grid: { offset: true } };

      // tampilkan 1 angka total di ujung bar (dataset terakhir saja)
      opts.plugins = opts.plugins || {};
      opts.plugins.datalabels = {
        display: (ctx) => ctx.datasetIndex === datasets.length - 1,
        formatter: (v, ctx) => {
          const i = ctx.dataIndex;
          const sum = datasets.reduce((a, ds) => a + (+ds.data[i] || 0), 0);
          return sum > 0 ? sum : '';
        },
        color: 'black',
        font: { weight: 'bold', size: 12 },
        anchor: 'end',
        align: 'right',
        offset: 6,
        clip: false
      };

      charts.djf = new Chart(djf, {
        type: 'bar',
        data: { labels, datasets },
        options: opts
      });
    }

    const dStat = document.getElementById('dosenStatusChart');
    if(dStat){
      charts.dStat = new Chart(dStat, { type:'bar',
        data:{ labels:Object.keys(data.dosenStatus||{}), datasets:[{ data:Object.values(data.dosenStatus||{}), backgroundColor:['#6366f1','#8b5cf6','#a78bfa'],
        borderWidth: 1,
        borderRadius: { topLeft: 6, topRight: 6 },
        borderSkipped: false
        }]},
        options: barOpts(false,false)
      });
    }

    const gb = document.getElementById('guruBesarFakultasChart');
    if (gb) {
      // ambil opsi dasar
      const opts = barOpts(false, false);

      // tambahkan ruang di atas nilai maksimum
      opts.scales = opts.scales || {};
      opts.scales.y = Object.assign({}, opts.scales.y, {
        beginAtZero: true,
        grace: '15%',          // ← ruang ekstra 15% di atas max (Chart.js v3+)
        ticks: { padding: 6 }  // sedikit jarak antara grid & angka
      });

      // supaya label datalabels tidak “nempel” ke tepian atas
      opts.plugins = opts.plugins || {};
      opts.plugins.datalabels = Object.assign({}, opts.plugins.datalabels, {
        offset: 4,   // dorong 4px dari ujung bar
        clip: false  // jangan di-clip kalau melewati area chart
      });

      // beri padding top global canvas (tambahan kecil)
      opts.layout = Object.assign({}, opts.layout, { padding: { top: 20 } });

      charts.gb = new Chart(gb, {
        type: 'bar',
        data: {
          labels: Object.keys(data.guruBesarFakultas || {}),
          datasets: [{
            data: Object.values(data.guruBesarFakultas || {}),
            backgroundColor: ['#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5','#ea580c','#c2410c','#9a3412'],
            borderWidth: 1,
            borderRadius: { topLeft: 6, topRight: 6 },
            borderSkipped: false
          }]
        },
        options: opts
      });
    }

    const s3 = document.getElementById('dosenS3FakultasChart');
    if(s3){
      charts.s3 = new Chart(s3, { type:'bar',
        data:{ labels:Object.keys(data.dosenS3Fakultas||{}), datasets:[{ data:Object.values(data.dosenS3Fakultas||{}), backgroundColor:['#10b981','#34d399','#6ee7b7','#a7f3d0','#d1fae5','#059669','#047857','#065f46'],
        borderWidth: 1,
        borderRadius: { topLeft: 6, topRight: 6 },
        borderSkipped: false
        }]},
        options: barOpts(false,false)
      });
    }

    const tot = document.getElementById('totalDosenFakultasChart');
    if (tot) {
      const rows = Object.entries(data.fakultasData || {}).map(([label, v]) => ({
        label,
        value: Number(v?.dosen ?? 0)
      })).filter(r => r.value > 0); // ← buang yang 0

      if (rows.length === 0) return;

      const labels = rows.map(r => r.label);
      const dosen  = rows.map(r => r.value);
      const palette = ['#1e3a8a','#3b82f6','#60a5fa','#93c5fd','#dbeafe','#1e40af','#2563eb','#3730a3'];

      charts.tot = new Chart(tot, {
        type: 'bar',
        data: { labels, datasets: [{ data: dosen, backgroundColor: labels.map((_,i)=>palette[i%palette.length]),
        borderWidth: 1,
        borderRadius: { topLeft: 6, topRight: 6 },
        borderSkipped: false
         }] },
        options: barOpts(false, false)
      });
    }

    // === Dosen: Pendidikan per Fakultas (Stacked) ===
    {
      const el = document.getElementById('dosenPendidikanFakultasChart');
      if (el) {
        const map = data.dosenPendidikanFakultas || {};
        const labels = Object.keys(map);
        if (!labels.length) {
          (document.getElementById('dosenPendFakWrap') || el.parentElement)
            .innerHTML = '<div class="p-6 text-sm text-gray-500">Tidak ada data pendidikan dosen per fakultas untuk tanggal ini.</div>';
          // jangan return; biar chart lain tetap jalan
        } else {
          // urutkan kategori pendidikan prioritas
          const prefer = ['S3','S2','S1','D4/D3','SMA/SMK','Lainnya'];
          const catSet = new Set();
          labels.forEach(f => Object.keys(map[f]||{}).forEach(k => catSet.add(k)));
          const cats = [
            ...prefer.filter(c => catSet.has(c)),
            ...Array.from(catSet).filter(c => !prefer.includes(c)).sort()
          ];

          const palette = ['#0ea5e9','#38bdf8','#7dd3fc','#bae6fd','#e0f2fe','#93c5fd','#60a5fa','#3b82f6'];
          const datasets = cats.map((c, i) => ({
            label: c,
            data: labels.map(f => (map[f]?.[c] || 0)),
            backgroundColor: palette[i % palette.length],
            borderRadius: 4
          }));

          // tinggi & scroll Y
          const perBar = 26;
          const h = Math.max(420, labels.length * perBar + 40);
          const wrap = document.getElementById('dosenPendFakWrap') || el.parentElement;
          if (wrap) { wrap.style.maxHeight = '520px'; wrap.style.overflowY = 'auto'; }
          el.style.height = h + 'px'; el.height = h;

          const opts = barOpts(true, true); // legend ON, horizontal
          opts.scales = {
            x: { beginAtZero:true, stacked:true, grace:'18%', ticks:{ padding:6 } },
            y: { stacked:true, ticks:{ autoSkip:false, padding:4, font:{ size:11 } }, grid:{ offset:true } }
          };
          // tampilkan total di ujung bar
          opts.plugins.datalabels = {
            display: (ctx) => ctx.datasetIndex === datasets.length - 1,
            formatter: (v, ctx) => {
              const i = ctx.dataIndex;
              const sum = datasets.reduce((a, ds) => a + (+ds.data[i] || 0), 0);
              return sum > 0 ? sum : '';
            },
            color:'black', font:{ weight:'bold', size:12 }, anchor:'end', align:'right', offset:6, clip:false
          };

          charts.dosenPendFak = new Chart(el, {
            type:'bar',
            data:{ labels, datasets },
            options: opts
          });
        }
      }
    }

    
    const tGen = document.getElementById('tendikGenderChart');
    if(tGen){
      charts.tGen = new Chart(tGen, { type:'doughnut',
        data:{ labels:['Laki-laki','Perempuan'], datasets:[{ data:[data.tendikGender?.L||0, data.tendikGender?.P||0], backgroundColor:['#60a5fa','#93c5fd'] }]},
        options: pieOpts()
      });
    }
    const tGol = document.getElementById('tendikGolonganChart');
    if(tGol){
      charts.tGol = new Chart(tGol, { type:'bar',
        data:{ labels:Object.keys(data.tendikGolongan||{}), datasets:[{ data:Object.values(data.tendikGolongan||{}), backgroundColor:['#004cfcff','#0040a7ff','#012961ff','#4e92f2ff','#3c5f90ff'],
        borderWidth: 1,
        borderRadius: { topLeft: 6, topRight: 6 },
        borderSkipped: false
        }]},
        options: barOpts(false,false)
      });
    }
    const tPend = document.getElementById('tendikPendidikanChart');
    if(tPend){
      charts.tPend = new Chart(tPend, { type:'bar',
        data:{ labels:Object.keys(data.tendikPendidikan||{}), datasets:[{ data:Object.values(data.tendikPendidikan||{}), backgroundColor:['#1e3a8a','#3b82f6','#60a5fa','#93c5fd'],
        borderWidth: 1,
        borderRadius: { topLeft: 6, topRight: 6 },
        borderSkipped: false
        }]},
        options: barOpts(false,false)
      });
    }

    const tJab = document.getElementById('tendikJabatanChart');
    if (tJab) {
      const labels = Object.keys(data.tendikJabatan || {});
      const values = Object.values(data.tendikJabatan || {});

      // ===== 1) siapkan wrapper scroll (pakai parent canvas yg sudah ada) =====
      const outer = tJab.parentElement;       // <div ...><canvas id="tendikJabatanChart"></canvas></div>
      outer.style.height = 'auto';            // hapus fixed height bawaan
      outer.style.maxHeight = '500px';        // tinggi tampilan (scroll akan muncul jika konten > ini)
      outer.style.overflowY = 'auto';
      outer.style.position = 'relative';

      // buat inner container yg menentukan tinggi kanvas (konten sebenarnya)
      const perBar = 28;                      // px per bar (atur 26–32 sesuai selera)
      const innerH = Math.max(220, labels.length * perBar + 40);
      const inner = document.createElement('div');
      inner.style.height = innerH + 'px';
      inner.style.position = 'relative';

      // pindahkan canvas ke inner
      outer.replaceChild(inner, tJab);
      inner.appendChild(tJab);

      // ===== 2) opsi chart horizontal + rapi =====
      const opts = barOpts(false, true);      // true = horizontal (indexAxis:'y')

      // sumbu nilai (horizontal -> X): beri headroom
      opts.scales = opts.scales || {};
      opts.scales.x = Object.assign({}, opts.scales.x, {
        beginAtZero: true,
        grace: '18%',
        ticks: { padding: 6 }
      });

      // sumbu kategori (Y): tampilkan semua label, rapikan grid
      opts.scales.y = Object.assign({}, opts.scales.y, {
        ticks: { autoSkip: false, padding: 4, font: { size: 11 } },
        grid: { offset: true }
      });

      // datalabels jangan ketumpuk/ter-clip
      opts.plugins = opts.plugins || {};
      opts.plugins.datalabels = Object.assign({}, opts.plugins.datalabels, {
        align: 'right', anchor: 'end', offset: 6, clamp: true, clip: false,
        formatter: v => (v > 0 ? v : '')
      });

      // ===== 3) render =====
      charts.tJab = new Chart(tJab, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            data: values,
            backgroundColor: ['#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5'],
            borderWidth: 1,
            borderRadius: { bottomRight: 6, topRight: 6 },
            // jarak antar bar:
            categoryPercentage: 0.8,
            barPercentage: 0.7
            // atau: barThickness: 14, maxBarThickness: 16
          }]
        },
        options: opts
      });
    }

    const tStat = document.getElementById('tendikStatusChart');
    if(tStat){
      charts.tStat = new Chart(tStat, { type:'bar',
        data:{ labels:Object.keys(data.tendikStatus||{}), datasets:[{ data:Object.values(data.tendikStatus||{}), backgroundColor:['#6366f1','#8b5cf6','#a78bfa'],
        borderWidth: 1,
        borderRadius: { topLeft: 6, topRight: 6 },
        borderSkipped: false
        }]},
        options: barOpts(false,false)
      });
    }

    // === Tendik: Jumlah per Fakultas ===
    {
      const el = document.getElementById('tendikFakultasCountChart');
      if (el) {
        const map = data.fakultasData || {};
        let labels = Object.keys(map);
        if (!labels.length) {
          (document.getElementById('tendikFakCountWrap') || el.parentElement)
            .innerHTML = '<div class="p-6 text-sm text-gray-500">Tidak ada data tendik per fakultas untuk tanggal ini.</div>';
        } else {
          // ambil nilai tendik; sembunyikan fakultas 0 opsional
          labels = labels.filter(f => (map[f]?.tendik || 0) > 0);
          const values = labels.map(f => map[f].tendik || 0);

          const perBar = 26;
          const h = Math.max(420, labels.length * perBar + 40);
          const wrap = document.getElementById('tendikFakCountWrap') || el.parentElement;
          if (wrap) { wrap.style.maxHeight = '520px'; wrap.style.overflowY = 'auto'; }
          el.style.height = h + 'px'; el.height = h;

          const opts = barOpts(false, true); // no legend, horizontal
          opts.scales = {
            x: { beginAtZero:true, grace:'18%', ticks:{ padding:6 } },
            y: { ticks:{ autoSkip:false, padding:4, font:{ size:11 } }, grid:{ offset:true } }
          };
          opts.plugins.datalabels = Object.assign({}, opts.plugins.datalabels, {
            align:'right', anchor:'end', offset:6, formatter: v => v>0 ? v : ''
          });

          charts.tendikFakCount = new Chart(el, {
            type:'bar',
            data:{
              labels,
              datasets:[{
                data: values,
                backgroundColor: '#10b981', // hijau
                borderRadius: 6
              }]
            },
            options: opts
          });
        }
      }
    }

    // === Tendik: Pendidikan per Fakultas (Stacked) ===
    {
      const el = document.getElementById('tendikPendidikanFakultasChart');
      if (el) {
        const map = data.tendikPendidikanFakultas || {};
        const labels = Object.keys(map);
        if (!labels.length) {
          (document.getElementById('tendikPendFakWrap') || el.parentElement)
            .innerHTML = '<div class="p-6 text-sm text-gray-500">Tidak ada data pendidikan tendik per fakultas untuk tanggal ini.</div>';
        } else {
          const prefer = ['S3','S2','S1','D4/D3','SMA/SMK','Lainnya'];
          const catSet = new Set();
          labels.forEach(f => Object.keys(map[f]||{}).forEach(k => catSet.add(k)));
          const cats = [
            ...prefer.filter(c => catSet.has(c)),
            ...Array.from(catSet).filter(c => !prefer.includes(c)).sort()
          ];

          const palette = ['#86efac','#4ade80','#22c55e','#16a34a','#15803d','#a7f3d0','#34d399','#10b981'];
          const datasets = cats.map((c, i) => ({
            label: c,
            data: labels.map(f => (map[f]?.[c] || 0)),
            backgroundColor: palette[i % palette.length],
            borderRadius: 4
          }));

          const perBar = 26;
          const h = Math.max(420, labels.length * perBar + 40);
          const wrap = document.getElementById('tendikPendFakWrap') || el.parentElement;
          if (wrap) { wrap.style.maxHeight = '520px'; wrap.style.overflowY = 'auto'; }
          el.style.height = h + 'px'; el.height = h;

          const opts = barOpts(true, true); // legend ON, horizontal
          opts.scales = {
            x: { beginAtZero:true, stacked:true, grace:'18%', ticks:{ padding:6 } },
            y: { stacked:true, ticks:{ autoSkip:false, padding:4, font:{ size:11 } }, grid:{ offset:true } }
          };
          opts.plugins.datalabels = {
            display: (ctx) => ctx.datasetIndex === datasets.length - 1,
            formatter: (v, ctx) => {
              const i = ctx.dataIndex;
              const sum = datasets.reduce((a, ds) => a + (+ds.data[i] || 0), 0);
              return sum > 0 ? sum : '';
            },
            color:'black', font:{ weight:'bold', size:12 }, anchor:'end', align:'right', offset:6, clip:false
          };

          charts.tendikPendFak = new Chart(el, {
            type:'bar',
            data:{ labels, datasets },
            options: opts
          });
        }
      }
    }



  const fak = document.getElementById('fakultasChart');
  if (fak) {
    // ambil data
    const labelsAll = Object.keys(data.fakultasData || {});
    const dosAll    = labelsAll.map(f => (data.fakultasData?.[f]?.dosen)  || 0);
    const tenAll    = labelsAll.map(f => (data.fakultasData?.[f]?.tendik) || 0);

    // sembunyikan kategori total 0 (opsional)
    const rows   = labelsAll.map((label, i) => ({ label, d: dosAll[i], t: tenAll[i] }))
                            .filter(r => (r.d + r.t) > 0);
    const labels = rows.map(r => r.label);
    const dosen  = rows.map(r => r.d);
    const tendik = rows.map(r => r.t);

    // === layout scroll X + bar tebal fix ===
    const BAR_PX  = 18;   // lebar batang per dataset (px)
    const SLOT_PX = 30;   // lebar slot per kategori (px) -> pengatur jarak antar kategori
    const MIN_W   = 900;  // min canvas width supaya enak di desktop
    const H_PX    = 420;  // tinggi canvas
    const canvasW = Math.max(MIN_W, labels.length * SLOT_PX);

    const wrap = fak.parentElement;
    wrap.style.overflowX = 'auto';
    wrap.style.overflowY = 'hidden';
    wrap.style.height    = H_PX + 'px';

    // set ukuran canvas (non-responsive agar scroll stabil)
    fak.style.width  = canvasW + 'px';
    fak.style.height = H_PX + 'px';
    fak.width  = canvasW;
    fak.height = H_PX;

    // destroy dulu kalau sudah ada
    if (charts.fak) charts.fak.destroy();

    charts.fak = new Chart(fak, {
      type: 'bar',
      data: {
        labels,
        datasets: [
          {
            label: 'Dosen',
            data: dosen,
            backgroundColor: '#1e3a8a',
            stack: 'pegawai',
            barThickness: BAR_PX,
            maxBarThickness: BAR_PX,
            borderRadius: 6,
            borderSkipped: false
          },
          {
            label: 'Tendik',
            data: tendik,
            backgroundColor: '#3b82f6',
            stack: 'pegawai',
            barThickness: BAR_PX,
            maxBarThickness: BAR_PX,
            borderRadius: { topLeft: 6, topRight: 6 },
            borderSkipped: false
          }
        ]
      },
      options: {
        // penting: non-responsive supaya lebar canvas di atas tidak diubah Chart.js
        responsive: false,
        maintainAspectRatio: false,
        scales: {
          x: {
            stacked: true,
            ticks: { autoSkip: false, maxRotation: 60, minRotation: 40, padding: 4 },
            grid: { drawOnChartArea: false }
          },
          y: {
            stacked: true,
            beginAtZero: true,
            grace: '18%',
            ticks: { padding: 6 }
          }
        },
        plugins: {
          legend: { position: 'top' },
          datalabels: {
            // hanya tampilkan total di dataset paling atas
            display: (ctx) => ctx.datasetIndex === ctx.chart.data.datasets.length - 1,
            formatter: (v, ctx) => {
              const i = ctx.dataIndex;
              const sum = ctx.chart.data.datasets.reduce((a, ds) => a + (+ds.data[i] || 0), 0);
              return sum > 0 ? sum : '';
            },
            color: 'black',
            font: { weight: 'bold', size: 12 },
            anchor: 'end',
            align: 'top',
            offset: 6,
            clip: false
          }
        }
      }
    });
  }
    // // setelah semua charts dibuat:
    ChartUI.attachAllChartToolbars(true);
  };
})();
