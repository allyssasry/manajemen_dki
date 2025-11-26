<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Notifikasi | IT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    html { scrollbar-gutter: stable; }
    body { overflow-x: hidden; }
    .scroll-thin::-webkit-scrollbar { width: 6px; }
    .scroll-thin::-webkit-scrollbar-thumb { background: #c89898; border-radius: 9999px; }
    .scroll-thin::-webkit-scrollbar-track { background: transparent; }
    .no-transition, .no-transition * { transition: none !important; }
  </style>
</head>
<body class="min-h-screen bg-[#FFFAFA] text-gray-900">

@php
  /* ========= USER, AVATAR, ROLE & RUTE DINAMIS ========= */
  $me   = $me ?? auth()->user()?->fresh();
  $role = $me?->role;
  $roleLabel = $role === 'it' ? 'IT' : ($role === 'digital_banking' ? 'DIG' : ($role === 'supervisor' ? 'Supervisor' : 'User'));

  // Avatar fallback
  $initial = urlencode(mb_substr($me?->name ?? $me?->username ?? 'U', 0, 1));
  $fallbackSvg = "data:image/svg+xml;utf8,".rawurlencode(
    '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">'.
    '<rect width="100%" height="100%" rx="12" ry="12" fill="#7A1C1C"/>'.
    '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="28" fill="#fff">'.$initial.'</text>'.
    '</svg>'
  );
  $rawUrl     = $me?->avatar_url_public;
  $extraKey   = $me?->avatar_cache_key ?? ($me?->updated_at?->timestamp ?? time());
  $avatarUrl  = $rawUrl ? ($rawUrl.(str_contains($rawUrl,'?') ? '&' : '?').'ck='.$extraKey) : $fallbackSvg;

  // Dashboard per role (home)
  $homeRouteName = match ($role) {
      'it'              => (\Route::has('it.dashboard') ? 'it.dashboard' : null),
      'supervisor'      => (\Route::has('supervisor.dashboard') ? 'supervisor.dashboard' : null),
      'digital_banking' => (\Route::has('dig.dashboard') ? 'dig.dashboard' : null),
      default           => (\Route::has('dig.dashboard') ? 'dig.dashboard' : null),
  };
  $homeUrl = $homeRouteName ? route($homeRouteName) : url('/');
  $isDashboardActive = $homeRouteName ? request()->routeIs($homeRouteName) : (url()->current() === $homeUrl);

  // Notifikasi per role
  $notifRoute = match ($role) {
      'it'              => 'it.notifications',
      'supervisor'      => 'supervisor.notifications',
      default           => 'dig.notifications',
  };
@endphp

{{-- ============== SIDEBAR MINI (RAIL) ============== --}}
@php $iconColor = '#7A1C1C'; @endphp
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
<div id="pageWrapper" class="transition-all duration-300 ml-0">

  {{-- NAVBAR --}}
  <header class="sticky top-0 z-30 bg-[#8D2121] backdrop-blur border-b">
    <div class="max-w-6xl mx-auto px-5 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-lg md:text-xl font-semibold text-white select-none">Notifikasi</span>
      </div>

      <div class="hidden md:flex items-center gap-3 pl-4 border-l border-white/30">
        <img src="{{ $avatarUrl }}" class="w-8 h-8 rounded-full object-cover bg-white" alt="Avatar"
             loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='{{ $fallbackSvg }}';">
        <div class="leading-tight">
          <div class="text-[13px] font-semibold text-white truncate max-w-[140px]">{{ $me?->name ?? ($me?->username ?? 'User') }}</div>
          <div class="text-[11px] text-white font-medium">{{ $roleLabel }}</div>
        </div>
      </div>
    </div>
  </header>

  {{-- HEADER LIST (BADGE TOTAL IT UNREAD) --}}
  <div class="max-w-5xl mx-auto px-5 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
    </div>
    <form method="POST" action="{{ route('it.notifications.readAll') }}">
      @csrf
      <button class="text-sm rounded-lg border px-3 py-1 bg-white hover:bg-red-50 border-red-200 text-[#7A1C1C]">
        Tandai semua terbaca
      </button>
    </form>
  </div>

  {{-- LIST NOTIFIKASI --}}
  <main class="max-w-5xl mx-auto px-5 py-6">
    @php
      $todayCollection = ($today ?? collect());
      $todayUnread = $todayCollection->whereNull('read_at')->count();

      // semua item "sebelum hari ini" (sudah difilter 7 hari terakhir di controller)
      $allItems = isset($notifications)
        ? collect($notifications->items() ?? [])
        : collect();

      // reject yang sudah muncul di "Hari Ini"
      $previous = $allItems->reject(function($n) use ($todayCollection) {
        return $todayCollection->contains('id', $n->id);
      });

      // flag: apakah ADA notifikasi sama sekali?
      $hasAny = $todayCollection->count() > 0 || $previous->count() > 0;
    @endphp

    {{-- KALAU BENAR-BENAR TIDAK ADA NOTIFIKASI --}}
    @if(!$hasAny)
      <div class="py-12 text-center text-sm text-gray-600">
        Belum ada notifikasi.
      </div>
    @else
      {{-- HARI INI --}}
      @if($todayCollection->count() > 0)
        <div>
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold">Hari Ini</h2>
            @if($todayUnread > 0)
              <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 rounded-full bg-[#7A1C1C] text-white text-xs px-2">
                {{ $todayUnread > 99 ? '99+' : $todayUnread }}
              </span>
            @endif
          </div>

          <div class="space-y-3">
            @forelse($todayCollection as $n)
              @php
                $d        = $n->data ?? [];
                $type     = strtolower($d['type'] ?? '');
                $pName    = $d['project_name'] ?? 'Project';
                $pId      = $d['project_id']   ?? null;
                $message  = $d['message']      ?? '';
                $decision = strtolower($d['decision'] ?? '');
                $isUnread = is_null($n->read_at);

                $created  = optional($n->created_at)->timezone('Asia/Jakarta');
                $dateText = $created ? $created->format('d M Y') : '-';
                $timeText = $created ? $created->format('H.i')   : '-';
              @endphp

              <div class="rounded-xl px-5 py-4 border {{ $isUnread ? 'border-[#7A1C1C] bg-[#F2DCDC]' : 'border-[#E7C9C9] bg-white' }}">
                <div class="flex items-start justify-between gap-4">
                  <div class="min-w-0">
                    @if($type === 'dig_project_created')
                      <div class="text-[15px] font-semibold">Project Baru Dibuat</div>
                      <div class="mt-1 text-sm">
                        <div><span class="font-semibold">Nama Project</span>: {{ $pName }}</div>
                        <div class="mt-1"><span class="font-semibold">Tanggal</span>: {{ $dateText }} • {{ $timeText }} WIB</div>
                        @if($message) <div class="text-gray-700 mt-1">{{ $message }}</div> @endif
                      </div>
                    @elseif($type === 'dig_completion_decision')
                      @php
                        $statusLabel = $d['status_label'] ?? ($decision === 'memenuhi' ? 'Project Selesai, Memenuhi' : 'Project Selesai, Tidak Memenuhi');
                        $isMeet = ($decision === 'memenuhi');
                      @endphp
                      <div class="text-[15px] font-semibold">Keputusan Penyelesaian Project</div>
                      <div class="mt-1 text-sm">
                        <div><span class="font-semibold">Nama Project</span>: {{ $pName }}</div>
                        <div class="mt-1">
                          <span class="font-semibold">Status</span>:
                          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $isMeet ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $statusLabel }}
                          </span>
                        </div>
                        <div class="mt-1"><span class="font-semibold">Tanggal</span>: {{ $dateText }} • {{ $timeText }} WIB</div>
                        @if($message) <div class="text-gray-700 mt-1">{{ $message }}</div> @endif
                      </div>
                    @else
                      <div class="text-[15px] font-semibold">Notifikasi</div>
                      <div class="mt-1 text-sm">
                        <div class="text-gray-700">{{ $message ?: 'Ada pembaruan.' }}</div>
                        <div class="mt-1 text-xs text-gray-600">{{ $dateText }} • {{ $timeText }} WIB</div>
                      </div>
                    @endif
                  </div>

                  <div class="text-right shrink-0">
                    <div class="text-xs text-gray-600">{{ $timeText }}</div>
                    <div class="mt-2 flex items-center gap-2 justify-end">
                      @if($pId)
                        <a href="{{ route('dig.projects.show', $pId) }}" class="text-xs underline text-[#7A1C1C]">Lihat Project</a>
                      @endif
                      @if($isUnread)
                        <form method="POST" action="{{ route('it.notifications.read', $n->id) }}">
                          @csrf
                          <button class="text-xs underline text-[#7A1C1C]">Tandai terbaca</button>
                        </form>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @empty
              {{-- seharusnya tidak masuk ke sini karena $todayCollection->count() > 0 --}}
            @endforelse
          </div>
        </div>
      @endif

      {{-- RIWAYAT (DIBAGI PER TANGGAL, MAKS 7 HARI TERAKHIR) --}}
      @php
        $groupedByDate = $previous->groupBy(function($n) {
          $c = optional($n->created_at)->timezone('Asia/Jakarta');
          return $c ? $c->format('d M Y') : '-';
        });
      @endphp

      @if($groupedByDate->count() > 0)
        <div class="mt-8 space-y-6">
          @foreach($groupedByDate as $dateLabel => $items)
            {{-- Judul per tanggal --}}
            <div>
              <div class="mb-2 flex items-center justify-between">
                <h2 class="text-base font-semibold">{{ $dateLabel }}</h2>
              </div>

              <div class="space-y-3">
                @foreach($items as $n)
                  @php
                    $d        = $n->data ?? [];
                    $type     = strtolower($d['type'] ?? '');
                    $pName    = $d['project_name'] ?? 'Project';
                    $pId      = $d['project_id']   ?? null;
                    $message  = $d['message']      ?? '';
                    $decision = strtolower($d['decision'] ?? '');
                    $isUnread = is_null($n->read_at);

                    $created  = optional($n->created_at)->timezone('Asia/Jakarta');
                    $timeText = $created ? $created->format('H.i')   : '-';
                  @endphp

                  <div class="rounded-xl px-5 py-4 border {{ $isUnread ? 'border-[#7A1C1C] bg-[#FDF3F3]' : 'border-[#E7C9C9] bg-white' }}">
                    <div class="flex items-start justify-between gap-4">
                      <div class="min-w-0">
                        @if($type === 'dig_project_created')
                          <div class="text-[15px] font-semibold">Project Baru Dibuat</div>
                          <div class="mt-1 text-sm">
                            <div><span class="font-semibold">Nama Project</span>: {{ $pName }}</div>
                            @if($message) <div class="text-gray-700 mt-1">{{ $message }}</div> @endif
                          </div>
                        @elseif($type === 'dig_completion_decision')
                          @php
                            $statusLabel = $d['status_label'] ?? ($decision === 'memenuhi' ? 'Project Selesai, Memenuhi' : 'Project Selesai, Tidak Memenuhi');
                            $isMeet = ($decision === 'memenuhi');
                          @endphp
                          <div class="text-[15px] font-semibold">Keputusan Penyelesaian Project</div>
                          <div class="mt-1 text-sm">
                            <div><span class="font-semibold">Nama Project</span>: {{ $pName }}</div>
                            <div class="mt-1">
                              <span class="font-semibold">Status</span>:
                              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $isMeet ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $statusLabel }}
                              </span>
                            </div>
                            @if($message) <div class="text-gray-700 mt-1">{{ $message }}</div> @endif
                          </div>
                        @else
                          <div class="text-[15px] font-semibold">Notifikasi</div>
                          <div class="mt-1 text-sm">
                            <div class="text-gray-700">{{ $message ?: 'Ada pembaruan.' }}</div>
                          </div>
                        @endif
                      </div>

                      <div class="text-right shrink-0">
                        <div class="text-xs text-gray-600">{{ $timeText }}</div>
                        <div class="mt-2 flex items-center gap-2 justify-end">
                          @if($pId)
                            <a href="{{ route('dig.projects.show', $pId) }}" class="text-xs underline text-[#7A1C1C]">Lihat Project</a>
                          @endif
                          @if($isUnread)
                            <form method="POST" action="{{ route('it.notifications.read', $n->id) }}">
                              @csrf
                              <button class="text-xs underline text-[#7A1C1C]">Tandai terbaca</button>
                            </form>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      @endif

      {{-- PAGINATION (kalau mau tetap pakai) --}}
      @if(isset($notifications) && $notifications->lastPage() > 1)
        <div class="mt-6">
          {{ $notifications->links() }}
        </div>
      @endif
    @endif
  </main>
</div> {{-- /pageWrapper --}}

<script>
  const sidebar      = document.getElementById('sidebar');
  const sidebarClose = document.getElementById('sidebarCloseBtn');
  const sbBackdrop   = document.getElementById('sidebarBackdrop');
  const pageWrapper  = document.getElementById('pageWrapper');
  const railLogo     = document.getElementById('railLogoBtn');

  const add = (el, ...cls) => el && el.classList.add(...cls);
  const rm  = (el, ...cls) => el && el.classList.remove(...cls);

  const SIDEBAR_OPEN_KEY = 'it.sidebar.open';
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

  railLogo && railLogo.addEventListener('click', openSidebar);
  sidebarClose && sidebarClose.addEventListener('click', closeSidebar);
  sbBackdrop && sbBackdrop.addEventListener('click', closeSidebar);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

  const syncOnResize = () => {
    const isDesktop = window.matchMedia('(min-width: 768px)').matches;
    const persistedOpen = getPersist();

    if (isDesktop) {
      if (persistedOpen) {
        rm(sidebar, '-translate-x-full');
        add(pageWrapper, 'ml-72');
        rm(sbBackdrop, 'hidden');
      } else {
        add(sidebar, '-translate-x-full');
        rm(pageWrapper, 'ml-72');
        add(sbBackdrop, 'hidden');
      }
    } else {
      if (persistedOpen) {
        rm(sidebar, '-translate-x-full');
        rm(pageWrapper, 'ml-72');
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
