<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title','Dashboard SDM UNDIP')</title>

  <!-- Tailwind CSS (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Chart.js + Datalabels (CDN) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

  <!-- SheetJS (CDN) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

  <link rel="stylesheet" href="{{ asset('css/custom.css') }}"/>
  <style>
            /* Main background gradient */
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 25%, #60a5fa 50%, #93c5fd 75%, #dbeafe 100%);
            min-height: 100vh;
        }

                /* Section headers with enhanced styling */
        .section-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.8) 100%);
            border-left: 4px solid transparent;
            border-image: linear-gradient(135deg, #3b82f6, #8b5cf6) 1;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
  </style>
  @stack('head')
</head>

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
      <button id="cdmDownloadExcel"
              class="px-3 py-1 rounded bg-emerald-600 text-white text-sm hover:bg-emerald-700">
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

<body class="bg-gray-50">
  <nav class="gradient-bg text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center">
          <h1 class="text-xl font-bold">Dashboard SDM UNDIP</h1>
        </div>
        <div class="flex items-center space-x-4">
          <a href="{{ route('home') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">Beranda</a>
          @if(session()->has('admin_id'))
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">Admin</a>
            <form action="{{ route('logout') }}" method="post" class="inline">
              @csrf
              <button class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">Logout</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">Admin</a>
          @endif
        </div>
      </div>
    </div>
  </nav>

  <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    @yield('content')
  </main>

  <!-- App scripts -->
  {{-- <script src="{{ asset('js/charts.js') }}"></script> --}}
  <script src="{{ asset('js/charts.js') }}?v={{ filemtime(public_path('js/charts.js')) }}"></script>
  <script src="{{ asset('js/upload_preview_nolib.js') }}?v={{ filemtime(public_path('js/upload_preview_nolib.js')) }}"></script>

  @stack('scripts')
</body>
</html>
