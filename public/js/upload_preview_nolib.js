/* global XLSX */
(function(){
  const fileInput = document.getElementById('fileInput');
  const previewSection = document.getElementById('previewSection');
  const previewTable = document.getElementById('previewTable');
  const btnSave = document.getElementById('btnSave');
  const btnCancel = document.getElementById('btnCancel');
  const dropZone = document.getElementById('dropZone');

  const hiddenStats = document.getElementById('stats_json');
  const hiddenRows = document.getElementById('row_count');
  const hiddenPreview = document.getElementById('preview_json');

  function headerIndex(headers, terms){
    headers = headers.map((v)=> String(v||'').trim().toLowerCase());
    for(const t of terms){
      const term = t.toLowerCase();
      const idx = headers.findIndex(h => h.includes(term));
      if(idx !== -1) return idx;
    }
    return -1;
  }

  function computeStats(data){
    const rows = data || [];
    if(rows.length < 2) return {};
    const headers = (rows[0]||[]).map(v => (v||'').toString());

    const idxGender   = headerIndex(headers, ['jns kel','jenis kel','gender','jns_kel']);
    const idxGolongan = headerIndex(headers, ['golongan']);
    const idxJabatan  = headerIndex(headers, ['jabatan']);
    const idxPend     = headerIndex(headers, ['pendidikan']);
    const idxStatus   = headerIndex(headers, ['status kepegawaian','status bekerja','status']);
    const idxJenis    = headerIndex(headers, ['jenis peg','jenis pegawai']);
    const idxFak      = headerIndex(headers, ['fakultas','sekolah','unit es ii']);

    const agg = {
      jenisData:        { dosen:0, tendik:0 },
      genderData:       { L:0, P:0 },
      dosenGender:      { L:0, P:0 },
      dosenGolongan:    {},
      dosenPendidikan:  {},
      dosenJabatan:     {},
      dosenStatus:      {},
      tendikGender:     { L:0, P:0 },
      tendikGolongan:   {},
      tendikPendidikan: {},
      tendikJabatan:    {},
      tendikStatus:     {},
      fakultasData:     {},
      guruBesarFakultas:{},
      dosenS3Fakultas:  {},
      dosenJabatanGender: {},   // ← NEW
      dosenJabatanFakultas: {}, // ← NEW
      dosenPendidikanFakultas: {},  // NEW
      tendikPendidikanFakultas: {}, // NEW
    };

    function inc(obj, key){
      if(!key) return;
      obj[key] = (obj[key]||0)+1;
    }

    for(let r=1;r<rows.length;r++){
      const row = rows[r]||[];
      const val = (i)=> i>=0 ? String(row[i]||'').trim() : '';

      const gender = val(idxGender).toUpperCase();
      const gol    = val(idxGolongan);
      const jab    = val(idxJabatan);
      const pend   = val(idxPend).toUpperCase();
      const status = val(idxStatus);
      const jenis  = val(idxJenis).toLowerCase();
      const fak    = val(idxFak);

      if(gender === 'L' || gender === 'LAKI-LAKI') agg.genderData.L++;
      else if(gender === 'P' || gender === 'PEREMPUAN') agg.genderData.P++;

      const jab_lc = jab.toLowerCase();
      const isDosen = jenis.includes('dosen') || jab_lc.includes('dosen') || jab_lc.includes('lektor') || jab_lc.includes('guru besar') || jab_lc.includes('asisten ahli');

      if(isDosen){
        agg.jenisData.dosen++;
        if(gender === 'L' || gender === 'LAKI-LAKI') agg.dosenGender.L++; else if(gender === 'P' || gender === 'PEREMPUAN') agg.dosenGender.P++;
        inc(agg.dosenGolongan, gol);
        inc(agg.dosenPendidikan, pend);
        inc(agg.dosenJabatan, jab);
        // cross-tab jabatan × gender (dosen saja)
        if (jab) {
          agg.dosenJabatanGender[jab] = agg.dosenJabatanGender[jab] || { L:0, P:0 };
          if (gender === 'L' || gender === 'LAKI-LAKI') agg.dosenJabatanGender[jab].L++;
          else if (gender === 'P' || gender === 'PEREMPUAN') agg.dosenJabatanGender[jab].P++;
        }

        // === NEW: per fakultas ===
        if (fak && jab) {
          agg.dosenJabatanFakultas[fak] = agg.dosenJabatanFakultas[fak] || {};
          agg.dosenJabatanFakultas[fak][jab] = (agg.dosenJabatanFakultas[fak][jab] || 0) + 1;
        }

        if (fak && pend) {
          agg.dosenPendidikanFakultas[fak] = agg.dosenPendidikanFakultas[fak] || {};
          agg.dosenPendidikanFakultas[fak][pend] = (agg.dosenPendidikanFakultas[fak][pend] || 0) + 1;
        }

        inc(agg.dosenStatus, status);
        if(fak){
          agg.fakultasData[fak] = agg.fakultasData[fak] || { dosen:0, tendik:0 };
          agg.fakultasData[fak].dosen++;
        }
        if(jab.toUpperCase().includes('GURU BESAR') && fak){
          agg.guruBesarFakultas[fak] = (agg.guruBesarFakultas[fak]||0)+1;
        }
        if(pend === 'S3' && fak){
          agg.dosenS3Fakultas[fak] = (agg.dosenS3Fakultas[fak]||0)+1;
        }
      } else {
        agg.jenisData.tendik++;
        if(gender === 'L' || gender === 'LAKI-LAKI') agg.tendikGender.L++; else if(gender === 'P' || gender === 'PEREMPUAN') agg.tendikGender.P++;
        inc(agg.tendikGolongan, gol);
        inc(agg.tendikPendidikan, pend);
        inc(agg.tendikJabatan, jab);
        inc(agg.tendikStatus, status);
        if(fak){
          agg.fakultasData[fak] = agg.fakultasData[fak] || { dosen:0, tendik:0 };
          agg.fakultasData[fak].tendik++;
        }
        if (fak && pend) {
          agg.tendikPendidikanFakultas[fak] = agg.tendikPendidikanFakultas[fak] || {};
          agg.tendikPendidikanFakultas[fak][pend] = (agg.tendikPendidikanFakultas[fak][pend] || 0) + 1;
        }

      }
    }
    return agg;
  }

//   function computeStats(data){
//   const rows = data || [];
//   if (rows.length < 2) return {};

//   const headers = (rows[0] || []).map(v => (v ?? '').toString());

//   // --- helper kecil ---
//   const nk = s => String(s || '').toLowerCase().replace(/[\s._/-]+/g, '');
//   const toTitle = s => String(s || '')
//     .toLowerCase()
//     .replace(/\s+/g, ' ')
//     .trim()
//     .replace(/\b\w/g, c => c.toUpperCase());

//   // normalisasi jabatan dosen agar tidak terpecah-pecah
//   function stdJabatanDosen(s) {
//     const t = String(s || '').toLowerCase().replace(/\./g, '').replace(/\s+/g, ' ').trim();
//     if (!t) return '';
//     if (/guru\s*besar|prof/.test(t))      return 'Guru Besar';
//     if (/lektor\s*kepala/.test(t))        return 'Lektor Kepala';
//     if (/\blektor\b/.test(t))             return 'Lektor';
//     if (/asisten\s*ahli/.test(t))         return 'Asisten Ahli';
//     // default: judulkan agar konsisten
//     return toTitle(s);
//   }

//   // robust index finder (fallback jika headerIndex kamu terlalu ketat)
//   function findIdx(candidates){
//     // coba pakai headerIndex milikmu kalau ada
//     if (typeof headerIndex === 'function') {
//       const i = headerIndex(headers, candidates);
//       if (i != null && i >= 0) return i;
//     }
//     // fallback: normalisasi dan cocokkan
//     const map = {};
//     headers.forEach((h, i) => map[nk(h)] = i);
//     for (const c of candidates) {
//       const key = nk(c);
//       if (map[key] != null) return map[key];
//     }
//     return -1;
//   }

//   // --- cari indeks kolom dengan sinonim yang lebih luas ---
//   const idxGender   = findIdx(['jns kel','jenis kel','gender','jns_kel','JNS KEL','jenis kelamin','jk']);
//   const idxGolongan = findIdx(['golongan','GOLONGAN']);
//   const idxPend     = findIdx(['pendidikan','PENDIDIKAN']);
//   const idxStatus   = findIdx(['status kepegawaian','status bekerja','status','STATUS KEPEGAWAIAN']);
//   const idxJenis    = findIdx(['jenis peg','jenis pegawai','JENIS PEG','jenis_peg','jenispegawai']);
//   const idxFak      = findIdx(['fakultas','sekolah','FAKULTAS/SEKOLAH','unit kerja','unit']);
//   const idxJabatan  = findIdx([
//     'jabatan fungsional','jab fungsional','jab. fungsional','jabfung','jab_fungsional',
//     'jabatan akademik','jabatan dosen','jabatan','jabatan fungsional dosen'
//   ]);

//   const agg = {
//     jenisData:        { dosen:0, tendik:0 },
//     genderData:       { L:0, P:0 },
//     dosenGender:      { L:0, P:0 },
//     dosenGolongan:    {},
//     dosenPendidikan:  {},
//     dosenJabatan:     {},
//     dosenStatus:      {},
//     tendikGender:     { L:0, P:0 },
//     tendikGolongan:   {},
//     tendikPendidikan: {},
//     tendikJabatan:    {},
//     tendikStatus:     {},
//     fakultasData:     {},
//     guruBesarFakultas:{},
//     dosenS3Fakultas:  {},
//     dosenJabatanGender: {},   // ← target chart baru
//   };

//   function inc(obj, key){
//     if(!key) return;
//     obj[key] = (obj[key] || 0) + 1;
//   }

//   for (let r = 1; r < rows.length; r++) {
//     const row = rows[r] || [];
//     const val = (i) => i >= 0 ? String(row[i] ?? '').trim() : '';

//     // normalisasi gender (L, LAKI-LAKI, Lk → L | P, PEREMPUAN, Pr → P)
//     const gRaw = val(idxGender);
//     const gU   = gRaw.toUpperCase();
//     const gender = /^L/.test(gU) ? 'L' : (/^P/.test(gU) ? 'P' : '');

//     const gol    = val(idxGolongan);
//     const jabRaw = val(idxJabatan);
//     const jab    = stdJabatanDosen(jabRaw); // ← pakai versi standar
//     const pend   = val(idxPend).toUpperCase();
//     const status = val(idxStatus);
//     const jenis  = val(idxJenis).toLowerCase();
//     const fak    = val(idxFak);

//     if (gender === 'L') agg.genderData.L++;
//     else if (gender === 'P') agg.genderData.P++;

//     // deteksi dosen yang lebih luwes
//     const jab_lc  = (jabRaw || '').toLowerCase();
//     const isDosen = /dosen/.test(jenis) ||
//                     /lektor|guru\s*besar|asisten\s*ahli/.test(jab_lc) ||
//                     /jabatan\s*akademik|jabatan\s*fungsional/.test(jab_lc);

//     if (isDosen) {
//       agg.jenisData.dosen++;
//       if (gender === 'L') agg.dosenGender.L++; else if (gender === 'P') agg.dosenGender.P++;

//       inc(agg.dosenGolongan, gol);
//       inc(agg.dosenPendidikan, pend);
//       inc(agg.dosenJabatan, jab);

//       // cross-tab: jabatan × gender
//       if (jab && gender) {
//         agg.dosenJabatanGender[jab] = agg.dosenJabatanGender[jab] || { L:0, P:0 };
//         agg.dosenJabatanGender[jab][gender] += 1;
//       }

//       inc(agg.dosenStatus, status);

//       if (fak) {
//         agg.fakultasData[fak] = agg.fakultasData[fak] || { dosen:0, tendik:0 };
//         agg.fakultasData[fak].dosen++;
//       }
//       if (jabRaw.toUpperCase().includes('GURU BESAR') && fak) {
//         agg.guruBesarFakultas[fak] = (agg.guruBesarFakultas[fak] || 0) + 1;
//       }
//       if (pend === 'S3' && fak) {
//         agg.dosenS3Fakultas[fak] = (agg.dosenS3Fakultas[fak] || 0) + 1;
//       }
//     } else {
//       agg.jenisData.tendik++;
//       if (gender === 'L') agg.tendikGender.L++; else if (gender === 'P') agg.tendikGender.P++;

//       inc(agg.tendikGolongan, gol);
//       inc(agg.tendikPendidikan, pend);
//       inc(agg.tendikJabatan, toTitle(jabRaw));
//       inc(agg.tendikStatus, status);

//       if (fak) {
//         agg.fakultasData[fak] = agg.fakultasData[fak] || { dosen:0, tendik:0 };
//         agg.fakultasData[fak].tendik++;
//       }
//     }
//   }

//   return agg;
// }


  // function showPreview(excelData){
  //   if(!excelData || excelData.length===0){
  //     previewTable.innerHTML = '<div class="p-4 text-center text-red-500">Tidak ada data untuk ditampilkan</div>';
  //     return;
  //   }
  //   let html = '';
  //   if(excelData[0]){
  //     html += '<thead class="bg-gray-50"><tr>';
  //     excelData[0].forEach(h=> html += `<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">${h||''}</th>`);
  //     html += '</tr></thead>';
  //   }
  //   html += '<tbody class="divide-y divide-gray-200">';
  //   const maxRows = excelData.length;           // ← tampilkan SEMUA baris
  //   // const maxRows = Math.min(excelData.length, 11);
  //   for(let i=1;i<maxRows;i++){
  //     const row = excelData[i]||[];
  //     html += '<tr>';
  //     const maxCols = excelData[0]? excelData[0].length : row.length;
  //     for(let j=0;j<maxCols;j++){
  //       const cell = row[j] ?? '';
  //       html += `<td class="px-3 py-2 text-sm text-gray-900">${cell}</td>`;
  //     }
  //     html += '</tr>';
  //   }
  //   if(excelData.length > 11){
  //     html += `<tr><td colspan="${excelData[0].length}" class="px-3 py-2 text-sm text-gray-500 text-center italic">... dan ${excelData.length-11} baris lainnya</td></tr>`;
  //   }
  //   html += '</tbody>';
  //   previewTable.innerHTML = html;
  //   previewSection.classList.remove('hidden');
  // }

  function showPreview(excelData){
    if(!excelData || excelData.length===0){
      previewTable.innerHTML = '<div class="p-4 text-center text-red-500">Tidak ada data untuk ditampilkan</div>';
      return;
    }
    let html = '';
    if (excelData[0]) {
      html += '<thead class="bg-gray-50"><tr>';
      excelData[0].forEach(h => html += `<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">${h||''}</th>`);
      html += '</tr></thead>';
    }
    html += '<tbody class="divide-y divide-gray-200">';
    const maxRows = excelData.length; // tampilkan SEMUA baris
    for (let i=1; i<maxRows; i++) {
      const row = excelData[i] || [];
      html += '<tr>';
      const maxCols = excelData[0] ? excelData[0].length : row.length;
      for (let j=0; j<maxCols; j++) {
        const cell = row[j] ?? '';
        html += `<td class="px-3 py-2 text-sm text-gray-900">${cell}</td>`;
      }
      html += '</tr>';
    }
    // HAPUS blok notice lama:
    // if (excelData.length > 11) { ... }
    html += '</tbody>';

    previewTable.innerHTML = html;
    previewSection.classList.remove('hidden');
  }


  function handleFile(file){
    const type = (file.name.split('.').pop() || '').toLowerCase();
    if(!['xlsx','xls','csv'].includes(type)){
      alert('Harap pilih file Excel (.xlsx / .xls / .csv)');
      return;
    }
    const reader = new FileReader();
    reader.onload = function(e){
      try{
        let data = [];
        if(type === 'csv'){
          const text = new TextDecoder().decode(e.target.result);
          const rows = text.split(/\r?\n/).map(line => line.split(','));
          data = rows;
        } else {
          const wb = XLSX.read(new Uint8Array(e.target.result), { type:'array' });
          const ws = wb.Sheets[wb.SheetNames[0]];
          data = XLSX.utils.sheet_to_json(ws, { header:1 });
        }
        const stats = computeStats(data);
        // const previewRows = data.slice(0, 101);
        const previewRows = data;

        hiddenStats.value = JSON.stringify(stats);
        hiddenRows.value = String(Math.max(data.length - 1, 0));
        hiddenPreview.value = JSON.stringify(previewRows);

        showPreview(data);
        window.__preview_ok = true;
      }catch(err){
        alert('Error membaca file: ' + err.message);
        previewSection.classList.add('hidden');
      }
    };
    reader.readAsArrayBuffer(file);
  }

  dropZone?.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-blue-500'); });
  dropZone?.addEventListener('dragleave', e => { dropZone.classList.remove('border-blue-500'); });
  dropZone?.addEventListener('drop', e => { e.preventDefault(); dropZone.classList.remove('border-blue-500'); if(e.dataTransfer.files[0]){ fileInput.files = e.dataTransfer.files; handleFile(e.dataTransfer.files[0]); } });

  fileInput?.addEventListener('change', e => { const f=e.target.files[0]; if(f) handleFile(f); });

  btnCancel?.addEventListener('click', ()=>{
    if(!fileInput) return;
    fileInput.value = '';
    previewSection?.classList.add('hidden');
    if(previewTable) previewTable.innerHTML = '';
    window.__preview_ok = false;
    if(hiddenStats) hiddenStats.value = '';
    if(hiddenRows) hiddenRows.value = '';
    if(hiddenPreview) hiddenPreview.value = '';
  });

  btnSave?.addEventListener('click', ()=>{
    if(!fileInput || !fileInput.files[0]){
      alert('Tidak ada file untuk disimpan.');
      return;
    }
    if(!window.__preview_ok){
      if(!confirm('Anda belum meninjau preview. Lanjut simpan?')) return;
    }
    document.getElementById('upload_form').submit();
  });
})();