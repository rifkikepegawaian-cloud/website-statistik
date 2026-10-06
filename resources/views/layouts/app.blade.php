{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@hasSection('title')@yield('title') - @endif Dashboard SDM UNDIP</title>

  <!-- Primary Meta Tags -->
  <meta name="title" content="@hasSection('title')@yield('title') - @endif Dashboard SDM UNDIP">
  <meta name="description" content="@yield('meta_description', 'Portal resmi visualisasi data dan statistik kepegawaian (Dosen dan Tenaga Kependidikan) Direktorat Sumber Daya Manusia Universitas Diponegoro.')">
  <meta name="keywords" content="Statistik Pegawai, SDM UNDIP, Kepegawaian UNDIP, Universitas Diponegoro, Dosen UNDIP, Tendik UNDIP, Dashboard SDM UNDIP">
  <meta name="author" content="Direktorat Sumber Daya Manusia Universitas Diponegoro">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="{{ url()->current() }}">

  <!-- Google Site Name & Branding Identification -->
  <meta name="application-name" content="Dashboard SDM UNDIP">
  <meta name="apple-mobile-web-app-title" content="Dashboard SDM UNDIP">

  <!-- Open Graph / Facebook / WhatsApp -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Dashboard SDM UNDIP">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:title" content="@hasSection('title')@yield('title') - @endif Dashboard SDM UNDIP">
  <meta property="og:description" content="@yield('meta_description', 'Portal resmi visualisasi data dan statistik kepegawaian (Dosen dan Tenaga Kependidikan) Direktorat Sumber Daya Manusia Universitas Diponegoro.')">
  <meta property="og:image" content="{{ asset('images/logo-undip.png') }}">
  <meta property="og:image:alt" content="Logo Universitas Diponegoro">
  <meta property="og:locale" content="id_ID">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary">
  <meta name="twitter:title" content="@hasSection('title')@yield('title') - @endif Dashboard SDM UNDIP">
  <meta name="twitter:description" content="@yield('meta_description', 'Portal resmi visualisasi data dan statistik kepegawaian (Dosen dan Tenaga Kependidikan) Direktorat Sumber Daya Manusia Universitas Diponegoro.')">
  <meta name="twitter:image" content="{{ asset('images/logo-undip.png') }}">

  <!-- Favicons (Google Search & Mobile compliant) -->
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ @filemtime(public_path('favicon.ico')) }}">
  <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}?v={{ @filemtime(public_path('favicon-48x48.png')) }}">
  <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}?v={{ @filemtime(public_path('favicon-96x96.png')) }}">
  <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192x192.png') }}?v={{ @filemtime(public_path('favicon-192x192.png')) }}">
  <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('apple-touch-icon.png') }}?v={{ @filemtime(public_path('apple-touch-icon.png')) }}">

  <!-- Structured Data: Google Search Site Name & EducationalOrganization -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "WebSite",
        "@id": "{{ url('/') }}/#website",
        "url": "{{ url('/') }}",
        "name": "Dashboard SDM UNDIP",
        "alternateName": [
          "Statistik SDM UNDIP",
          "SDM UNDIP",
          "Statistik Kepegawaian UNDIP",
          "Portal Statistik UNDIP"
        ],
        "description": "Portal visualisasi data dan statistik kepegawaian Dosen dan Tenaga Kependidikan Universitas Diponegoro",
        "inLanguage": "id-ID"
      },
      {
        "@type": "EducationalOrganization",
        "@id": "https://www.undip.ac.id/#organization",
        "name": "Universitas Diponegoro",
        "alternateName": "UNDIP",
        "url": "https://www.undip.ac.id",
        "logo": {
          "@type": "ImageObject",
          "url": "{{ asset('images/logo-undip.png') }}"
        },
        "department": {
          "@type": "Organization",
          "name": "Direktorat Sumber Daya Manusia Universitas Diponegoro",
          "alternateName": "Direktorat SDM UNDIP",
          "url": "{{ url('/') }}"
        }
      }
    ]
  }
  </script>

  <!-- Tailwind CSS (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Chart.js + Datalabels (CDN) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

  <!-- SheetJS (CDN) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

  <link rel="stylesheet" href="{{ asset('css/custom.css') }}"/>

  <style>
    html, body { width: 100%; overflow-x: hidden; } /* cegah overflow horizontal tak sengaja */

    /* Background gradien utama */
    body {
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 25%, #60a5fa 50%, #93c5fd 75%, #dbeafe 100%);
    }

    /* Container pembatas konten utama */
    .content-wrap{
      max-width: 80rem;          /* ~max-w-7xl */
      margin-inline: auto;       /* center */
      padding-inline: 1rem;      /* px-4 */
    }
    @media (min-width: 640px){ .content-wrap{ padding-inline: 1.5rem; } }  /* sm:px-6 */
    @media (min-width: 1024px){ .content-wrap{ padding-inline: 2rem; } }   /* lg:px-8 */

    /* Section header dekoratif (opsional) */
    .section-header {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.8) 100%);
      border-left: 4px solid transparent;
      border-image: linear-gradient(135deg, #3b82f6, #8b5cf6) 1;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    /* Tombol navbar (kompatibel CDN, tanpa @apply) */
    .nav-btn{
      padding: 0.5rem 0.75rem;
      border-radius: 0.375rem;
      font-size: .875rem;
      font-weight: 600;
      transition: background-color .2s, color .2s, box-shadow .2s;
      display: inline-flex; align-items: center; gap: .375rem;
    }
    .nav-btn-active{ background:#1e3a8a; color:#fff; box-shadow:0 2px 6px rgba(0,0,0,.2); }
    .nav-btn-inactive{ color:rgba(255,255,255,.9); }
    .nav-btn-inactive:hover{ background:rgba(29,78,216,.7); color:#fff; }

    /* Canvas chart responsif di dalam card */
    .chart-card canvas{
      width: 100% !important;
      height: 360px !important;     /* ubah sesuai kebutuhan */
      display: block;
    }
    /* Wrapper scroll-X untuk chart kategori panjang (pakai di view yg perlu) */
    .chart-xscroll{ overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .chart-xscroll-inner{ display:inline-block; min-width:100%; }
  </style>

  @stack('head')
</head>

<body class="bg-gray-50">
  @php
    $isHome  = request()->routeIs('home');
    $isAdmin = request()->routeIs('admin.*');
    $isLogin = request()->routeIs('login');
  @endphp

  {{-- NAVBAR (fixed) --}}
  <nav class="gradient-bg text-white shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="content-wrap">
      <div class="flex justify-between items-center h-16">
        {{-- Brand (logo + title) --}}
        <div class="flex items-center gap-3">
          <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/logo-undip.png') }}" alt="UNDIP" class="h-8 w-auto">
            <h1 class="text-lg sm:text-xl font-bold">Dashboard SDM UNDIP</h1>
          </a>
        </div>

        {{-- Desktop Menu --}}
        <div class="hidden md:flex items-center gap-3 lg:gap-4">
          <a href="{{ route('home') }}" class="nav-btn {{ $isHome ? 'nav-btn-active' : 'nav-btn-inactive' }}">Beranda</a>

          @auth
            <a href="{{ route('admin.dashboard') }}" class="nav-btn {{ $isAdmin ? 'nav-btn-active' : 'nav-btn-inactive' }}">Admin</a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
              @csrf
              <button type="submit" class="nav-btn nav-btn-inactive bg-white/10 hover:bg-white/20">Logout</button>
            </form>
          @endauth

          @guest
            <a href="{{ route('login') }}" class="nav-btn {{ $isLogin ? 'nav-btn-active' : 'nav-btn-inactive' }}">Login</a>
          @endguest
        </div>

        {{-- Mobile: Hamburger --}}
        <button id="navToggle"
                class="md:hidden p-2 rounded hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/40"
                aria-label="Toggle menu" aria-expanded="false" aria-controls="mobileMenu">
          <svg id="iconBurger" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg id="iconClose" class="h-6 w-6 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    {{-- Mobile Menu Panel (penuh lebar, tapi isinya tetap dibatasi content-wrap) --}}
    <div id="mobileMenu" class="md:hidden hidden fixed top-16 left-0 right-0 bg-blue-800/95 backdrop-blur-sm border-t border-white/10 shadow-lg">
      <div class="content-wrap py-3 space-y-2">
        <a href="{{ route('home') }}" class="block nav-btn w-full {{ $isHome ? 'nav-btn-active' : 'nav-btn-inactive' }}">Beranda</a>

        @auth
          <a href="{{ route('admin.dashboard') }}" class="block nav-btn w-full {{ $isAdmin ? 'nav-btn-active' : 'nav-btn-inactive' }}">Admin</a>
          <form action="{{ route('logout') }}" method="POST" class="block">
            @csrf
            <button type="submit" class="nav-btn nav-btn-inactive w-full bg-white/10 hover:bg-white/20">Logout</button>
          </form>
        @endauth

        @guest
          <a href="{{ route('login') }}" class="block nav-btn w-full {{ $isLogin ? 'nav-btn-active' : 'nav-btn-inactive' }}">Login</a>
        @endguest
      </div>
    </div>
  </nav>

  {{-- WRAPPER agar footer nempel bawah: min-h-screen + flex-col --}}
  <div class="min-h-screen flex flex-col">
    {{-- spacer untuk mengimbangi navbar fixed (±64px) --}}
    <div class="h-16"></div>

    {{-- Modal Data Chart (global) --}}
    <div id="chartDataModal" class="fixed inset-0 z-50 hidden">
      <div class="absolute inset-0 bg-black/40" onclick="ChartUI.closeModal()"></div>
      <div class="relative mx-auto my-10 w-[95%] max-w-4xl bg-white rounded-lg shadow-xl p-5">
        <div class="flex items-start justify-between mb-3">
          <h4 id="cdmTitle" class="text-lg font-semibold">Data Chart</h4>
          <button onclick="ChartUI.closeModal()" class="text-xl text-gray-400 hover:text-gray-700">&times;</button>
        </div>

        <div class="flex items-center justify-between mb-3">
          <div id="cdmSubtitle" class="text-sm text-gray-500"></div>
          <button id="cdmDownloadExcel" class="px-3 py-1 rounded bg-emerald-600 text-white text-sm hover:bg-emerald-700">
            Unduh Data (Excel)
          </button>
        </div>

        <div class="border rounded overflow-auto max-h-[60vh]">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead id="cdmThead" class="bg-gray-50"></thead>
            <tbody id="cdmTbody" class="divide-y divide-gray-100"></tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- MAIN sebagai flex-1 agar mendorong footer ke bawah; konten dibatasi content-wrap --}}
    <main class="flex-1">
      <div class="content-wrap pt-4 pb-10">
        @yield('content')
      </div>
    </main>

    {{-- FOOTER --}}
    <footer class="py-6 text-center text-blue-900">
      &copy; {{ now()->year }} Direktorat Sumber Daya Manusia Universitas Diponegoro
    </footer>
  </div>

  <!-- App scripts -->
  <script src="{{ asset('js/charts.js') }}?v={{ filemtime(public_path('js/charts.js')) }}"></script>
  <script src="{{ asset('js/upload_preview_nolib.js') }}?v={{ filemtime(public_path('js/upload_preview_nolib.js')) }}"></script>

  {{-- Mobile menu toggler --}}
  <script>
    (function(){
      const btn   = document.getElementById('navToggle');
      const menu  = document.getElementById('mobileMenu');
      const burger= document.getElementById('iconBurger');
      const closeI= document.getElementById('iconClose');

      if (!btn || !menu) return;

      function toggleMenu(forceState){
        const willOpen = (typeof forceState === 'boolean') ? forceState : menu.classList.contains('hidden');
        menu.classList.toggle('hidden', !willOpen);
        burger.classList.toggle('hidden',  willOpen);
        closeI.classList.toggle('hidden', !willOpen);
        btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
      }

      btn.addEventListener('click', () => toggleMenu());

      document.addEventListener('click', (e) => {
        if (menu.classList.contains('hidden')) return;
        const withinMenu = menu.contains(e.target);
        const withinBtn  = btn.contains(e.target);
        if (!withinMenu && !withinBtn) toggleMenu(false);
      });

      window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) toggleMenu(false);
      });

      Array.from(menu.querySelectorAll('a,button[type="submit"]')).forEach(el => {
        el.addEventListener('click', () => toggleMenu(false));
      });
    })();
  </script>

  @stack('scripts')
</body>
</html>
