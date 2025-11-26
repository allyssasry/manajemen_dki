{{-- resources/views/arsip/arsip.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Arsip Project</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Konsistensi UI */
    html { scrollbar-gutter: stable; }
    body { overflow-x: hidden; }

    /* Scrollbar (samakan dengan DIG dashboard) */
    .scroll-thin::-webkit-scrollbar{ width:6px; height:6px }
    .scroll-thin::-webkit-scrollbar-thumb{ background:#c89898; border-radius:9999px }
    .scroll-thin::-webkit-scrollbar-track{ background:transparent }

    /* Utility untuk matiin transisi sementara (persis seperti Notifikasi IT) */
    .no-transition,
    .no-transition * {
      transition: none !important;
    }
  </style>
</head>
<body class="min-h-screen bg-white text-gray-900">

@php
  /** ================== DATA USER & URL BERDASAR ROLE ================== */
  $user = auth()->user();
  $role = $user?->role; // 'digital_banking' | 'it' | 'supervisor' | 'kepala_divisi'
  $roleLabel = $role === 'it'
    ? 'IT'
    : ($role === 'digital_banking'
        ? 'DIG'
        : ($role === 'kepala_divisi' ? 'Kepala Divisi' : 'User'));

  // Dashboard per role
  if ($role === 'it' && \Illuminate\Support\Facades\Route::has('it.dashboard')) {
    $homeUrl = route('it.dashboard');
  } elseif ($role === 'kepala_divisi' && \Illuminate\Support\Facades\Route::has('kd.dashboard')) {
    $homeUrl = route('kd.dashboard');
  } elseif (\Illuminate\Support\Facades\Route::has('dig.dashboard')) {
    $homeUrl = route('dig.dashboard');
  } else {
    $homeUrl = url('/');
  }

  // Progress per role (fallback ke semua.progresses)
  if ($role === 'it' && \Illuminate\Support\Facades\Route::has('it.progresses')) {
    $progressUrl = route('it.progresses');
  } elseif ($role === 'kepala_divisi' && \Illuminate\Support\Facades\Route::has('kd.progresses')) {
    $progressUrl = route('kd.progresses');
  } elseif (\Illuminate\Support\Facades\Route::has('semua.progresses')) {
    $progressUrl = route('semua.progresses');
  } else {
    $progressUrl = url()->current();
  }

  // Notifications per role
  if ($role === 'it' && \Illuminate\Support\Facades\Route::has('it.notifications')) {
    $notifUrl = route('it.notifications');
  } elseif ($role === 'kepala_divisi' && \Illuminate\Support\Facades\Route::has('kd.notifications')) {
    $notifUrl = route('kd.notifications');
  } elseif (\Illuminate\Support\Facades\Route::has('dig.notifications')) {
    $notifUrl = route('dig.notifications');
  } else {
    $notifUrl = url()->current();
  }

  // Arsip (umum)
  if (\Illuminate\Support\Facades\Route::has('semua.arsip')) {
    $arsipUrl = route('semua.arsip');
  } elseif (\Illuminate\Support\Facades\Route::has('arsip.arsip')) {
    $arsipUrl = route('arsip.arsip');
  } else {
    $arsipUrl = url()->current();
  }

  // Helper active berdasar URL
  $isDashboardActive = url()->current() === $homeUrl;
  $isProgressActive  = url()->current() === $progressUrl;
  $isNotifActive     = url()->current() === $notifUrl;
  $isArsipActive     = url()->current() === $arsipUrl;

  // Avatar
  $me   = $user?->fresh();
  $initial = urlencode(mb_substr($me?->name ?? $me?->username ?? 'U', 0, 1));
  $fallbackSvg = "data:image/svg+xml;utf8,".rawurlencode(
    '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">'.
    '<rect width="100%" height="100%" rx="12" ry="12" fill="#7A1C1C"/>'.
    '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="28" fill="#fff">'.$initial.'</text>'.
    '</svg>'
  );
  $rawUrl   = $me?->avatar_url_public;
  $extraKey = $me?->avatar_cache_key ?? ($me?->updated_at?->timestamp ?? time());
  $avatarUrl= $rawUrl ? ($rawUrl.(str_contains($rawUrl,'?') ? '&' : '?').'ck='.$extraKey) : $fallbackSvg;

  // Container lebar konten
  $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';

  // Setting URL
  $settingUrl = \Illuminate\Support\Facades\Route::has('account.setting')
    ? route('account.setting')
    : url()->current();
  $isSettingActive = url()->current() === $settingUrl;
@endphp

{{-- ================== MINI SIDEBAR (RAIL) ================== --}}
<aside id="miniSidebar"
       class="hidden md:flex fixed inset-y-0 left-0 z-40 w-16 bg-white border-r shadow-xl flex-col items-center justify-between py-4">

  <div class="flex flex-col items-center gap-6">
    {{-- Logo / tombol buka sidebar penuh --}}
    <button id="railLogoBtn" type="button" title="Buka Sidebar" aria-label="Buka Sidebar"
            class="rounded-xl p-2 hover:bg-[#FFF2F2] cursor-pointer">
      <img src="{{ asset('images/dki.png') }}" class="h-6 w-auto object-contain" alt="Logo" />
    </button>

    {{-- DASHBOARD --}}
    <a href="{{ $homeUrl }}"
       title="Dashboard" aria-label="Dashboard"
       class="p-2 rounded-lg {{ $isDashboardActive ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
           fill="{{ $isDashboardActive ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
      </svg>
    </a>

    {{-- PROGRESS --}}
    <a href="{{ $progressUrl }}"
       title="Progress" aria-label="Progress"
       class="p-2 rounded-lg {{ $isProgressActive ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
           fill="{{ $isProgressActive ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
        <path d="M4 22h16V2H4v20zm3-5h2v3H7v-3zm4-7h2v10h-2V10zm4 3h2v7h-2v-7z" />
      </svg>
    </a>

    {{-- NOTIFIKASI --}}
    <a href="{{ $notifUrl }}"
       title="Notifikasi" aria-label="Notifikasi"
       class="p-2 rounded-lg {{ $isNotifActive ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
           fill="{{ $isNotifActive ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
        <path d="M12 24a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z"/>
      </svg>
    </a>

    {{-- ARSIP --}}
    <a href="{{ $arsipUrl }}"
       title="Arsip" aria-label="Arsip"
       class="p-2 rounded-lg {{ $isArsipActive ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
           fill="{{ $isArsipActive ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
        <path d="M3 3h18v4H3V3zm1 4h16v14H4V7zm2 2v10h12V9H6zm3 3h6v2H9v-2z" />
      </svg>
    </a>
  </div>

  <div class="flex flex-col items-center gap-4">
    {{-- PENGATURAN AKUN --}}
    <a href="{{ $settingUrl }}"
       title="Pengaturan Akun" aria-label="Pengaturan Akun"
       class="p-2 rounded-lg {{ $isSettingActive ? 'bg-[#FFF2F2]' : 'hover:bg-[#FFF2F2]' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" viewBox="0 0 24 24" fill="currentColor">
        <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1a8.523 8.523 0 0 0-1.874-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"/>
      </svg>
    </a>

    {{-- LOG OUT --}}
    <a href="/logout" class="p-2 rounded-lg hover:bg-[#FFF2F2]" title="Log Out" aria-label="Log Out">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="black">
        <path d="M3 3h10a1 1 0 0 1 1 1v5h-2V5H5v14h7v-4h2v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/>
        <path d="M14 12l5-5v3h4v4h-4v3l-5-5z"/>
      </svg>
    </a>
  </div>
</aside>

{{-- ===== BACKDROP UNTUK SIDEBAR (MOBILE) ===== --}}
<div id="sidebarBackdrop" class="hidden fixed inset-0 z-40 bg-black/30 md:hidden"></div>

{{-- ============== SIDEBAR PENUH (DRAWER) ============== --}}
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
    <a href="{{ $homeUrl }}"
       class="flex items-center gap-3 px-5 py-2.5 transition-all duration-150 rounded-xl
              {{ $isDashboardActive ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
           fill="{{ $isDashboardActive ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
      </svg>
      <span>Dashboard</span>
    </a>

    {{-- PROGRESS --}}
    <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Project</div>
    <a href="{{ $progressUrl }}"
       class="flex items-center gap-3 px-5 py-2.5 transition-all duration-150 rounded-xl
              {{ $isProgressActive ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
           fill="{{ $isProgressActive ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
        <path d="M4 22h16V2H4v20zm3-5h2v3H7v-3zm4-7h2v10h-2V10zm4 3h2v7h-2v-7z" />
      </svg>
      <span>Project</span>
    </a>

    {{-- NOTIFIKASI --}}
    <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Notifikasi</div>
    <a href="{{ $notifUrl }}"
       class="flex items-center gap-3 px-5 py-2.5 transition-all duration-150 rounded-xl
              {{ $isNotifActive ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
           fill="{{ $isNotifActive ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
        <path d="M12 24a2.5 2.5 0 0 0 2.45-2H9.55A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z"/>
      </svg>
      <span>Notifikasi</span>
    </a>

    {{-- ARSIP --}}
    <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Arsip</div>
    <a href="{{ $arsipUrl }}"
       class="flex items-center gap-3 px-5 py-2.5 transition-all duration-150 rounded-xl
              {{ $isArsipActive ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
           fill="{{ $isArsipActive ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
        <path d="M3 3h18v4H3V3zm1 4h16v14H4V7zm2 2v10h12V9H6zm3 3h6v2H9v-2z" />
      </svg>
      <span>Arsip</span>
    </a>
  </nav>

  {{-- FOOTER MENU --}}
  <div class="mt-6 mb-9 px-3 space-y-1 text-sm text-gray-900">
    <a href="{{ $settingUrl }}"
       class="flex items-center gap-3 px-3 py-2 rounded-xl transition
              {{ $isSettingActive ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2]' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" viewBox="0 0 24 24" fill="currentColor">
        <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1a8.523 8.523 0 0 0-1.874-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"/>
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


{{-- ================== WRAPPER & HEADER ================== --}}
<div id="pageWrapper" class="transition-all duration-300 ml-0">
  <header class="sticky top-0 z-30 bg-[#8D2121] backdrop-blur">
    <div class="{{ $container }} py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        {{-- Tombol buka (mobile) --}}
        <button id="sidebarOpenBtn"
                class="p-2 rounded-xl border border-red-200 text-red-700 hover:bg-red-50 md:hidden"
                title="Buka Sidebar" aria-label="Buka Sidebar">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 3h18v2H3V3zm0 6h18v2H3V9zm0 6h18v2H3v-2z" />
          </svg>
        </button>
        <span class="text-lg md:text-xl font-bold text-white select-none">Arsip</span>
      </div>

      <div class="hidden md:flex items-center gap-3 pl-4 border-l border-white/30">
        <img src="{{ $avatarUrl }}" class="w-8 h-8 rounded-full object-cover bg-white" alt="Avatar"
             loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='{{ $fallbackSvg }}';">
        <div class="leading-tight hidden md:block">
          <div class="text-[13px] font-semibold text-white truncate max-w-[140px]">{{ $me?->name ?? ($me?->username ?? 'User') }}</div>
          <div class="text-[11px] text-white font-light">{{ $roleLabel }}</div>
        </div>
      </div>
    </div>
  </header>

  {{-- ================== KONTEN UTAMA ================== --}}
  <main class="{{ $container }} py-6">
    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap items-end gap-3 border-b pb-4">
      <div>
        <label class="text-xs text-gray-600">Kata Kunci</label>
        <input type="text" name="q" value="{{ request('q') }}"
               class="block w-[220px] rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none"
               placeholder="Cari nama/deskripsi project">
      </div>
      <div>
        <label class="text-xs text-gray-600">Tanggal Mulai</label>
        <input type="date" name="from" value="{{ request('from') }}"
               class="block rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
      </div>
      <div>
        <label class="text-xs text-gray-600">Tanggal Selesai</label>
        <input type="date" name="to" value="{{ request('to') }}"
               class="block rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
      </div>
      <div>
        <label class="text-xs text-gray-600">Urutkan</label>
        <select name="sort" class="block rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
          <option value="finished_desc" @selected(request('sort','finished_desc')==='finished_desc')>Terbaru selesai</option>
          <option value="finished_asc"  @selected(request('sort')==='finished_asc')>Terlama selesai</option>
          <option value="name_asc"      @selected(request('sort')==='name_asc')>Nama A-Z</option>
          <option value="name_desc"     @selected(request('sort')==='name_desc')>Nama Z-A</option>
        </select>
      </div>
      <button class="h-9 px-5 rounded-[12px] text-sm font-semibold border-2 border-[#7A1C1C]  bg-[#FFF7F7] hover:bg-[#8D2121]/10">
        Terapkan
      </button>
    </form>

    {{-- ===== LIST ARSIP ===== --}}
    @forelse ($projects as $project)
      @php
        // Ambil realisasi terbaru per progress
        $latestPercents = [];
        foreach ($project->progresses as $pr) {
          $last = $pr->updates->first() ?: $pr->updates->sortByDesc('update_date')->first();
          $latestPercents[] = $last ? (int) ($last->progress_percent ?? ($last->percent ?? 0)) : 0;
        }
        $realization = count($latestPercents) ? (int) round(array_sum($latestPercents) / max(count($latestPercents), 1)) : 0;

        // Ring
        $size=88; $stroke=10; $r=$size/2-$stroke; $circ=2*M_PI*$r; $off=$circ*(1-$realization/100);

        // Status final
        $isMeet  = (bool) $project->meets_requirement;
        $statusText  = $isMeet ? 'Project Selesai, Memenuhi' : 'Project Selesai, Tidak Memenuhi';
        $statusColor = $isMeet ? '#166534' : '#7A1C1C';
        $statusBg    = $isMeet ? '#DCFCE7' : '#FEE2E2';

        $finishedAt = $project->completed_at ?? ($project->finished_at_calc ?? $project->updated_at);
      @endphp

      <section class="mt-6 rounded-2xl border-2 border-[#7A1C1C] bg-[#F2DCDC] p-5">
        <div class="grid md:grid-cols-[auto,1fr,auto] items-start gap-4">
          {{-- Chip status --}}
          <div class="text-xs font-semibold">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                  style="color: {{ $statusColor }}; background-color: {{ $statusBg }};">
              {{ $statusText }}
            </span>
          </div>

          {{-- Ring + Info --}}
          <div class="flex items-center gap-5">
            <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}">
              <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}" stroke="#E9D0D0" stroke-width="{{ $stroke }}" fill="none" opacity=".9"/>
              <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}" stroke="#7A1C1C" stroke-width="{{ $stroke }}"
                      stroke-linecap="round" stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $off }}"
                      transform="rotate(-90 {{ $size/2 }} {{ $size/2 }})" fill="none"/>
              <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="16" font-weight="700" fill="#7A1C1C">{{ $realization }}%</text>
            </svg>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-1 text-sm">
              <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                <span class="text-gray-600">Nama Project</span><span>:</span>
                <span class="font-semibold">{{ $project->name }}</span>
              </div>
              <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                <span class="text-gray-600">Penanggung Jawab (DIG)</span><span>:</span>
                <span>{{ $project->digitalBanking->name ?? '-' }}</span>
              </div>
              <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                <span class="text-gray-600">Penanggung Jawab (IT)</span><span>:</span>
                <span>{{ $project->developer->name ?? '-' }}</span>
              </div>
              <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                <span class="text-gray-600">Deskripsi</span><span>:</span>
                <span>{{ $project->description ?: '-' }}</span>
              </div>
            </div>
          </div>
        </div>

        {{-- List Progress --}}
        <div class="mt-4">
          <div class="scroll-thin grid md:grid-cols-2 gap-4 max-h-[280px] overflow-y-auto pr-1">
            @forelse($project->progresses as $idx => $pr)
              @php
                $last = $pr->updates->sortByDesc('update_date')->first();
                $realisasi = $last ? (int)($last->percent ?? $last->progress_percent ?? 0) : 0;
              @endphp

              <div class="rounded-2xl bg-[#E6CACA] p-4">
                <div class="font-semibold mb-2">Progress {{ $idx+1 }}{{ $pr->name ? ' — '.$pr->name : '' }}</div>

                <div class="text-sm">
                  <div class="grid grid-cols-[auto,1fr] gap-x-4 gap-y-1">
                    <span>Timeline Mulai</span>
                    <span>: {{ $pr->start_date ? \Illuminate\Support\Carbon::parse($pr->start_date)->timezone('Asia/Jakarta')->format('d M Y') : '-' }}</span>

                    <span>Timeline Selesai</span>
                    <span>: {{ $pr->end_date ? \Illuminate\Support\Carbon::parse($pr->end_date)->timezone('Asia/Jakarta')->format('d M Y') : '-' }}</span>

                    <span>Target Progress</span>    <span>: {{ (int)$pr->desired_percent }}%</span>
                    <span>Realisasi Progress</span> <span>: {{ $realisasi }}%</span>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-span-2 text-sm text-gray-600">Tidak ada progress.</div>
            @endforelse
          </div>
        </div>

        <div class="mt-4 flex justify-end">
          <a href="{{ route('dig.projects.show', $project->id) }}"
            class="inline-flex items-center gap-2 rounded-lg border border-[#7A1C1C] px-3 py-1.5 text-xs font-semibold text-[#7A1C1C] bg-white hover:bg-[#FFF2F2]">
            Detail Informasi
          </a>
        </div>
      </section>
    @empty
      <div class="mt-6">
        <div class="bg-[#EBD0D0] rounded-2xl px-6 py-8 flex items-center justify-center">
          <div class="rounded-2xl bg-[#CFA8A8] px-5 py-3 text-white/95">Belum ada project yang diarsipkan.</div>
        </div>
      </div>
    @endforelse

    {{-- Pagination (opsional) --}}
    @if(method_exists($projects,'links'))
      <div class="mt-6">{{ $projects->withQueryString()->links() }}</div>
    @endif
  </main>
</div> {{-- /#pageWrapper --}}

{{-- ================== SCRIPT (SAMAKAN DENGAN NOTIF IT) ================== --}}
<script>
  const sidebar      = document.getElementById('sidebar');
  const sidebarClose = document.getElementById('sidebarCloseBtn');
  const sbBackdrop   = document.getElementById('sidebarBackdrop');
  const pageWrapper  = document.getElementById('pageWrapper');
  const railLogo     = document.getElementById('railLogoBtn');
  const sidebarOpen  = document.getElementById('sidebarOpenBtn');

  const add = (el, ...cls) => el && el.classList.add(...cls);
  const rm  = (el, ...cls) => el && el.classList.remove(...cls);

  // Key localStorage (biar konsisten dengan halaman lain di DIG)
  const SIDEBAR_OPEN_KEY = 'dig.sidebar.open';

  const setPersist = (isOpen) => {
    try { localStorage.setItem(SIDEBAR_OPEN_KEY, isOpen ? '1' : '0'); } catch {}
  };
  const getPersist = () => {
    try { return localStorage.getItem(SIDEBAR_OPEN_KEY) === '1'; } catch { return false; }
  };

  const openSidebar = () => {
    rm(sidebar, '-translate-x-full');
    add(pageWrapper, 'ml-72');
    rm(sbBackdrop, 'hidden');
    setPersist(true);
  };

  const closeSidebar = () => {
    add(sidebar, '-translate-x-full');
    rm(pageWrapper, 'ml-72');
    add(sbBackdrop, 'hidden');
    setPersist(false);
  };

  railLogo    && railLogo.addEventListener('click', openSidebar);
  sidebarOpen && sidebarOpen.addEventListener('click', openSidebar);
  sidebarClose&& sidebarClose.addEventListener('click', closeSidebar);
  sbBackdrop  && sbBackdrop.addEventListener('click', closeSidebar);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

  const syncOnResize = () => {
    const isDesktop = window.matchMedia('(min-width: 768px)').matches;
    const persistedOpen = getPersist();

    if (isDesktop) {
      if (persistedOpen) {
        rm(sidebar, '-translate-x-full');
        add(pageWrapper, 'ml-72');
        add(sbBackdrop, 'hidden'); // desktop: biasanya tanpa backdrop
      } else {
        add(sidebar, '-translate-x-full');
        rm(pageWrapper, 'ml-72');
        add(sbBackdrop, 'hidden');
      }
    } else {
      if (persistedOpen) {
        rm(sidebar, '-translate-x-full');
        rm(pageWrapper, 'ml-72'); // mobile: konten full width
        rm(sbBackdrop, 'hidden');
      } else {
        add(sidebar, '-translate-x-full');
        rm(pageWrapper, 'ml-72');
        add(sbBackdrop, 'hidden');
      }
    }
  };

  const firstPaint = () => {
    add(pageWrapper, 'no-transition');
    add(sidebar, 'no-transition');

    syncOnResize();

    requestAnimationFrame(() => {
      rm(pageWrapper, 'no-transition');
      rm(sidebar, 'no-transition');
    });
  };

  window.addEventListener('resize', syncOnResize);
  firstPaint();
</script>
</body>
</html>
