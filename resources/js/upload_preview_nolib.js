/* global XLSX */
(function(){
  const fileInput      = document.getElementById('fileInput');
  const previewSection = document.getElementById('previewSection');
  const previewTable   = document.getElementById('previewTable');
  const btnSave        = document.getElementById('btnSave');
  const btnCancel      = document.getElementById('btnCancel');
  const dropZone       = document.getElementById('dropZone');

  const hiddenStats    = document.getElementById('stats_json');
  const hiddenRows     = document.getElementById('row_count');
  const hiddenPreview  = document.getElementById('preview_json');

  // TAMPIL DI UI MAKS 100 DATA (plus header)
  const PREVIEW_LIMIT = 100;

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

    const idxGender       = headerIndex(headers, ['jns kel','jenis kel','gender','jns_kel']);
    const idxGolongan     = headerIndex(headers, ['golongan']);
    const idxJabatan      = headerIndex(headers, ['jabatan']);
    const idxPend         = headerIndex(headers, ['pendidikan']);
    const idxStatusKerja  = headerIndex(headers, ['status bekerja','status kerja','status keaktifan','keaktifan']);
    const idxStatusPeg    = headerIndex(headers, ['status kepegawaian','status pegawai']);
    const idxStatusAny    = headerIndex(headers, ['status']);
    const idxStatus       = idxStatusPeg !== -1 ? idxStatusPeg : idxStatusAny;
    const idxJenis        = headerIndex(headers, ['jenis peg','jenis pegawai']);
    const idxFak          = headerIndex(headers, ['fakultas','sekolah','unit es ii']);

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
      dosenJabatanGender: {},
      dosenJabatanFakultas: {},
      dosenPendidikanFakultas: {},
      tendikPendidikanFakultas: {},
    };

    function inc(obj, key){
      if(!key) return;
      obj[key] = (obj[key]||0)+1;
    }

    function isNonAktif(str){
      if(!str) return false;
      const s = String(str).toLowerCase().trim().replace(/[^a-z0-9]/g, '');
      return s === 'nonaktif' || s === 'tidakaktif';
    }

    for(let r=1;r<rows.length;r++){
      const row = rows[r]||[];
      const val = (i)=> i>=0 ? String(row[i]||'').trim() : '';

      // Abaikan pegawai jika berstatus Non Aktif
      const statusKerja = val(idxStatusKerja);
      const statusPeg   = val(idxStatusPeg);
      const statusAny   = val(idxStatusAny);

      if (isNonAktif(statusKerja) || (idxStatusKerja === -1 && isNonAktif(statusAny)) || isNonAktif(statusPeg)) {
        continue; // Pegawai Non Aktif tidak dihitung dalam perhitungan jumlah pegawai
      }

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

        if (jab) {
          agg.dosenJabatanGender[jab] = agg.dosenJabatanGender[jab] || { L:0, P:0 };
          if (gender === 'L' || gender === 'LAKI-LAKI') agg.dosenJabatanGender[jab].L++;
          else if (gender === 'P' || gender === 'PEREMPUAN') agg.dosenJabatanGender[jab].P++;
        }
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

  // === TAMPILKAN HANYA 100 BARIS DI UI ===
  function showPreview(excelData){
    if(!excelData || excelData.length===0){
      previewTable.innerHTML = '<div class="p-4 text-center text-red-500">Tidak ada data untuk ditampilkan</div>';
      previewSection.classList.remove('hidden');
      return;
    }
    const header     = excelData[0] || [];
    const totalRows  = Math.max(excelData.length - 1, 0);
    const shownRows  = Math.min(totalRows, PREVIEW_LIMIT);

    let html = '';
    if (header.length) {
      html += '<thead class="bg-gray-50"><tr>';
      header.forEach(h => html += `<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">${h||''}</th>`);
      html += '</tr></thead>';
    }
    html += '<tbody class="divide-y divide-gray-200">';
    for (let i=1; i<=shownRows; i++) {
      const row = excelData[i] || [];
      html += '<tr>';
      const maxCols = header.length || row.length;
      for (let j=0; j<maxCols; j++) {
        const cell = row[j] ?? '';
        html += `<td class="px-3 py-2 text-sm text-gray-900">${cell}</td>`;
      }
      html += '</tr>';
    }
    if (totalRows > PREVIEW_LIMIT) {
      html += `<tr><td colspan="${header.length||1}" class="px-3 py-2 text-sm text-gray-500 text-center italic">… dan ${totalRows - PREVIEW_LIMIT} baris lainnya</td></tr>`;
    }
    html += '</tbody>';

    previewTable.innerHTML = html;
    previewSection.classList.remove('hidden');
  }

  // === PARSE FILE & KIRIM PAYLOAD RINGAN ===
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
          data = text.split(/\r?\n/).map(line => line.split(','));
        } else {
          const wb = XLSX.read(new Uint8Array(e.target.result), { type:'array' });
          const ws = wb.Sheets[wb.SheetNames[0]];
          data = XLSX.utils.sheet_to_json(ws, { header:1 });
        }

        const stats        = computeStats(data);                    // dihitung dari SELURUH data (Non Aktif dilewati)
        const activeCount  = (stats.jenisData?.dosen || 0) + (stats.jenisData?.tendik || 0);
        const totalRows    = activeCount > 0 ? activeCount : Math.max(data.length - 1, 0);
        const previewLim   = Math.min(data.length, PREVIEW_LIMIT + 1); // header + 100 baris
        const previewRows  = data.slice(0, previewLim);

        hiddenStats.value   = JSON.stringify(stats);
        hiddenRows.value    = String(totalRows);                 // total pegawai aktif yang dihitung
        hiddenPreview.value = JSON.stringify(previewRows);       // header + 100 baris saja

        showPreview(data);                                       // UI tampil 100 baris
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
  dropZone?.addEventListener('drop', e => {
    e.preventDefault(); dropZone.classList.remove('border-blue-500');
    if(e.dataTransfer.files[0]){ fileInput.files = e.dataTransfer.files; handleFile(e.dataTransfer.files[0]); }
  });

  fileInput?.addEventListener('change', e => {
    const f=e.target.files[0]; if(f) handleFile(f);
  });

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