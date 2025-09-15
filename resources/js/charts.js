/* global Chart, ChartDataLabels */
(function(){
  const charts = {};
  function destroyAll(){
    Object.values(charts).forEach(c => { try { c.destroy(); } catch(e){} });
  }
  function barOpts(showLegend=false, horizontal=false){
    const o = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: showLegend },
        datalabels: {
          display: true,
          color: 'black',
          font: { weight: 'bold', size: 12 },
          anchor: 'end',
          align: horizontal ? 'right':'top',
          formatter: (v)=> v>0? v:''
        }
      },
      scales: { y: { beginAtZero: true } }
    };
    if(horizontal){ o.indexAxis='y'; }
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
        data:{ labels:['Tenaga Dosen','Tenaga Kependidikan'], datasets:[{ data:[data.jenisData?.dosen||0, data.jenisData?.tendik||0], backgroundColor:['#0f172a','#334155'], borderWidth:3, borderColor:'#fff' }]},
        options: pieOpts()
      });
    }
    const gender = document.getElementById('genderChart');
    if(gender){
      charts.gender = new Chart(gender, { type:'pie',
        data:{ labels:['Laki-laki','Perempuan'], datasets:[{ data:[data.genderData?.L||0, data.genderData?.P||0], backgroundColor:['#0d9488','#14b8a6'], borderWidth:3, borderColor:'#fff' }]},
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
        data:{ labels:Object.keys(data.dosenGolongan||{}), datasets:[{ data:Object.values(data.dosenGolongan||{}), backgroundColor:['#4338ca','#6366f1','#818cf8','#a5b4fc','#c7d2fe'] }]},
        options: barOpts(false,false)
      });
    }
    const dPend = document.getElementById('dosenPendidikanChart');
    if(dPend){
      charts.dPend = new Chart(dPend, { type:'pie',
        data:{ labels:Object.keys(data.dosenPendidikan||{}), datasets:[{ data:Object.values(data.dosenPendidikan||{}), backgroundColor:['#1e3a8a','#3b82f6','#60a5fa'] }]},
        options: pieOpts()
      });
    }
    const dJab = document.getElementById('dosenJabatanChart');
    if(dJab){
      charts.dJab = new Chart(dJab, { type:'doughnut',
        data:{ labels:Object.keys(data.dosenJabatan||{}), datasets:[{ data:Object.values(data.dosenJabatan||{}), backgroundColor:['#f97316','#fb923c','#fdba74','#fed7aa'] }]},
        options: pieOpts()
      });
    }
    const dStat = document.getElementById('dosenStatusChart');
    if(dStat){
      charts.dStat = new Chart(dStat, { type:'bar',
        data:{ labels:Object.keys(data.dosenStatus||{}), datasets:[{ data:Object.values(data.dosenStatus||{}), backgroundColor:['#6366f1','#8b5cf6','#a78bfa'] }]},
        options: barOpts(false,false)
      });
    }
    const gb = document.getElementById('guruBesarFakultasChart');
    if(gb){
      charts.gb = new Chart(gb, { type:'bar',
        data:{ labels:Object.keys(data.guruBesarFakultas||{}), datasets:[{ data:Object.values(data.guruBesarFakultas||{}), backgroundColor:['#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5','#ea580c','#c2410c','#9a3412'] }]},
        options: barOpts(false,false)
      });
    }
    const s3 = document.getElementById('dosenS3FakultasChart');
    if(s3){
      charts.s3 = new Chart(s3, { type:'bar',
        data:{ labels:Object.keys(data.dosenS3Fakultas||{}), datasets:[{ data:Object.values(data.dosenS3Fakultas||{}), backgroundColor:['#10b981','#34d399','#6ee7b7','#a7f3d0','#d1fae5','#059669','#047857','#065f46'] }]},
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
        data: { labels, datasets: [{ data: dosen, backgroundColor: labels.map((_,i)=>palette[i%palette.length]) }] },
        options: barOpts(false, false)
      });
    }


    // const tot = document.getElementById('totalDosenFakultasChart');
    // if(tot){
    //   // Filter label dan data dosen yang bernilai 0
    //   const rawLabels = Object.keys(data.fakultasData||{});
    //   const rawData = rawLabels.map(f=> (data.fakultasData?.[f]?.dosen)||0);
    //   const filtered = rawLabels
    //     .map((label, i) => ({ label, value: rawData[i] }))
    //     .filter(item => item.value > 0);
    //   const labels = filtered.map(item => item.label);
    //   const dosen = filtered.map(item => item.value);

    //   charts.tot = new Chart(tot, { type:'bar',
    //     data:{
    //       labels,
    //       datasets:[{
    //         data: dosen,
    //         backgroundColor:['#1e3a8a','#3b82f6','#60a5fa','#93c5fd','#dbeafe','#1e40af','#2563eb','#3730a3']
    //       }]
    //     },
    //     options: barOpts(false,false)
    //   });
    // }
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
        data:{ labels:Object.keys(data.tendikGolongan||{}), datasets:[{ data:Object.values(data.tendikGolongan||{}), backgroundColor:['#0f172a','#1e293b','#334155','#475569','#64748b'] }]},
        options: barOpts(false,false)
      });
    }
    const tPend = document.getElementById('tendikPendidikanChart');
    if(tPend){
      charts.tPend = new Chart(tPend, { type:'bar',
        data:{ labels:Object.keys(data.tendikPendidikan||{}), datasets:[{ data:Object.values(data.tendikPendidikan||{}), backgroundColor:['#1e3a8a','#3b82f6','#60a5fa','#93c5fd'] }]},
        options: barOpts(false,false)
      });
    }
    const tJab = document.getElementById('tendikJabatanChart');
    if(tJab){
      charts.tJab = new Chart(tJab, { type:'bar',
        data:{ labels:Object.keys(data.tendikJabatan||{}), datasets:[{ data:Object.values(data.tendikJabatan||{}), backgroundColor:['#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5'], barThickness:10, maxBarThickness:10 }]},
        options: barOpts(false,true)
      });
    }
    const tStat = document.getElementById('tendikStatusChart');
    if(tStat){
      charts.tStat = new Chart(tStat, { type:'bar',
        data:{ labels:Object.keys(data.tendikStatus||{}), datasets:[{ data:Object.values(data.tendikStatus||{}), backgroundColor:['#6366f1','#8b5cf6','#a78bfa'] }]},
        options: barOpts(false,false)
      });
    }
    const fak = document.getElementById('fakultasChart');
    if(fak){
      const labels = Object.keys(data.fakultasData||{});
      const dosen = labels.map(f=> (data.fakultasData?.[f]?.dosen)||0);
      const tendik = labels.map(f=> (data.fakultasData?.[f]?.tendik)||0);
      charts.fak = new Chart(fak, {
        type:'bar',
        data:{ labels, datasets:[
          { label:'Dosen', data:dosen, backgroundColor:'#1e3a8a' },
          { label:'Tendik', data:tendik, backgroundColor:'#3b82f6' }
        ]},
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{position:'top'}, datalabels:{ display:true, color:'black', font:{weight:'bold',size:12}, anchor:'end', align:'top', formatter:(v)=> v>0? v:'' } }, scales:{ y:{ beginAtZero:true } } }
      });
    }
  };
})();
