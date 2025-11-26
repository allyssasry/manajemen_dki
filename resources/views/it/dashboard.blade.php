{{-- resources/views/it/dashboard.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard IT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- ====== EARLY SYNC: anti glitch sebelum paint ====== -->
  <script>
    (function () {
      try {
        var persisted = localStorage.getItem('it.sidebar.open') === '1';
        var isDesktop = window.matchMedia('(min-width: 768px)').matches;
        var pageWrapperML = (persisted && isDesktop) ? '18rem' : '4rem';
        var sidebarTransform = (persisted ? 'none' : 'translateX(-100%)');
        var showBackdrop = (!isDesktop && persisted);

        var css = ''
          + 'body{visibility:hidden}'
          + '#pageWrapper{margin-left:'+ pageWrapperML +' !important;}'
          + '#sidebar{transform:'+ sidebarTransform +' !important;}'
          + (showBackdrop ? '#sidebarBackdrop{display:block !important;}' : '');
        var s = document.createElement('style');
        s.id = 'early-sync';
        s.appendChild(document.createTextNode(css));
        document.head.appendChild(s);
      } catch (e) {}
    })();
  </script>

  <style>
    /* Matikan transisi/animasi global */
    *, *::before, *::after { transition: none !important; animation: none !important; scroll-behavior: auto !important; }

    html { scrollbar-gutter: stable; }
    body { overflow-x: hidden; }

    /* Scrollbar */
    .scroll-thin::-webkit-scrollbar{ width:6px; height:6px }
    .scroll-thin::-webkit-scrollbar-thumb{ background:#c89898; border-radius:9999px }
    .scroll-thin::-webkit-scrollbar-track{ background:transparent }

    /* Kartu maroon */
    .card-maroon { background:#7A1C1C; color:#fff; border-color:#7A1C1C; }
    .chip-soft   { background:#FFF2F2; color:#7A1C1C; }

    /* Grafik area (fallback style) */
    .chart-grid line { stroke:#f1d6d6; }
    .chart-axis text { fill:#7c7c7c; font-size:11px; }
  </style>
</head>

@php
  $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';
@endphp

<body class="min-h-screen bg-white text-gray-900">
  {{-- ===== MINI SIDEBAR (rail ikon) ===== --}}
  {{-- ================== MINI SIDEBAR (IT) ================== --}}
  @php
      $isItDashboardActive = request()->routeIs('it.dashboard');
  @endphp

  <aside id="miniSidebar"
    class="hidden md:flex fixed inset-y-0 left-0 z-40 w-16 bg-white border-r shadow-xl flex-col items-center justify-between py-4">
    <div class="flex flex-col items-center gap-6">
      <button id="railLogoBtn" type="button" title="Buka Sidebar" aria-label="Buka Sidebar"
              class="rounded-xl p-2 hover:bg-[#FFF2F2] cursor-pointer">
        <img src="{{ asset('images/dki.png') }}" class="h-6 w-auto object-contain" alt="Logo" />
      </button>

      {{-- DASHBOARD IT --}}
      <a href="{{ route('it.dashboard') }}"
         class="p-2 rounded-lg {{ $isItDashboardActive ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
         title="Dashboard" aria-label="Dashboard">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
             fill="{{ $isItDashboardActive ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
          <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
      </a>

      {{-- PROGRESS (global) --}}
      <a href="{{ route('semua.progresses') }}"
         class="p-2 rounded-lg {{ request()->routeIs('semua.progresses*') ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]'}}"
         title="Progress" aria-label="Progress">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
             fill="{{ request()->routeIs('semua.progresses*') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
          <path d="M4 22h16V2H4v20zm3-5h2v3H7v-3zm4-7h2v10h-2V10zm4 3h2v7h-2v-7z" />
        </svg>
      </a>

      {{-- NOTIFIKASI IT --}}
      <a href="{{ route('it.notifications') }}"
         class="p-2 rounded-lg {{ request()->routeIs('it.notifications*') ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
         title="Notifikasi" aria-label="Notifikasi">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
             fill="{{ request()->routeIs('it.notifications*') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
          <path d="M12 24a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z"/>
        </svg>
      </a>

      {{-- ARSIP (global) --}}
      <a href="{{ route('semua.arsip') }}"
         class="p-2 rounded-lg {{ request()->routeIs('semua.arsip*') ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]'}}"
         title="Arsip" aria-label="Arsip">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
             fill="{{ request()->routeIs('semua.arsip*') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
          <path d="M3 3h18v4H3V3zm1 4h16v14H4V7zm2 2v10h12V9H6zm3 3h6v2H9v-2z" />
        </svg>
      </a>
    </div>

    <div class="flex flex-col items-center gap-4">
      <a href="{{ route('account.setting') }}"
         class="p-2 rounded-lg {{ request()->routeIs('account.setting*') ? 'bg-[#FFF2F2]' : 'hover:bg-[#FFF2F2]' }}"
         title="Pengaturan Akun" aria-label="Pengaturan Akun">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" viewBox="0 0 24 24" fill="currentColor">
          <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1c-.6-.35-1.22-.6-1.87-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 110 7 3.5 3.5 0 010-7z"/>
        </svg>
      </a>

      <a href="/logout" class="p-2 rounded-lg hover:bg-[#FFF2F2]" title="Log Out" aria-label="Log Out">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="black">
          <path d="M3 3h10a1 1 0 0 1 1 1v5h-2V5H5v14h7v-4h2v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/>
          <path d="M14 12l5-5v3h4v4h-4v3l-5-5z"/>
        </svg>
      </a>
    </div>
  </aside>

  {{-- ================== SIDEBAR PENUH (IT) ================== --}}
  <div id="sidebarBackdrop" class="hidden fixed inset-0 z-40 bg-black/30 md:hidden"></div>

  <aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] -translate-x-full transition-transform duration-300 ease-out
           bg-white border-r shadow-xl flex flex-col">

    <div class="px-5 pt-5 pb-4 border-b bg-white">
      <div class="flex items-center">
        <img src="https://website-api.bankdki.co.id/integrations/storage/page-meta-data/007UlZbO3Oe6PivLltdFiQax6QH5kWDvb0cKPdn4.png"
             class="h-8 w-auto object-contain" alt="Bank Jakarta">
        <button id="sidebarCloseBtn"
                class="ml-auto p-2 rounded-lg border hover:bg-red-50 text-red-700"
                title="Tutup" aria-label="Tutup sidebar">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.71 2.89 18.3 9.17 12 2.89 5.71 4.3 4.29l6.29 6.29 6.3-6.29z"/>
          </svg>
        </button>
      </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-3 text-sm font-medium text-gray-700">
      {{-- DASHBOARD --}}
      <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-3 mb-1">Dashboard</div>
      <a href="{{ route('it.dashboard') }}"
         class="flex items-center gap-3 px-5 py-2.5 transition-all duration-150 rounded-xl
                {{ request()->routeIs('it.dashboard') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
             fill="{{ request()->routeIs('it.dashboard') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
        <span>Dashboard</span>
      </a>

      {{-- PROGRESS --}}
      <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Project</div>
      <a href="{{ route('semua.progresses') }}"
         class="flex items-center gap-3 px-5 py-2.5 transition-all duration-150 rounded-xl
                {{ request()->routeIs('semua.progresses*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
             fill="{{ request()->routeIs('semua.progresses*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
          <path d="M4 22h16V2H4v20zm3-5h2v3H7v-3zm4-7h2v10h-2V10zm4 3h2v7h-2v-7z" />
        </svg>
        <span>Project</span>
      </a>

      {{-- NOTIFIKASI IT --}}
      <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Notifikasi</div>
      <a href="{{ route('it.notifications') }}"
         class="flex items-center gap-3 px-5 py-2.5 transition-all duration-150 rounded-xl
                {{ request()->routeIs('it.notifications*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
             fill="{{ request()->routeIs('it.notifications*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
          <path d="M12 24a2.5 2.5 0 0 0 2.45-2H9.55A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z"/>
        </svg>
        <span>Notifikasi</span>
      </a>

      {{-- ARSIP --}}
      <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Arsip</div>
      <a href="{{ route('semua.arsip') }}"
         class="flex items-center gap-3 px-5 py-2.5 transition-all duration-150 rounded-xl
                {{ request()->routeIs('semua.arsip*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
             fill="{{ request()->routeIs('semua.arsip*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
          <path d="M3 3h18v4H3V3zm1 4h16v14H4V7zm2 2v10h12V9H6zm3 3h6v2H9v-2z" />
        </svg>
        <span>Arsip</span>
      </a>
    </nav>

    {{-- FOOTER MENU --}}
    <div class="mt-6 mb-9 px-3 space-y-1 text-sm text-gray-900">
      <a href="{{ route('account.setting') }}"
         class="flex items-center gap-3 px-3 py-2 rounded-xl transition
                {{ request()->routeIs('account.setting*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2]' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" viewBox="0 0 24 24" fill="currentColor">
          <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1c-.6-.35-1.22-.6-1.87-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"/>
        </svg>
        <span>Pengaturan Akun</span>
      </a>

      <a href="/logout" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-[#FFF2F2] transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" fill="black" viewBox="0 0 24 24">
          <path d="M3 3h10a1 1 0 0 1 1 1v5h-2V5H5v14h7v-4h2v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
          <path d="M14 12l5-5v3h4v4h-4v3l-5-5z" />
        </svg>
        <span>Log Out</span>
      </a>
    </div>
  </aside>


  {{-- WRAPPER --}}
  <div id="pageWrapper" class="md:ml-16">

    {{-- NAVBAR --}}
    <header class="sticky top-0 z-30 bg-[#8D2121]">
      <div class="{{ $container }} py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <button id="sidebarOpenBtn"
                  class="p-2 rounded-xl border border-red-200 text-red-50 hover:bg-red-700/20 md:hidden"
                  title="Buka Sidebar" aria-label="Buka Sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
              <path d="M3 3h18v2H3V3zm0 6h18v2H3V9zm0 6h18v2H3v-2z" />
            </svg>
          </button>
          <span class="text-lg md:text-xl font-bold text-white select-none">Dashboard IT</span>
        </div>

        @php
          $me = $me ?? auth()->user()->fresh();
          $role = $me?->role;
          $roleLabel = $role === 'it' ? 'IT' : ($role === 'digital_banking' ? 'DIG' : ($role === 'supervisor' ? 'Supervisor' : 'User'));
          $initial = urlencode(mb_substr($me?->name ?? ($me?->username ?? 'U'), 0, 1));
          $fallbackSvg = 'data:image/svg+xml;utf8,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"><rect width="100%" height="100%" rx="12" ry="12" fill="#7A1C1C"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="28" fill="#fff">'.$initial.'</text></svg>');
          $rawUrl   = $me?->avatar_url_public;
          $extraKey = $me?->avatar_cache_key ?? ($me?->updated_at?->timestamp ?? time());
          $avatarUrl= $rawUrl ? ($rawUrl.(str_contains($rawUrl,'?') ? '&' : '?').'ck='.$extraKey) : $fallbackSvg;
        @endphp

        <div class="flex items-center gap-3 pl-4 border-l border-white/30">
          <img src="{{ $avatarUrl }}" class="w-8 h-8 rounded-full object-cover bg-white"
               alt="Avatar" loading="lazy" referrerpolicy="no-referrer"
               onerror="this.onerror=null;this.src='{{ $fallbackSvg }}';">
          <div class="leading-tight hidden md:block">
            <div class="text-[13px] font-semibold text-white max-w-[160px] truncate">{{ $me?->name ?? ($me?->username ?? 'User') }}</div>
            <div class="text-[11px] text-white/80">{{ $roleLabel }}</div>
          </div>
        </div>
      </div>
    </header>

    {{-- ====== DATA & KPI (server prep) ====== --}}
    @php
      use Illuminate\Support\Collection;
      use Carbon\Carbon;

      if (!isset($digitalUsers) || !($digitalUsers instanceof Collection)) {
        try { $digitalUsers = \App\Models\User::where('role','digital_banking')->orderByRaw('COALESCE(NULLIF(name, \'\'), username) ASC')->get(['id','name','username']); } catch (\Throwable $e) { $digitalUsers = collect(); }
      }
      if (!isset($itUsers) || !($itUsers instanceof Collection)) {
        try { $itUsers = \App\Models\User::where('role','it')->orderByRaw('COALESCE(NULLIF(name, \'\'), username) ASC')->get(['id','name','username']); } catch (\Throwable $e) { $itUsers = collect(); }
      }

      $scope      = request('scope','all');
      $userId     = (int)auth()->id();
      $scopeMeet  = request('scope_meet','all');
      $scopeNot   = request('scope_not','all');
      $monthStartParam = request('month_start');
      $monthEndParam   = request('month_end');
      $mode = request('mode','real');  // real | confirm
      $chartProjectId = (int) request('chart_project_id', 0);

      try { $periodStart = $monthStartParam ? Carbon::createFromFormat('Y-m',$monthStartParam)->startOfMonth() : Carbon::now()->startOfMonth(); }
      catch (\Throwable $e) { $periodStart = Carbon::now()->startOfMonth(); }
      try { $periodEnd   = $monthEndParam ? Carbon::createFromFormat('Y-m',$monthEndParam)->endOfMonth()     : Carbon::now()->endOfMonth(); }
      catch (\Throwable $e) { $periodEnd   = Carbon::now()->endOfMonth(); }

      if ($periodEnd->lt($periodStart)) { $tmp=$periodStart->copy(); $periodStart=$periodEnd->copy()->startOfMonth(); $periodEnd=$tmp->endOfMonth(); }

      $monthStartVal = $periodStart->format('Y-m');
      $monthEndVal   = $periodEnd->format('Y-m');
      $ws = $periodStart->format('m/Y'); $we = $periodEnd->format('m/Y');

      $between = function ($date) use ($periodStart,$periodEnd) {
        if (!$date) return false;
        try { $d = $date instanceof Carbon ? $date : Carbon::parse($date); return $d->betweenIncluded($periodStart,$periodEnd); }
        catch (\Throwable $e) { return false; }
      };
      $overlapRange = function ($start,$end) use ($periodStart,$periodEnd,$between) {
        try {
          $s = $start ? Carbon::parse($start) : null; $e = $end ? Carbon::parse($end) : null;
          if ($s && $e) return $s <= $periodEnd && $e >= $periodStart;
          if ($s && !$e) return $between($s);
          if (!$s && $e) return $between($e);
          return false;
        } catch (\Throwable $e) { return false; }
      };
      $projectTouchesPeriod = function ($project) use ($between,$overlapRange) {
        if ($between($project->completed_at ?? null)) return true;
        if ($between($project->created_at ?? null))  return true;
        foreach ($project->progresses ?? collect() as $pr) {
          if ($overlapRange($pr->start_date ?? null, $pr->end_date ?? null)) return true;
          foreach ($pr->updates ?? collect() as $up) { if ($between($up->update_date ?? null)) return true; }
        }
        return false;
      };

      // Koleksi scope utk IT:
      $all  = $projects ?? collect();
      $mine = $all->filter(fn($p) => (int)($p->developer_id ?? 0) === $userId || (int)($p->created_by ?? 0) === $userId);

      $allPeriod  = $all->filter($projectTouchesPeriod)->values();
      $minePeriod = $mine->filter($projectTouchesPeriod)->values();

      $calc = function (Collection $col) use ($between) {
        $completedInPeriod = $col->filter(fn($p) => !is_null($p->completed_at) && $between($p->completed_at));
        $meet = $completedInPeriod->where('meets_requirement', true)->count();
        $not  = $completedInPeriod->where('meets_requirement', false)->count();
        $tot  = $completedInPeriod->count();
        $acc  = $tot > 0 ? (int) round(($meet / $tot) * 100) : 0;
        return [$meet,$not,$tot,$acc];
      };

      [$meetAll,$notAll,$completedAll,$accAll]     = $calc($allPeriod);
      [$meetMine,$notMine,$completedMine,$accMine] = $calc($minePeriod);

      [$meetCount_global,$notMeetCount_global,$completedCount_global,$acc] =
        $scope === 'mine' ? [$meetMine,$notMine,$completedMine,$accMine] : [$meetAll,$notAll,$completedAll,$accAll];

      $scopedPeriodGlobal = $scope === 'mine' ? $minePeriod : $allPeriod;
      $totalScopeProjects = $scopedPeriodGlobal->count();

      $meetDisplay = $scopeMeet === 'mine' ? $meetMine : $meetAll;
      $notMeetDisplay = $scopeNot === 'mine' ? $notMine : $notAll;

      $size=110; $stroke=12; $r=$size/2-$stroke; $circ=2*pi()*$r; $off=$circ*(1-$acc/100);

      $scopedAll = $scope === 'mine' ? $mine : $all;
      $displayProjects = $scopedAll->values();

      // ===== Pilihan project utk grafik
      $chartProject = $chartProjectId ? $displayProjects->firstWhere('id',$chartProjectId) : $displayProjects->first();
      $chartTitle = $chartProject ? $chartProject->name : '-';

      // ===== LOGIKA GRAFIK (identik DIG)
      $chartPoints = [];
      if ($chartProject) {
        $candStart = collect();
        $candStop  = collect();

        foreach ($chartProject->progresses as $pr) {
          if ($overlapRange($pr->start_date ?? null, $pr->end_date ?? null) && $pr->start_date) {
            $candStart->push(Carbon::parse($pr->start_date)->startOfDay());
          }
          foreach ($pr->updates as $up) {
            if ($between($up->update_date ?? null)) {
              $candStart->push(Carbon::parse($up->update_date)->startOfDay());
              $candStop->push(Carbon::parse($up->update_date)->endOfDay());
            }
          }
          if ($pr->end_date && $overlapRange($pr->start_date ?? null, $pr->end_date ?? null)) {
            $candStop->push(Carbon::parse($pr->end_date)->endOfDay());
          }
          if (!empty($pr->confirmed_at) && $between($pr->confirmed_at)) {
            $candStop->push(Carbon::parse($pr->confirmed_at)->endOfDay());
          }
        }

        if (!empty($chartProject->completed_at) && $between($chartProject->completed_at)) {
          $candStop->push(Carbon::parse($chartProject->completed_at)->endOfDay());
        }

        $from = $candStart->isNotEmpty()
          ? $candStart->min()->copy()->startOfDay()
          : $periodStart->copy()->startOfMonth()->startOfDay();

        $stopReal = $candStop->isNotEmpty()
          ? $candStop->max()->copy()->endOfDay()
          : ($candStart->isNotEmpty()
              ? $candStart->max()->copy()->endOfDay()
              : $from->copy()->endOfDay());

        if ($from->lt($periodStart)) $from = $periodStart->copy()->startOfDay();
        $to = $stopReal->gt($periodEnd) ? $periodEnd->copy()->endOfDay() : $stopReal->copy()->endOfDay();
        if ($from->greaterThan($to)) { $tmp=$from; $from=$to->copy()->startOfDay(); $to=$tmp->copy()->endOfDay(); }

        $updateDates = collect();
        foreach ($chartProject->progresses as $pr) {
          foreach ($pr->updates as $up) {
            try {
              $ud = Carbon::parse($up->update_date);
              if ($ud->betweenIncluded($from, $to)) $updateDates->push($ud->copy()->startOfDay());
            } catch (\Throwable $e) {}
          }
        }
        $updateDates = $updateDates->unique(fn($d)=>$d->toDateString())->sort()->values();

        if ($mode === 'confirm') {
          $latestConfirm = collect($chartProject->progresses)->map(function($pr){
            return $pr->confirmed_at ? Carbon::parse($pr->confirmed_at)->endOfDay() : null;
          })->filter()->max();
          if ($latestConfirm) {
            $to = $latestConfirm->lt($to) ? $latestConfirm : $to;
            $updateDates = $updateDates->filter(fn($d)=>$d->lte($to))->values();
          }
        }

        $prevY = null;
        foreach ($updateDates as $d) {
          $sum = 0;
          foreach ($chartProject->progresses as $pr) {
            $last = $pr->updates
              ->filter(function($u) use ($d){
                try { return Carbon::parse($u->update_date)->toDateString() <= $d->toDateString(); }
                catch (\Throwable $e) { return false; }
              })
              ->sortByDesc('update_date')
              ->first();

            $val = $last ? (int)($last->percent ?? ($last->progress_percent ?? 0)) : 0;
            if ($mode === 'confirm' && is_null($pr->confirmed_at)) $val = 0;
            $sum += $val;
          }
          $y = max(0, min(100, (int)$sum));
          $delta = is_null($prevY) ? 0 : ($y - $prevY);
          $prevY = $y;

          $chartPoints[] = [
            'x'     => $d->format('d/m'),
            'y'     => $y,
            'delta' => $delta,
            'title' => $d->format('d/m/Y'),
          ];
        }

        if (empty($chartPoints)) {
          $sum = 0;
          foreach ($chartProject->progresses as $pr) {
            $last = $pr->updates->sortByDesc('update_date')->first();
            $val = $last ? (int)($last->percent ?? ($last->progress_percent ?? 0)) : 0;
            if ($mode === 'confirm' && is_null($pr->confirmed_at)) $val = 0;
            $sum += $val;
          }
          $sum = max(0, min(100,(int)$sum));
          $chartPoints[] = [
            'x' => $to->format('d/m'),
            'y' => $sum,
            'delta' => 0,
            'title' => $to->format('d/m/Y'),
          ];
        }
      }
    @endphp

    {{-- ===== BANNER ===== --}}
    <section class="relative h-[260px] md:h-[320px] overflow-hidden">
      <img src="https://i.pinimg.com/736x/c5/43/71/c543719c97d9efa97da926387fa79d1f.jpg" class="w-full h-full object-cover" alt="Banner" />
      <div class="absolute inset-0 bg-black/30"></div>
      <div class="absolute inset-0 flex items-center justify-center">
        <h1 class="text-white text-2xl md:text-3xl font-bold">Selamat Datang di Timeline Progress (IT)</h1>
      </div>
    </section>

    {{-- ===== Project Information bar ===== --}}
    <div class="bg-white">
      <div class="{{ $container }} mt-4">
        <div class="rounded-xlbg-white shadow-sm px-4 py-3 flex items-center justify-between">
          <span class="text-sm md:text-base font-semibold text-gray-700">Project Information</span>
          <a href="{{ route('semua.projects.create') }}"
              class="inline-flex items-center gap-2 rounded-[12px] border border-[#7A1C1C] px-4 py-2 text-sm font-semibold text-white bg-[#8D2121] hover:bg-[#741B1B]">
              + Tambah Project
          </a>
        </div>
      </div>
    </div>

    {{-- ===== Project Work Progress (grafik) ===== --}}
    <section class="{{ $container }} mt-4">
      <div class="rounded-2xl border border-red-200 bg-white p-3 md:p-4">
        <div class="flex items-center justify-between">
          <div class="text-sm font-semibold text-gray-700">Project Work Progress</div>
          <form method="GET" action="{{ route('it.dashboard') }}" class="flex items-center gap-2 text-xs">
            <input type="hidden" name="month_start" value="{{ $monthStartVal }}">
            <input type="hidden" name="month_end"   value="{{ $monthEndVal }}">
            <select name="chart_project_id" class="border rounded-md px-2 py-1 bg-[#FFF7F7] border-red-200">
              @foreach($displayProjects as $p)
                <option value="{{ $p->id }}" {{ ($chartProject && $chartProject->id===$p->id) ? 'selected' : '' }}>{{ $p->name }}</option>
              @endforeach
            </select>
            <select name="mode" class="border rounded-md px-2 py-1 bg-[#FFF7F7] border-red-200">
              <option value="real" {{ $mode==='real'?'selected':'' }}>Realisasi (update)</option>
              <option value="confirm" {{ $mode==='confirm'?'selected':'' }}>Terkonfirmasi</option>
            </select>
            <button class="h-7 px-3 rounded-md border border-red-200 px-2 py-1 font-semibold text-sm text-[#8D2121] bg-[#FFF7F7] hover:bg-[#8D2121]/10">Terapkan</button>
          </form>
        </div>

        <div class="mt-3 rounded-xl border border-red-100 bg-[#FFF8F8] p-3">
          <div class="text-center text-[12px] text-gray-600 mb-1">Work Progress Overview (Project: {{ $chartTitle }})</div>

          <div class="relative w-full">
            <svg id="workProgressSvg" viewBox="0 0 800 320" class="w-full h-[260px] md:h-[300px]">
              <!-- sumbu & grid akan digambar via JS -->
            </svg>
          </div>

          <script>
            (function(){
              const points = @json($chartPoints); // [{x, y, delta, title}]
              const svg = document.getElementById('workProgressSvg');
              const W = 800, H = 320, PADL=50, PADR=16, PADT=22, PADB=42;
              const plotW = W - PADL - PADR, plotH = H - PADT - PADB;

              while (svg.firstChild) svg.removeChild(svg.firstChild);

              const NS = 'http://www.w3.org/2000/svg';
              const gGrid = document.createElementNS(NS,'g'); gGrid.setAttribute('class','chart-grid');
              const gAxis = document.createElementNS(NS,'g'); gAxis.setAttribute('class','chart-axis');
              const gArea = document.createElementNS(NS,'g');

              const yMax = 100, yMin = 0, yTicks = 5;
              for (let i=0;i<=yTicks;i++){
                const t = i/yTicks, y = PADT + plotH - t*plotH;
                const ln = document.createElementNS(NS,'line');
                ln.setAttribute('x1', PADL); ln.setAttribute('x2', PADL+plotW);
                ln.setAttribute('y1', y);    ln.setAttribute('y2', y);
                ln.setAttribute('stroke-width','1');
                gGrid.appendChild(ln);

                const lbl = document.createElementNS(NS,'text');
                lbl.setAttribute('x', PADL-10); lbl.setAttribute('y', y+4);
                lbl.setAttribute('text-anchor','end');
                lbl.textContent = Math.round(yMin+(yMax-yMin)*t);
                gAxis.appendChild(lbl);
              }

              const n = Math.max(points.length, 1);
              const stepX = n > 1 ? (plotW / (n - 1)) : plotW;

              const pathPts = [];
              for (let i=0;i<n;i++){
                const p = points[i] || points[0];
                const x = PADL + i * stepX;
                const y = PADT + plotH - ((p?.y || 0)/100)*plotH;
                pathPts.push({x,y,p});
              }

              pathPts.forEach((q,i)=>{
                const tx = document.createElementNS(NS,'text');
                tx.setAttribute('x', q.x);
                tx.setAttribute('y', PADT+plotH+18);
                tx.setAttribute('text-anchor','middle');
                tx.textContent = points[i]?.x || (points[0]?.x || '');
                gAxis.appendChild(tx);
              });

              if (n >= 2) {
                const dArea = pathPts.map((q,i)=> (i?'L':'M')+q.x+' '+q.y).join(' ')
                             + ` L ${pathPts[pathPts.length-1].x} ${PADT+plotH}`
                             + ` L ${pathPts[0].x} ${PADT+plotH} Z`;
                const area = document.createElementNS(NS,'path');
                area.setAttribute('d', dArea);
                area.setAttribute('fill', '#F5B8B8');
                area.setAttribute('fill-opacity','0.55');
                gArea.appendChild(area);

                const dLine = pathPts.map((q,i)=> (i?'L':'M')+q.x+' '+q.y).join(' ');
                const line = document.createElementNS(NS,'path');
                line.setAttribute('d', dLine);
                line.setAttribute('fill','none');
                line.setAttribute('stroke','#E11D48');
                line.setAttribute('stroke-width','2.5');
                gArea.appendChild(line);
              }

              pathPts.forEach((q,i)=>{
                const outer = document.createElementNS(NS,'circle');
                outer.setAttribute('cx', q.x); outer.setAttribute('cy', q.y); outer.setAttribute('r','6');
                outer.setAttribute('fill','#fff'); outer.setAttribute('stroke','#FF2D2D'); outer.setAttribute('stroke-width','2');
                gArea.appendChild(outer);

                const inner = document.createElementNS(NS,'circle');
                inner.setAttribute('cx', q.x); inner.setAttribute('cy', q.y); inner.setAttribute('r','2');
                inner.setAttribute('fill','#FF2D2D');
                gArea.appendChild(inner);

                const d = q.p?.delta ?? 0;
                const textVal = (i===0 ? `${q.p?.y ?? 0}%` : ((d>0?`+${d}`:`${d}`)+'%'));
                const tDelta = document.createElementNS(NS,'text');
                tDelta.setAttribute('x', q.x);
                tDelta.setAttribute('y', q.y - 10);
                tDelta.setAttribute('text-anchor','middle');
                tDelta.setAttribute('font-size','10');
                tDelta.setAttribute('fill','#7A1C1C');
                tDelta.textContent = textVal;
                gArea.appendChild(tDelta);

                if (q.p?.title){
                  const title = document.createElementNS(NS,'title');
                  title.textContent = `${q.p.title} • Total ${q.p.y}% (${i===0? 'awal' : (d>0? '+'+d: d)}%)`;
                  outer.appendChild(title);
                  inner.appendChild(title.cloneNode(true));
                }
              });

              if (n === 1) {
                const note = document.createElementNS(NS,'text');
                note.setAttribute('x', PADL + 4);
                note.setAttribute('y', PADT + 14);
                note.setAttribute('text-anchor','start');
                note.setAttribute('font-size','11');
                gAxis.appendChild(note);
              }

              svg.appendChild(gGrid);
              svg.appendChild(gAxis);
              svg.appendChild(gArea);
            })();
          </script>
        </div>
      </div>
    </section>

    {{-- ===== KPI SECTION (maroon) ===== --}}
    <section class="{{ $container }} mt-5">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        {{-- Project Akumulasi --}}
        <div class="rounded-2xl card-maroon border p-4 min-h-[140px] md:col-span-6">
          <div class="flex items-center justify-between mb-3">
            <div class="font-semibold text-sm">Project Akumulasi</div>
            <form method="GET" action="{{ route('it.dashboard') }}" class="flex items-center gap-2">
              <input type="hidden" name="scope" value="{{ $scope }}">
              <input type="hidden" name="scope_meet" value="{{ $scopeMeet }}">
              <input type="hidden" name="scope_not" value="{{ $scopeNot }}">
              <input type="month" name="month_start" value="{{ $monthStartVal }}" class="h-8 rounded-lg border border-red-300 bg-white/10 px-2 text-xs">
              <span class="text-xs text-white/80">s/d</span>
              <input type="month" name="month_end" value="{{ $monthEndVal }}" class="h-8 rounded-lg border border-red-300 bg-white/10 px-2 text-xs">
              <button class="h-8 px-3 rounded-lg bg-white text-[#7A1C1C] text-xs font-semibold">Terapkan</button>
            </form>
          </div>
          <div class="flex items-center gap-4">
            <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}">
              <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}" stroke="#A05353" stroke-width="{{ $stroke }}" fill="none" opacity=".35"/>
              <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}" stroke="#fff" stroke-width="{{ $stroke }}" stroke-linecap="round"
                      stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $off }}"
                      transform="rotate(-90 {{ $size/2 }} {{ $size/2 }})" fill="none"/>
              <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="18" font-weight="700" fill="#fff">{{ $acc }}%</text>
            </svg>
            <div class="text-xs text-white min-w-0 break-words">
              <div>Periode: <span class="font-semibold">{{ $ws }}–{{ $we }}</span></div>
              <div>Scope aktif: <span class="font-semibold">{{ $scope === 'mine' ? 'Tugas saya' : 'Semua' }}</span></div>
              <div class="mt-1">Total project (scope global): <span class="font-semibold">{{ $totalScopeProjects }}</span></div>
              <div class="mt-1">Selesai (periode): <span class="font-semibold">Saya {{ $completedMine }} • Semua {{ $completedAll }}</span></div>
            </div>
          </div>
        </div>

        {{-- Memenuhi --}}
        <div class="rounded-2xl card-maroon border p-4 grid min-h-[140px] md:col-span-3">
          <div class="flex items-center justify-between mb-3">
            <div class="font-semibold text-sm">Project Selesai, Memenuhi</div>
            <form method="GET" action="{{ route('it.dashboard') }}" class="flex items-center gap-2">
              <input type="hidden" name="month_start" value="{{ $monthStartVal }}">
              <input type="hidden" name="month_end"  value="{{ $monthEndVal }}">
              <input type="hidden" name="scope"      value="{{ $scope }}">
              <input type="hidden" name="scope_not"  value="{{ $scopeNot }}">
              <select name="scope_meet" class="h-8 rounded-lg border bg-white/10 px-2 text-xs" onchange="this.form.submit()">
                <option value="all"  {{ $scopeMeet === 'all'  ? 'selected' : '' }}>Semua</option>
                <option value="mine" {{ $scopeMeet === 'mine' ? 'selected' : '' }}>Tugas saya</option>
              </select>
            </form>
          </div>
          <div class="text-4xl font-bold place-self-center text-white">{{ $meetDisplay }}</div>
          <div class="text-[11px] text-white/80 mt-2 text-center">Saya: {{ $meetMine }} · Semua: {{ $meetAll }}</div>
        </div>

        {{-- Tidak Memenuhi --}}
        <div class="rounded-2xl card-maroon border p-4 grid min-h-[140px] md:col-span-3">
          <div class="flex items-center justify-between mb-3">
            <div class="font-semibold text-sm">Project Selesai, Tidak Memenuhi</div>
            <form method="GET" action="{{ route('it.dashboard') }}" class="flex items-center gap-2">
              <input type="hidden" name="month_start" value="{{ $monthStartVal }}">
              <input type="hidden" name="month_end"  value="{{ $monthEndVal }}">
              <input type="hidden" name="scope"      value="{{ $scope }}">
              <input type="hidden" name="scope_meet" value="{{ $scopeMeet }}">
              <select name="scope_not" class="h-8 rounded-lg border bg-white/10 px-2 text-xs" onchange="this.form.submit()">
                <option value="all"  {{ $scopeNot === 'all'  ? 'selected' : '' }}>Semua</option>
                <option value="mine" {{ $scopeNot === 'mine' ? 'selected' : '' }}>Tugas saya</option>
              </select>
            </form>
          </div>
          <div class="text-4xl font-bold place-self-center text-white">{{ $notMeetDisplay }}</div>
          <div class="text-[11px] text-white/80 mt-2 text-center">Saya: {{ $notMine }} · Semua: {{ $notAll }}</div>
        </div>
      </div>
    </section>

    {{-- ===== KONTEN UTAMA ===== --}}
    <div class="{{ $container }}">
      {{-- NOTIFIKASI --}}
      @if (session('success'))
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ session('success') }}</div>
      @endif
      @if ($errors->any())
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
          <div class="font-semibold mb-1">Terjadi kesalahan:</div>
          <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
          </ul>
        </div>
      @endif

      {{-- EMPTY STATE --}}
      @if (($displayProjects ?? collect())->isEmpty())
        <div class="mt-6">
          <div class="bg-[#EBD0D0] rounded-2xl px-6 py-8 flex items-center justify-center">
            <div class="rounded-2xl bg-[#CFA8A8] px-5 py-3 text-white/95">Anda belum memiliki project</div>
          </div>
        </div>
      @endif

      {{-- DAFTAR PROJECT --}}
      @if (($displayProjects ?? collect())->isNotEmpty())
        <div class="mt-8 space-y-8">
          @foreach ($displayProjects as $project)
            @php
              $sumDesired = (int) $project->progresses->sum(fn($p)=>(int)$p->desired_percent);
              $sumReal = (int) $project->progresses->sum(function($p){
                $last = $p->updates->sortByDesc('update_date')->first();
                return $last ? (int)($last->percent ?? ($last->progress_percent ?? 0)) : 0;
              });
              $barWidth = max(0, min(100, $sumReal));

              $finalized = !is_null($project->meets_requirement) || !is_null($project->completed_at);
              $progressCount = $project->progresses->count();
              $allConfirmedAndMet = $progressCount > 0 && $project->progresses->every(function($p){
                $last = $p->updates->sortByDesc('update_date')->first();
                $real = $last ? (int)($last->percent ?? ($last->progress_percent ?? 0)) : 0;
                return !is_null($p->confirmed_at) && $real >= (int)$p->desired_percent;
              });
              if ($finalized) {
                $statusText = $project->meets_requirement ? 'Project Selesai, Memenuhi' : 'Project Selesai, Tidak Memenuhi';
                $statusColor = $project->meets_requirement ? '#166534' : '#7A1C1C';
              } elseif ($allConfirmedAndMet) {
                $statusText = 'Menunggu Finalisasi (DIG)';
                $statusColor = '#7A1C1C';
              } else {
                $statusText = 'Dalam Proses';
                $statusColor = '#7A1C1C';
              }

              $meUser = auth()->user();
            @endphp

            <div class="rounded-2xl border-2 border-[#7A1C1C] bg-white p-5">
              <div class="grid md:grid-cols-[1fr,auto] items-start gap-4">
                <div class="flex flex-col gap-3 w-full">
                  <div class="text-xs font-semibold">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                          style="color: {{ $statusColor }}; background-color: {{ $finalized ? '#DCFCE7' : '#FEE2E2' }};">
                      {{ $statusText }}
                    </span>
                  </div>

                  <div class="w-full">
                    <div class="flex items-center justify-between mb-1">
                      <span class="text-[13px] text-gray-700">Progress Project</span>
                      <span class="text-[11px] text-gray-700 font-semibold">{{ $sumReal }}%</span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-[#f1d6d6] overflow-hidden">
                      <div class="h-3 bg-[#7A1C1C] rounded-full" style="width: {{ $barWidth }}%;"></div>
                    </div>
                    <div class="mt-1 text-[11px] text-gray-600">Target (akumulasi “Keinginan Awal”): {{ $sumDesired }}%</div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-2 text-sm mt-2">
                    <div class="grid grid-cols-[auto_auto_1fr] gap-x-2"><span class="text-gray-600">Nama Project</span><span>:</span><span class="font-semibold">{{ $project->name }}</span></div>
                    <div class="grid grid-cols-[auto_auto_1fr] gap-x-2"><span class="text-gray-600">Keinginan Awal</span><span>:</span><span class="font-semibold">{{ $sumDesired }}%</span></div>
                    <div class="grid grid-cols-[auto_auto_1fr] gap-x-2"><span class="text-gray-600">Realisasi</span><span>:</span><span class="font-semibold">{{ $sumReal }}%</span></div>
                    <div class="grid grid-cols-[auto_auto_1fr] gap-x-2"><span class="text-gray-600">Penanggung Jawab (DIG)</span><span>:</span><span>{{ $project->digitalBanking->name ?? '-' }}</span></div>
                    <div class="grid grid-cols-[auto_auto_1fr] gap-x-2"><span class="text-gray-600">Penanggung Jawab (IT)</span><span>:</span><span>{{ $project->developer->name ?? '-' }}</span></div>
                    <div class="grid grid-cols-[auto_auto_1fr] gap-x-2"><span class="text-gray-600">Deskripsi</span><span>:</span><span>{{ $project->description ?: '-' }}</span></div>
                  </div>

                  {{-- ===== LAMPIRAN PROJECT (LIST) ===== --}}
                  @if ($project->attachments && $project->attachments->isNotEmpty())
                    <div class="mt-3">
                      <div class="text-xs font-semibold text-gray-700 mb-1">Lampiran</div>
                      <div class="flex flex-wrap gap-2">
                        @foreach ($project->attachments as $att)
                          @php
                            $isPdf =
                              str_contains(strtolower($att->mime_type ?? ''), 'pdf') ||
                              \Illuminate\Support\Str::endsWith(strtolower($att->original_name), '.pdf');
                            $url = route('attachments.show', $att->id);
                          @endphp
                          <a href="{{ $url }}" target="_blank"
                             class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs border border-[#C89898] bg-[#FFF7F7] hover:bg-[#FDEEEE]">
                            @if ($isPdf)
                              <span class="inline-flex w-5 h-5 items-center justify-center rounded-full bg-red-600 text-white text-[10px]">PDF</span>
                            @else
                              <span class="inline-flex w-5 h-5 items-center justify-center rounded-full bg-amber-500 text-white text-[10px]">IMG</span>
                            @endif
                            <span class="truncate max-w-[140px]" title="{{ $att->original_name }}">{{ $att->original_name }}</span>
                          </a>
                        @endforeach
                      </div>
                    </div>
                  @endif
                  {{-- ===== /LAMPIRAN PROJECT (LIST) ===== --}}
                </div>

                <div class="flex items-start gap-2 justify-end">
                  {{-- FINALISASI PROJECT: IT tidak bisa (hanya tampil jika policy memperbolehkan) --}}
                  @can('finalize', $project)
                    @php
                      $canDecideCompletion = $project->progresses->every(function($pr){
                        $last = $pr->updates->sortByDesc('update_date')->first();
                        $real = $last ? (int)($last->percent ?? ($last->progress_percent ?? 0)) : 0;
                        return !is_null($pr->confirmed_at) && $real >= (int)$pr->desired_percent;
                      }) && is_null($project->meets_requirement);
                    @endphp
                    @if ($canDecideCompletion)
                      <form method="POST" action="{{ route('projects.setCompletion', $project->id) }}" class="mr-2">
                        @csrf @method('PATCH')
                        <input type="hidden" name="meets" value="1">
                        <button class="px-3 py-1.5 text-xs rounded-full bg-green-700 text-white">Memenuhi</button>
                      </form>
                      <form method="POST" action="{{ route('projects.setCompletion', $project->id) }}" class="mr-2">
                        @csrf @method('PATCH')
                        <input type="hidden" name="meets" value="0">
                        <button class="px-3 py-1.5 text-xs rounded-full bg-[#7A1C1C] text-white">Tidak Memenuhi</button>
                      </form>
                    @endif
                  @endcan

                  {{-- Edit lama (ke halaman edit) tetap dipertahankan --}}
                  @can('update', $project)
                    <a href="{{ route('semua.projects.edit', $project->id) }}"
                      class="p-2 rounded-lg bg-white hover:bg-[#FFF2F2] border"
                      title="Edit Project">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                          fill="currentColor">
                          <path
                              d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM22.61 5.64c.39-.39.39-1.02 0-1.41l-2.83-2.83a.9959.9959 0 0 0-1.41 0L16.13 3.04l3.75 3.75 2.73-2.73z" />
                      </svg>
                    </a>
                  @endcan

                  @can('delete', $project)
                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus project ini? Aksi ini tidak bisa dibatalkan.');">
                      @csrf @method('DELETE')
                      <button type="submit" class="p-2 rounded-lg bg-white hover:bg-[#FFF2F2] border" title="Hapus Project">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M6 7h12v12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7zm3-4h6l1 1h4v2H4V4h4l1-1z"/>
                        </svg>
                      </button>
                    </form>
                  @endcan
                </div>
              </div>

              <div class="mt-4 flex justify-end">
                <button type="button"
                        class="btn-toggle-progress inline-flex items-center gap-2 rounded-xl bg-[#7A1C1C] text-white px-3 py-2 text-sm shadow"
                        data-target="progressForm-{{ $project->id }}">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z"/></svg>
                  Tambah Progress
                </button>
              </div>

              <div id="progressForm-{{ $project->id }}" class="hidden mt-3 rounded-xl bg-[#FFF8F8] p-4 border border-[#E7C9C9]">
                <div class="font-semibold mb-2">Tambah Progress untuk Project ini</div>
                <form method="POST" action="{{ route('projects.progresses.store', $project->id) }}" class="grid grid-cols-1 md:grid-cols-5 gap-2">
                  @csrf
                  <input name="name" required placeholder="Nama Progress" class="rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none md:col-span-2">
                  <input type="date" name="start_date" required class="rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
                  <input type="date" name="end_date"   required class="rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
                  <input type="number" name="desired_percent" required
                    min="1" max="100"
                    placeholder="Target %"
                    class="rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none" />

                  <button class="rounded-xl border-2 border-[#7A1C1C] bg-[#E2B9B9] px-4 py-2 font-semibold">Tambah</button>
                </form>
              </div>

              <div class="mt-4">
                <div class="scroll-thin grid md:grid-cols-2 gap-4 max-h-[280px] overflow-y-auto pr-1">
                  @forelse($project->progresses as $pr)
                    @php
                      $last = $pr->updates->sortByDesc('update_date')->first();
                      $realisasi = $last ? (int)($last->percent ?? ($last->progress_percent ?? 0)) : 0;
                      $alreadyConfirmed = !is_null($pr->confirmed_at);
                      $endDate = $pr->end_date ? \Illuminate\Support\Carbon::parse($pr->end_date)->startOfDay() : null;
                      $isOverdue = $endDate ? $endDate->lt(now()->startOfDay()) : false;
                      $isUnmet = $isOverdue && !$alreadyConfirmed && $realisasi < (int)$pr->desired_percent;
                    @endphp

                    <div class="rounded-2xl bg-[#F7E4E4] p-4 border border-[#E7C9C9]">
                      <div class="flex items-start justify-between mb-2">
                        <div class="font-semibold">
                          Progress {{ $loop->iteration }} — {{ $pr->name }}
                          @if ($isUnmet)
                            <span class="ml-2 inline-flex items-center rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-[11px] font-semibold">Tidak Memenuhi</span>
                          @endif
                        </div>

                        <div class="flex items-center gap-2">
                          {{-- Edit/Hapus progress: hanya owner progress --}}
                          @can('update', $pr)
                            <button type="button" class="px-3 py-1.5 text-xs rounded-lg border bg-white hover:bg-[#FFF2F2]"
                                    onclick="document.getElementById('editProgress-{{ $pr->id }}').classList.toggle('hidden')"
                                    @if($alreadyConfirmed) disabled title="Sudah dikonfirmasi" @endif>
                              Edit
                            </button>
                          @endcan
                          @can('delete', $pr)
                            <form method="POST" action="{{ route('progresses.destroy', $pr->id) }}" onsubmit="return confirm('Hapus progress ini?');">
                              @csrf @method('DELETE')
                              <button class="px-3 py-1.5 text-xs rounded-lg border bg-white hover:bg-[#FFF2F2]"
                                      @if($alreadyConfirmed) disabled title="Sudah dikonfirmasi" @endif>
                                Hapus
                              </button>
                            </form>
                          @endcan
                        </div>
                      </div>

                      @if ($isUnmet)
                        <div class="mb-2 text[12px] rounded-lg border border-red-300 bg-red-50 text-red-700 px-3 py-2">
                          Melewati timeline selesai, realisasi belum mencapai target & belum dikonfirmasi.
                        </div>
                      @endif

                      <div class="mt-2 text-sm">
                        <div class="grid grid-cols-[auto,1fr] gap-x-4 gap-y-1">
                          <span>Timeline Mulai</span>
                          <span>: {{ $pr->start_date ? \Illuminate\Support\Carbon::parse($pr->start_date)->timezone('Asia/Jakarta')->format('d M Y') : '-' }}</span>
                          <span>Timeline Selesai</span>
                          <span>: {{ $pr->end_date ? \Illuminate\Support\Carbon::parse($pr->end_date)->timezone('Asia/Jakarta')->format('d M Y') : '-' }}</span>
                          <span>Target Progress</span><span>: {{ (int)$pr->desired_percent }}%</span>
                          <span>Realisasi Progress</span><span>: {{ $realisasi }}%</span>
                        </div>
                      </div>

                      {{-- Form edit progress (owner saja) --}}
                      @can('update', $pr)
                        <div id="editProgress-{{ $pr->id }}" class="hidden mb-3">
                          <form method="POST" action="{{ route('progresses.update', $pr->id) }}"
                                class="grid grid-cols-1 md:grid-cols-5 gap-2 bg-white rounded-xl p-3 border">
                            @csrf @method('PUT')
                            <input name="name" value="{{ old('name', $pr->name) }}" required
                                   class="rounded-xl bg-white border px-3 py-2 outline-none md:col-span-2"
                                   placeholder="Nama progress" @if($alreadyConfirmed) disabled @endif>
                            <input type="date" name="start_date" value="{{ old('start_date', $pr->start_date) }}" required
                                   class="rounded-xl bg-white border px-3 py-2 outline-none" @if($alreadyConfirmed) disabled @endif>
                            <input type="date" name="end_date" value="{{ old('end_date', $pr->end_date) }}" required
                                   class="rounded-xl bg-white border px-3 py-2 outline-none" @if($alreadyConfirmed) disabled @endif>
                            {{-- FIX: hapus pemakaian $isOwner yang tidak didefinisikan --}}
                            <input type="number" name="desired_percent"
                              min="1" max="100"
                              value="{{ old('desired_percent', $pr->desired_percent) }}"
                              placeholder="Target %"
                              class="rounded-xl bg-white border px-3 py-2 outline-none"
                              required
                              @if($alreadyConfirmed) disabled @endif>

                            <button class="h-[40px] min-w-[140px] px-4 rounded-full border-2 border-[#7A1C1C] bg-[#E2B9B9] text-xs font-semibold" @if($alreadyConfirmed) disabled @endif>
                              Simpan Perubahan
                            </button>
                          </form>
                        </div>
                      @endcan

                      <div class="mt-3">
                        {{-- Update log progress (owner progress saja) --}}
                        @can('createUpdate', $pr)
                          @php
                            $canUpdate = !$alreadyConfirmed && !$isOverdue;
                            $updateDisabledReason =
                              $isOverdue ? 'Tidak bisa update: sudah lewat timeline selesai'
                              : ($alreadyConfirmed ? 'Sudah dikonfirmasi'
                              : '');
                          @endphp
                          <form method="POST" action="{{ route('progresses.updates.store', $pr->id) }}" class="flex flex-wrap gap-3 items-center">
                            @csrf
                            <input type="date" name="update_date" value="{{ now()->toDateString() }}"
                                   class="rounded-xl border px-3 py-2 text-sm" @unless($canUpdate) disabled @endunless>
                            <input type="number" name="percent" min="0" max="100" placeholder="%"
                                   class="rounded-xl border px-3 py-2 text-sm w-28" @unless($canUpdate) disabled @endunless>
                            <button class="rounded-xl bg-[#7A1C1C] text-white px-4 py-2 text-sm font-semibold disabled:opacity-50"
                                    @unless($canUpdate) disabled @endunless title="{{ $updateDisabledReason }}">
                              Update Progress
                            </button>
                          </form>
                        @endcan

                        {{-- Konfirmasi progress (hanya owner progress) --}}
                        @if (!$alreadyConfirmed)
                          @can('confirm', $pr)
                            @php
                              $canConfirmBase = $realisasi >= (int)$pr->desired_percent;
                            @endphp
                            <form method="POST" action="{{ route('progresses.confirm', $pr->id) }}" class="mt-2">
                              @csrf
                              <button class="rounded-xl bg-green-700 text-white px-4 py-2 text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                                      {{ $canConfirmBase ? '' : 'disabled' }}
                                      title="{{ $canConfirmBase ? '' : 'Belum mencapai target' }}">
                                Konfirmasi
                              </button>
                            </form>
                          @else
                            <div class="mt-2">
                              <button class="rounded-xl bg-gray-300 text-white px-4 py-2 text-sm font-semibold cursor-not-allowed"
                                      title="Hanya pembuat progress yang dapat konfirmasi">
                                Konfirmasi
                              </button>
                            </div>
                          @endcan
                        @else
                          <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-semibold mt-2">
                            Sudah dikonfirmasi
                          </span>
                        @endif

                        @if ($isOverdue && !$alreadyConfirmed)
                          <div class="mt-2">
                            <span class="inline-flex items-center rounded-full bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold">
                              Telat dari timeline
                            </span>
                          </div>
                        @endif
                      </div>

                      @cannot('update', $pr)
                        <p class="mt-2 text-xs text-gray-600">
                          *Progress ini dibuat oleh pengguna lain. Anda hanya dapat melihat tanpa mengubah.
                        </p>
                      @endcannot
                    </div>
                  @empty
                    <div class="col-span-2 text-sm text-gray-600">Belum ada progress.</div>
                  @endforelse
                </div>
              </div>

              <div class="mt-4 flex justify-end">
                {{-- Detail bisa arahkan ke halaman detail project milik semua role / khusus IT --}}
                <a href="{{ route('dig.projects.show', $project->id) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-[#7A1C1C] px-3 py-1.5 text-xs font-semibold text-[#7A1C1C] bg-white hover:bg-[#FFF2F2]">
                  Detail Informasi
                </a>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div> {{-- /#pageWrapper --}}

  {{-- ============== SCRIPTS ============== --}}
  <script>
    const sidebar      = document.getElementById('sidebar');
    const sidebarOpen  = document.getElementById('sidebarOpenBtn');
    const sidebarClose = document.getElementById('sidebarCloseBtn');
    const sbBackdrop   = document.getElementById('sidebarBackdrop');
    const pageWrapper  = document.getElementById('pageWrapper');
    const railLogo     = document.getElementById('railLogoBtn');
    const miniSidebar  = document.getElementById('miniSidebar');

    const SIDEBAR_OPEN_KEY = 'it.sidebar.open';
    const setPersist = (isOpen) => { try { localStorage.setItem(SIDEBAR_OPEN_KEY, isOpen ? '1' : '0'); } catch {} };
    const getPersist = () => { try { return localStorage.getItem(SIDEBAR_OPEN_KEY) === '1'; } catch { return false; } };

    const openSidebar = () => {
      sidebar.style.transform = 'none';
      miniSidebar && miniSidebar.classList.add('md:hidden');
      pageWrapper.style.marginLeft = (window.matchMedia('(min-width:768px)').matches ? '18rem' : '0');
      sbBackdrop && (sbBackdrop.classList.remove('hidden'));
      setPersist(true);
    };
    const closeSidebar = () => {
      sidebar.style.transform = 'translateX(-100%)';
      miniSidebar && miniSidebar.classList.remove('md:hidden');
      pageWrapper.style.marginLeft = (window.matchMedia('(min-width:768px)').matches ? '4rem' : '0');
      sbBackdrop && (sbBackdrop.classList.add('hidden'));
      setPersist(false);
    };

    sidebarOpen && sidebarOpen.addEventListener('click', openSidebar);
    railLogo   && railLogo.addEventListener('click', openSidebar);
    sidebarClose && sidebarClose.addEventListener('click', closeSidebar);
    sbBackdrop && sbBackdrop.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

    const syncOnResize = () => {
      const isDesktop = window.matchMedia('(min-width: 768px)').matches;
      const persistedOpen = getPersist();

      if (isDesktop) {
        if (persistedOpen) {
          sidebar.style.transform = 'none';
          miniSidebar && miniSidebar.classList.add('md:hidden');
          pageWrapper.style.marginLeft = '18rem';
          sbBackdrop && sbBackdrop.classList.add('hidden');
        } else {
          sidebar.style.transform = 'translateX(-100%)';
          miniSidebar && miniSidebar.classList.remove('md:hidden');
          pageWrapper.style.marginLeft = '4rem';
          sbBackdrop && sbBackdrop.classList.add('hidden');
        }
      } else {
        if (persistedOpen) {
          sidebar.style.transform = 'none';
          miniSidebar && miniSidebar.classList.add('md:hidden');
          pageWrapper.style.marginLeft = '0';
          sbBackdrop && sbBackdrop.classList.remove('hidden');
        } else {
          sidebar.style.transform = 'translateX(-100%)';
          miniSidebar && miniSidebar.classList.add('md:hidden');
          pageWrapper.style.marginLeft = '0';
          sbBackdrop && sbBackdrop.classList.add('hidden');
        }
      }
    };

    const reveal = () => {
      const early = document.getElementById('early-sync');
      if (early) early.remove();
      document.body.style.visibility = 'visible';
    };

    syncOnResize();
    reveal();
    window.addEventListener('resize', syncOnResize);

    // Toggle form "Tambah Progress"
    document.querySelectorAll('.btn-toggle-progress').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-target');
        document.getElementById(id)?.classList.toggle('hidden');
      });
    });


    // Dinamis Progress rows (modal)
    (function() {
      const list = document.getElementById('progressList');
      const addBtn = document.getElementById('addProgressBtn');
      if (!list || !addBtn) return;

      const renumber = () => {
        const rows = list.querySelectorAll('.progress-row');
        rows.forEach((row, i) => {
          row.querySelector('.progress-number').textContent = i + 1;
          const removeBtn = row.querySelector('.removeProgressBtn');
          if (removeBtn) removeBtn.disabled = rows.length <= 1;
        });
      };
      const reindexNames = () => {
        const rows = list.querySelectorAll('.progress-row');
        rows.forEach((row, idx) => {
          row.dataset.index = idx;
          row.querySelectorAll('input[name], select[name]').forEach(el => {
            el.name = el.name.replace(/progresses\[\d+\]/, `progresses[${idx}]`);
          });
        });
      };
      const addRow = () => {
        const currentIndex = list.querySelectorAll('.progress-row').length;
        const tpl = document.createElement('template');
        tpl.innerHTML = `
          <div class="progress-row rounded-xl bg-[#E2B9B9]/60 border border-[#C89898] p-4" data-index="${currentIndex}">
            <div class="flex items-center justify-between mb-3">
              <div class="font-semibold text-sm">Progress <span class="progress-number">${currentIndex+1}</span></div>
              <button type="button" class="removeProgressBtn text-xs px-2 py-1 rounded-lg border border-red-300 text-red-700 hover:bg-red-50">Hapus</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="text-sm font-semibold mb-1 block">Nama Progress</label>
                <input name="progresses[${currentIndex}][name]" required
                       class="w-full rounded-xl bg-white/80 border border-[#C89898] px-4 py-3 outline-none"
                       placeholder="Nama Progress" />
              </div>
              <div>
                <label class="text-sm font-semibold mb-1 block">Timeline</label>
                <div class="grid grid-cols-2 gap-2">
                  <input type="date" name="progresses[${currentIndex}][start_date]" required
                         class="rounded-xl bg-white/80 border border-[#C89898] px-4 py-3 outline-none">
                  <input type="date" name="progresses[${currentIndex}][end_date]" required
                         class="rounded-xl bg-white/80 border border-[#C89898] px-4 py-3 outline-none">
                </div>
                <label class="block text-sm font-semibold mt-3 mb-1">Target (%)</label>
                <select name="progresses[${currentIndex}][desired_percent]" required
                        class="w-full rounded-xl bg-white/80 border border-[#C89898] px-4 py-3 outline-none cursor-pointer">
                  ${Array.from({length:21},(_,i)=>i*5).map(v=>`<option value="${v}">${v}%</option>`).join('')}
                </select>
              </div>
            </div>
          </div>
        `.trim();
        const row = tpl.content.firstChild;
        row.querySelector('.removeProgressBtn').addEventListener('click', () => { row.remove(); renumber(); reindexNames(); });
        list.appendChild(row);
        renumber();
        reindexNames();
      };
      addBtn.addEventListener('click', addRow);

      const firstRemove = list.querySelector('.removeProgressBtn');
      if (firstRemove) {
        firstRemove.addEventListener('click', (e) => {
          const rows = list.querySelectorAll('.progress-row');
          if (rows.length > 1) { e.currentTarget.closest('.progress-row').remove(); renumber(); reindexNames(); }
        });
      }
    })();

    // ===== Lampiran: Modal create =====
    (function() {
      const input = document.getElementById('createAttachmentInput');
      const btn   = document.getElementById('createAttachmentBtn');
      const list  = document.getElementById('createAttachmentList');
      if (!input || !btn || !list) return;

      btn.addEventListener('click', () => input.click());
      input.addEventListener('change', () => {
        list.innerHTML = '';
        Array.from(input.files || []).forEach(file => {
          const li = document.createElement('li');
          li.className = 'flex items-center justify-between rounded-lg border border-[#E7C9C9] bg-[#FFF7F7] px-3 py-2';
          li.innerHTML = `
            <span class="text-xs truncate max-w-[200px]">${file.name}</span>
            <span class="text-[11px] text-gray-500">${(file.size/1024).toFixed(1)} KB</span>
          `;
          list.appendChild(li);
        });
      });
    })();

    // ===== Lampiran: Edit inline per project =====
    (function() {
      document.querySelectorAll('.addAttachmentBtn').forEach(btn => {
        const projectId = btn.getAttribute('data-id');
        const input = document.getElementById('attachmentInput-' + projectId);
        const list  = document.getElementById('attachmentList-' + projectId);
        if (!input || !list) return;

        btn.addEventListener('click', () => input.click());
        input.addEventListener('change', () => {
          list.innerHTML = '';
          Array.from(input.files || []).forEach(file => {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between rounded-lg border border-[#E7C9C9] bg-[#FFF7F7] px-3 py-2';
            li.innerHTML = `
              <span class="text-xs truncate max-w-[200px]">${file.name}</span>
              <span class="text-[11px] text-gray-500">${(file.size/1024).toFixed(1)} KB</span>
            `;
            list.appendChild(li);
          });
        });
      });
    })();
  </script>
</body>
</html>
