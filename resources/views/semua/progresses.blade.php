{{-- resources/views/semua/progresses.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Project</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    html { scrollbar-gutter: stable; }
    body { overflow-x: hidden; }

    .scroll-thin::-webkit-scrollbar{width:6px;height:6px}
    .scroll-thin::-webkit-scrollbar-thumb{background:#c89898;border-radius:9999px}
    .scroll-thin::-webkit-scrollbar-track{background:transparent}

    .no-transition, .no-transition * { transition: none !important; }
  </style>
</head>

<body class="min-h-screen bg-white text-gray-900">
@php
  $me   = $me ?? auth()->user()?->fresh();
  $role = $me?->role;
  $roleLabel = $role === 'it'
      ? 'IT'
      : ($role === 'digital_banking'
          ? 'DIG'
          : ($role === 'kepala_divisi' ? 'Kepala Divisi' : 'User'));

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

  $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';

  $homeRouteName = match ($role) {
      'it'              => (\Route::has('it.dashboard') ? 'it.dashboard' : null),
      'kepala_divisi'   => (\Route::has('kd.dashboard') ? 'kd.dashboard' : null),
      'digital_banking' => (\Route::has('dig.dashboard') ? 'dig.dashboard' : null),
      default           => (\Route::has('dig.dashboard') ? 'dig.dashboard' : null),
  };
  $homeUrl = $homeRouteName ? route($homeRouteName) : url('/');

  $isDashboardActive = $homeRouteName
      ? request()->routeIs($homeRouteName)
      : url()->current() === $homeUrl;

  $notifRoute = match ($role) {
      'it'              => 'it.notifications',
      'kepala_divisi'   => 'kd.notifications',
      default           => 'dig.notifications',
  };
@endphp

{{-- MINI SIDEBAR --}}
<aside id="miniSidebar"
  class="hidden md:flex fixed inset-y-0 left-0 z-40 w-16 bg-white border-r shadow-xl flex-col items-center justify-between py-4">
  <div class="flex flex-col items-center gap-6">
    <button id="railLogoBtn" type="button" title="Buka Sidebar" aria-label="Buka Sidebar"
            class="rounded-xl p-2 hover:bg-[#FFF2F2] cursor-pointer">
      <img src="{{ asset('images/dki.png') }}" class="h-6 w-auto object-contain" alt="Logo" />
    </button>

    <a href="{{ $homeUrl }}"
       class="p-2 rounded-lg {{ $isDashboardActive ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
       title="Dashboard" aria-label="Dashboard">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
           fill="{{ $isDashboardActive ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
      </svg>
    </a>

    <a href="{{ route('semua.progresses') }}"
       class="p-2 rounded-lg {{ request()->routeIs('semua.progresses*') ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]'}}"
       title="Progress" aria-label="Progress">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
           fill="{{ request()->routeIs('semua.progresses*') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
        <path d="M4 22h16V2H4v20zm3-5h2v3H7v-3zm4-7h2v10h-2V10zm4 3h2v7h-2v-7z" />
      </svg>
    </a>

    @if(Route::has($notifRoute))
      <a href="{{ route($notifRoute) }}"
         class="p-2 rounded-lg {{ request()->routeIs($notifRoute.'*') ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]'}}"
         title="Notifikasi" aria-label="Notifikasi">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
             fill="{{ request()->routeIs($notifRoute.'*') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
          <path d="M12 24a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z"/>
        </svg>
      </a>
    @endif

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
        <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1a8.523 8.523 0 0 0-1.874-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"/>
      </svg>
    </a>

 <a href="/logout"
   data-confirm-logout="true"
   class="p-2 rounded-lg hover:bg-[#FFF2F2]" title="Log Out" aria-label="Log Out">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="black">
        <path d="M3 3h10a1 1 0 0 1 1 1v5h-2V5H5v14h7v-4h2v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/>
        <path d="M14 12l5-5v3h4v4h-4v3l-5-5z"/>
      </svg>
    </a>
  </div>
</aside>

<div id="sidebarBackdrop" class="hidden fixed inset-0 z-40 bg-black/30 md:hidden"></div>

<aside id="sidebar"
  class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] -translate-x-full
         bg-white border-r shadow-xl flex flex-col">

  @php
    $me  = $me ?? auth()->user()->fresh();
    $role = $me?->role;
  @endphp

  <div class="px-5 pt-5 pb-4 border-b bg-white">
    <div class="flex items-center">
      <img src="https://website-api.bankdki.co.id/integrations/storage/page-meta-data/007UlZbO3Oe6PivLltdFiQax6QH5kWDvb0cKPdn4.png"
           class="h-8 w-auto object-contain" alt="Bank Jakarta">
      <button id="sidebarCloseBtn" class="ml-auto p-2 rounded-lg border hover:bg-red-50 text-red-700"
              title="Tutup" aria-label="Tutup sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.71 2.89 18.3 9.17 12 2.89 5.71 4.3 4.29l6.29 6.29 6.3-6.29z"/>
        </svg>
      </button>
    </div>
  </div>

  <nav class="flex-1 overflow-y-auto py-3 text-sm font-medium text-gray-700">
    <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-3 mb-1">Dashboard</div>
    <a href="{{ $homeUrl }}"
       class="flex items-center gap-3 px-5 py-2.5 rounded-xl
              {{ $isDashboardActive ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
           fill="{{ $isDashboardActive ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
      </svg>
      <span>Dashboard</span>
    </a>

    <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Project</div>
    <a href="{{ route('semua.progresses') }}"
       class="flex items-center gap-3 px-5 py-2.5 rounded-xl
              {{ request()->routeIs('semua.progresses*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
           fill="{{ request()->routeIs('semua.progresses*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
        <path d="M4 22h16V2H4v20zm3-5h2v3H7v-3zm4-7h2v10h-2V10zm4 3h2v7h-2v-7z" />
      </svg>
      <span>Project</span>
    </a>

    <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Notifikasi</div>
    @php $notifHas = \Route::has($notifRoute); @endphp
    @if($notifHas)
      <a href="{{ route($notifRoute) }}"
         class="flex items-center gap-3 px-5 py-2.5 rounded-xl
                {{ request()->routeIs($notifRoute.'*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
             fill="{{ request()->routeIs($notifRoute.'*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
          <path d="M12 24a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z"/>
        </svg>
        <span>Notifikasi</span>
      </a>
    @endif

    <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Arsip</div>
    <a href="{{ route('semua.arsip') }}"
       class="flex items-center gap-3 px-5 py-2.5 rounded-xl
              {{ request()->routeIs('semua.arsip*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
           fill="{{ request()->routeIs('semua.arsip*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
        <path d="M3 3h18v4H3V3zm1 4h16v14H4V7zm2 2v10h12V9H6zm3 3h6v2H9v-2z" />
      </svg>
      <span>Arsip</span>
    </a>
  </nav>

  <div class="mt-6 mb-9 px-3 space-y-1 text-sm text-gray-900 bg-white">
    <a href="{{ route('account.setting') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-xl
              {{ request()->routeIs('account.setting*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'text-gray-800 hover:bg-[#FFF2F2]' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" viewBox="0 0 24 24" fill="currentColor">
        <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1a8.523 8.523 0 0 0 1.874.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1a8.523 8.523 0 0 0-1.874-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"/>
      </svg>
      <span>Pengaturan Akun</span>
    </a>

      <a href="/logout"
       data-confirm-logout="true"
       class="flex items-center gap-3 px-3 py-2 rounded-xl transition hover:bg-[#FFF2F2] text-gray-900"
       title="Log Out" aria-label="Log Out">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" fill="black" viewBox="0 0 24 24">
        <path d="M3 3h10a1 1 0 0 1 1 1v5h-2V5H5v14h7v-4h2v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
        <path d="M14 12l5-5v3h4v4h-4v3l-5-5z" />
      </svg>
      <span>Log Out</span>
    </a>
  </div>
</aside>

<div id="pageWrapper" class="transition-all duration-300 ml-0">
  <header class="sticky top-0 z-30 bg-[#8D2121] backdrop-blur">
    <div class="{{ $container }} py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <button id="sidebarOpenBtn"
                class="p-2 rounded-xl border border-red-200 text-red-50 bg-transparent/0 hover:bg-red-50/10 md:hidden"
                title="Buka Sidebar" aria-label="Buka Sidebar">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 3h18v2H3V3zm0 6h18v2H3V9zm0 6h18v2H3v-2z"/>
          </svg>
        </button>
        <span class="text-lg md:text-xl font-bold text-white select-none">Project</span>
      </div>

      <div class="hidden md:flex items-center gap-3 pl-4 border-l border-white/30">
        <img src="{{ $avatarUrl }}" class="w-8 h-8 rounded-full object-cover bg-white"
             alt="Avatar" loading="lazy" referrerpolicy="no-referrer"
             onerror="this.onerror=null;this.src='{{ $fallbackSvg }}';">
        <div class="leading-tight hidden md:block">
          <div class="text-[13px] font-semibold text-white truncate max-w-[140px]">
            {{ $me?->name ?? ($me?->username ?? 'User') }}
          </div>
          <div class="text-[11px] text-white/80 font-light">{{ $roleLabel }}</div>
        </div>
      </div>
    </div>
  </header>

  <div class="{{ $container }}">
    @php
      $q = request('status','all');        // all | in_progress | meet | not_meet
      $mine = request('mine','0');         // '1' atau '0'
      $mineActive = $mine === '1';
      $tab = fn($v) => $q===$v ? 'bg-[#7A1C1C] text-white' : 'bg-white text-[#7A1C1C] hover:bg-[#FFF2F2]';
      $mineBtnClass = $mineActive ? 'bg-[#7A1C1C] text-white' : 'bg-white text-[#7A1C1C] hover:bg-[#FFF2F2]';
      $buildUrl = function (string $status, bool $toggleMine = false) use ($mineActive) {
          $params = ['status' => $status];
          $params['mine'] = $toggleMine ? ($mineActive ? null : 1) : ($mineActive ? 1 : null);
          return route('semua.progresses', array_filter($params, fn($v)=>!is_null($v)));
      };
    @endphp

    <div class="py-3 flex items-center gap-3">
      <a href="{{ $buildUrl('all') }}"
         class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tab('all') }} grid place-items-center">Semua</a>
      <a href="{{ $buildUrl('in_progress') }}"
         class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tab('in_progress') }} grid place-items-center">Dalam Proses</a>
      <a href="{{ $buildUrl('meet') }}"
         class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tab('meet') }} grid place-items-center">Project Selesai, Memenuhi</a>
      <a href="{{ $buildUrl('not_meet') }}"
         class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tab('not_meet') }} grid place-items-center">Project Selesai, Tidak Memenuhi</a>

      <a href="{{ $buildUrl($q, true) }}"
         class="ml-auto rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $mineBtnClass }} grid place-items-center">
        Tugas Saya
      </a>
    </div>

    @if (session('success'))
      <div class="mt-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="mt-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
        <div class="font-semibold mb-1">Terjadi kesalahan:</div>
        <ul class="list-disc pl-5 space-y-1">
          @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
      </div>
    @endif

    @php $hasAny = false; @endphp
    @forelse ($projects as $project)
      @php
        $isFinalizedByDIG = !is_null($project->meets_requirement);
        $isMeet           = $isFinalizedByDIG && (int)$project->meets_requirement === 1;
        $isNotMeet        = $isFinalizedByDIG && (int)$project->meets_requirement === 0;

        $skipByTab = false;
        if ($q === 'in_progress' && $isFinalizedByDIG) $skipByTab = true;
        if ($q === 'meet'        && !$isMeet)          $skipByTab = true;
        if ($q === 'not_meet'    && !$isNotMeet)       $skipByTab = true;
      @endphp
      @if($skipByTab) @continue @endif
      @php
        $currentId = (int) auth()->id();
        $mineById = ((int)($project->digital_banking_id ?? 0) === $currentId)
                 || ((int)($project->developer_id ?? 0) === $currentId);

        $mineByName = false;
        if(!$mineById) {
          $meName = trim((string)($me?->name ?? ''));
          $dbName = trim((string)($project->digitalBanking->name ?? ''));
          $itName = trim((string)($project->developer->name ?? ''));
          $mineByName = $meName !== '' && ($meName === $dbName || $meName === $itName);
        }
        $isMine = $mineById || $mineByName;
        if ($mineActive && !$isMine) { @endphp @continue @php }
        $hasAny = true;

        $latestPercents = [];
        foreach ($project->progresses as $pr) {
          $last = $pr->updates->first() ?: $pr->updates->sortByDesc('update_date')->first();
          $latestPercents[] = $last ? (int) ($last->progress_percent ?? ($last->percent ?? 0)) : 0;
        }
        $realization = count($latestPercents) ? (int) round(array_sum($latestPercents) / max(count($latestPercents), 1)) : 0;

        $size=88; $stroke=10; $r=$size/2-$stroke; $circ=2*M_PI*$r; $off=$circ*(1-$realization/100);

        if ($isFinalizedByDIG) {
          $statusText  = $isMeet ? 'Project Selesai, Memenuhi' : 'Project Selesai, Tidak Memenuhi';
          $statusBg    = $isMeet ? '#DCFCE7' : '#FEE2E2';
          $statusColor = $isMeet ? '#166534' : '#7A1C1C';
        } else {
          $statusText  = 'Dalam Proses';
          $statusBg    = '#FEE2E2';
          $statusColor = '#7A1C1C';
        }

        $isDig = $me?->role === 'digital_banking';
        $canDecideCompletion = $project->can_decide_completion
          ?? (function() use ($project){
                $all = $project->progresses->every(function ($p) {
                  $last = $p->updates->first() ?: $p->updates->sortByDesc('update_date')->first();
                  $real = $last ? (int) ($last->progress_percent ?? ($last->percent ?? 0)) : 0;
                  return $real >= (int) $p->desired_percent && !is_null($p->confirmed_at);
                });
                return $all && is_null($project->meets_requirement);
             })();
      @endphp

      <section class="mt-5 rounded-2xl border-2 border-[#7A1C1C] bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <span class="inline-flex items-center rounded-full px-3 py-1 text-[12px] font-semibold"
                style="background: {{ $statusBg }}; color: {{ $statusColor }};">
            {{ $statusText }}
          </span>

          @if($isDig && $canDecideCompletion)
            <div class="ml-auto flex items-center gap-2">
              <form method="POST" action="{{ route('projects.setCompletion', $project->id) }}"
                    class="inline-block" title="Tandai project ini sebagai selesai dan memenuhi">
                @csrf @method('PATCH')
                <input type="hidden" name="meets" value="1">
                <button type="submit"
                        class="px-3 py-1.5 text-xs rounded-full bg-green-700 text-white hover:opacity-90">
                  Selesai, Memenuhi
                </button>
              </form>

              <form method="POST" action="{{ route('projects.setCompletion', $project->id) }}"
                    class="inline-block" title="Tandai project ini sebagai selesai dan tidak memenuhi">
                @csrf @method('PATCH')
                <input type="hidden" name="meets" value="0">
                <button type="submit"
                        class="px-3 py-1.5 text-xs rounded-full bg-[#7A1C1C] text-white hover:opacity-90">
                  Selesai, Tidak Memenuhi
                </button>
              </form>
            </div>
          @endif
        </div>

        <div class="mt-3 grid md:grid-cols-[auto,1fr,auto] items-start gap-4">
          <div class="flex items-center">
            <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}">
              <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}" stroke="#E9D0D0" stroke-width="{{ $stroke }}" fill="none" opacity=".9"/>
              <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}" stroke="#7A1C1C" stroke-width="{{ $stroke }}"
                      stroke-linecap="round" stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $off }}"
                      transform="rotate(-90 {{ $size/2 }} {{ $size/2 }})" fill="none"/>
              <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="16" font-weight="700" fill="#7A1C1C">{{ $realization }}%</text>
            </svg>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-1 text-sm">
            <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
              <span class="text-gray-700 font-medium">Nama Project</span><span>:</span>
              <span class="font-semibold">{{ $project->name }}</span>
            </div>
            <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
              <span class="text-gray-700">Penanggung Jawab (Digital Banking)</span><span>:</span>
              <span>{{ $project->digitalBanking->name ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-[auto_auto_1fr] gap-x-2 md:col-span-5">
              <span class="text-gray-700">Penanggung Jawab (Developer)</span><span>:</span>
              <span>{{ $project->developer->name ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-[auto_auto_1fr] gap-x-2 md:col-span-2">
              <span class="text-gray-700">Deskripsi</span><span>:</span>
              <span>{{ $project->description ?: '-' }}</span>
            </div>
          </div>

          <div class="flex items-start gap-2 justify-end">
            <a href="{{ route('projects.edit', $project->id) }}"
               class="p-2 rounded-lg bg-white/60 hover:bg-white border" title="Edit Project">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM22.61 5.64c.39-.39.39-1.02 0-1.41l-2.83-2.83a.9959.9959 0 0 0-1.41 0L16.13 3.04l3.75 3.75 2.73-2.73z"/>
              </svg>
            </a>
           <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
      data-confirm-delete="true"
      data-message="Yakin ingin menghapus project ini? Aksi ini tidak bisa dibatalkan.">
              @csrf @method('DELETE')
              <button type="submit" class="p-2 rounded-lg bg-white/60 hover:bg-white border" title="Hapus Project">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M6 7h12v12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7zm3-4h6l1 1h4v2H4V4h4l1-1z"/>
                </svg>
              </button>
            </form>
          </div>
        </div>

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
                  <span class="truncate max-w-[160px]" title="{{ $att->original_name }}">{{ $att->original_name }}</span>
                </a>
              @endforeach
            </div>
          </div>
        @endif

        <div class="mt-4 flex justify-end">
          <button type="button"
              data-target="progressForm-{{ $project->id }}"
              @if($isFinalizedByDIG)
                  disabled
                  title="Project sudah difinalisasi, progress baru tidak dapat ditambahkan."
                  class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm shadow bg-[#7A1C1C]/40 text-white cursor-not-allowed opacity-60"
              @else
                  class="btn-toggle-progress inline-flex items-center gap-2 rounded-xl bg-[#7A1C1C] text-white px-3 py-2 text-sm shadow"
              @endif
          >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                  fill="currentColor">
                  <path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z" />
              </svg>
              Tambah Progress
          </button>
        </div>

        <div id="progressForm-{{ $project->id }}" class="hidden mt-3 rounded-xl bg-white p-4 border border-[#E7C9C9]">
          <div class="font-semibold mb-2">Tambah Progress untuk Project ini</div>
          <form method="POST" action="{{ route('projects.progresses.store', $project->id) }}"
                class="grid grid-cols-1 md:grid-cols-5 gap-2">
            @csrf
            <input name="name" required placeholder="Nama Progress"
              class="rounded-xl bg-[#E2B9B9]/40 border border-[#C89898] px-3 py-2 outline-none md:col-span-2">
            <input type="date" name="start_date" required
              class="rounded-xl bg-[#E2B9B9]/40 border border-[#C89898] px-3 py-2 outline-none">
            <input type="date" name="end_date" required
              class="rounded-xl bg-[#E2B9B9]/40 border border-[#C89898] px-3 py-2 outline-none">
            <input type="number" name="desired_percent" required
                   min="1" max="100"
                   placeholder="Target %"
                   class="rounded-xl bg-[#E2B9B9]/40 border border-[#C89898] px-3 py-2 outline-none" />
            <button class="rounded-xl border-2 border-[#7A1C1C] bg-[#E2B9B9] px-4 py-2 font-semibold hover:bg-[#D9AFAF]">
              Tambah
            </button>
          </form>
        </div>

        <div class="mt-4">
          <div class="scroll-thin grid md:grid-cols-2 gap-4 max-h-[280px] overflow-y-auto pr-1">
            @forelse($project->progresses as $pr)
              @php
                $last              = $pr->updates->sortByDesc('update_date')->first();
                $realisasi         = $last ? (int)($last->percent ?? $last->progress_percent ?? 0) : 0;
                $isOwner           = (int)($pr->created_by ?? 0) === (int)auth()->id();
                $alreadyConfirmed  = !is_null($pr->confirmed_at);
                $isDigProgress     = $me?->role === 'digital_banking';
                $canUpdate         = $isOwner && $isDigProgress && !$alreadyConfirmed;

                $endDate   = $pr->end_date ? \Illuminate\Support\Carbon::parse($pr->end_date)->startOfDay() : null;
                $isOverdue = $endDate ? $endDate->lt(now()->startOfDay()) : false;
                $isUnmet   = $isOverdue && !$pr->confirmed_at && ($realisasi < (int)$pr->desired_percent);

                $canUpdate = $canUpdate && !$isOverdue;
                $updateDisabledReason = $isOverdue
                  ? 'Tidak bisa update: sudah lewat timeline selesai'
                  : ($alreadyConfirmed ? 'Sudah dikonfirmasi' : ($isOwner ? 'Hanya DIG yang bisa update' : 'Bukan pembuat progress'));
              @endphp

              <div class="rounded-2xl bg-[#F7E4E4] p-4 border border-[#E7C9C9]">
                <div class="flex items-start justify-between mb-2">
                  <div class="font-semibold">
                    Progress {{ $loop->iteration }}{{ $pr->name ? ' — '.$pr->name : '' }}
                    @if($isUnmet)
                      <span class="ml-2 inline-flex items-center rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-[11px] font-semibold">Tidak Memenuhi</span>
                    @endif
                  </div>

                  @if($isOwner)
                    <div class="flex gap-2">
                      <button type="button"
                        class="px-3 py-1.5 text-xs rounded-lg border bg-white/70 hover:bg-white"
                        onclick="document.getElementById('editProgress-{{ $pr->id }}').classList.toggle('hidden')">
                        Edit
                      </button>
                        <form method="POST"
      action="{{ route('progresses.destroy', $pr->id) }}"
      data-confirm-delete="true"
      data-message="Hapus progress ini?">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 text-xs rounded-lg border bg-white/70 hover:bg-white">
                          Hapus
                        </button>
                      </form>
                    </div>
                  @endif
                </div>

                @if($isUnmet)
                  <div class="mb-2 text-[12px] rounded-lg border border-red-300 bg-red-50 text-red-700 px-3 py-2">
                    Melewati timeline selesai, realisasi belum mencapai target & belum dikonfirmasi.
                  </div>
                @endif

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

                <div id="editProgress-{{ $pr->id }}" class="hidden mt-3">
                  <form method="POST" action="{{ route('progresses.update', $pr->id) }}"
                        class="grid grid-cols-1 md:grid-cols-5 gap-2 bg-white/70 rounded-xl p-3 border">
                    @csrf @method('PUT')
                    <input name="name" value="{{ old('name', $pr->name) }}" required
                      class="rounded-xl bg-white border px-3 py-2 outline-none md:col-span-2" placeholder="Nama progress"
                      @unless($isOwner) disabled @endunless>
                    <input type="date" name="start_date" value="{{ old('start_date', $pr->start_date) }}" required
                      class="rounded-xl bg-white border px-3 py-2 outline-none" @unless($isOwner) disabled @endunless>
                    <input type="date" name="end_date" value="{{ old('end_date', $pr->end_date) }}" required
                      class="rounded-xl bg-white border px-3 py-2 outline-none" @unless($isOwner) disabled @endunless>
                    <select name="desired_percent" class="rounded-xl bg-white border px-3 py-2 outline-none" required
                      @unless($isOwner) disabled @endunless>
                      @for($i=0;$i<=100;$i+=5)
                        <option value="{{ $i }}" @selected((int)old('desired_percent',$pr->desired_percent)===$i)>{{ $i }}%</option>
                      @endfor
                    </select>
                    <button class="h-[40px] min-w-[140px] px-4 rounded-full border-2 border-[#7A1C1C] bg-[#E2B9B9] hover:bg-[#D9AFAF] text-xs font-semibold"
                      @unless($isOwner) disabled @endunless>
                      Simpan Perubahan
                    </button>
                  </form>
                </div>

                <div class="mt-3">
                  <form method="POST" action="{{ route('progresses.updates.store', $pr->id) }}" class="flex flex-wrap gap-3 items-center">
                    @csrf
                    <input type="date" name="update_date" value="{{ now()->toDateString() }}"
                      class="rounded-xl border px-3 py-2 text-sm" @unless($canUpdate) disabled @endunless>
                    <input type="number" name="percent" min="0" max="100" placeholder="%"
                      class="rounded-xl border px-3 py-2 text-sm w-28" @unless($canUpdate) disabled @endunless required>
                    <button
                      class="rounded-xl bg-[#7A1C1C] text-white px-4 py-2 text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                      @unless($canUpdate) disabled @endunless
                      title="{{ $canUpdate ? '' : $updateDisabledReason }}">
                      Update Progress
                    </button>
                  </form>

                  <div class="mt-2">
                    @if(!$alreadyConfirmed)
                      <form method="POST" action="{{ route('progresses.confirm', $pr->id) }}">
                        @csrf
                        <button
                          class="rounded-xl bg-green-700 text-white px-4 py-2 text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                          {{ ($isOwner && $realisasi >= (int)$pr->desired_percent) ? '' : 'disabled' }}
                          title="{{ $isOwner ? 'Belum mencapai target' : 'Hanya pembuat progress yang dapat konfirmasi' }}">
                          Konfirmasi
                        </button>
                      </form>
                    @else
                      <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-semibold">
                        Sudah dikonfirmasi
                      </span>
                    @endif
                  </div>

                  @if($isOverdue && !$alreadyConfirmed)
                    <div class="mt-2">
                      <span class="inline-flex items-center rounded-full bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold">
                        Telat dari timeline
                      </span>
                    </div>
                  @endif
                </div>
              </div>
            @empty
              <div class="col-span-2 text-sm text-gray-600">Belum ada progress.</div>
            @endforelse
          </div>
        </div>

        <div class="mt-4 flex justify-end">
          <a href="{{ route('dig.projects.show', $project->id) }}"
             class="inline-flex items-center gap-2 rounded-[12px] border border-[#7A1C1C] px-4 py-2 text-sm font-semibold text-[#7A1C1C] bg-white hover:bg-[#FFF2F2]">
            Detail Informasi
          </a>
        </div>
      </section>
    @empty
    @endforelse

    @if(!$hasAny)
      <div class="mt-4">
        <div class="bg-[#EBD0D0] rounded-xl px-6 py-8 flex items-center justify-center border border-[#E7C9C9]">
          <div class="rounded-xl bg-[#CFA8A8] px-5 py-3 text-white/95">
            Tidak ada project.
          </div>
        </div>
      </div>
    @endif
  </div>
</div>
         {{-- ===== MODAL KONFIRMASI LOGOUT ===== --}}
    <div id="confirmLogoutModal"
         class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40">
        <div class="mx-4 w-full max-w-sm rounded-2xl bg-white shadow-xl border border-red-100 overflow-hidden">
            <div class="flex items-center gap-3 px-4 py-3 bg-[#8D2121] text-white">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 3h10a1 1 0 0 1 1 1v5h-2V5H5v14h7v-4h2v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/>
                        <path d="M14 12l5-5v3h4v4h-4v3l-5-5z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-semibold">Konfirmasi Logout</div>
                    <div class="text-xs text-white/80">Anda akan keluar dari akun ini.</div>
                </div>
            </div>
            <div class="px-4 py-4 text-sm text-gray-700">
                Yakin ingin logout dari akun ini?
            </div>
            <div class="flex justify-end gap-2 px-4 py-3 bg-[#FFF7F7]">
                <button type="button"
                        id="cancelLogoutBtn"
                        class="inline-flex items-center justify-center rounded-xl border border-red-200 px-4 py-1.5 text-xs font-semibold text-[#7A1C1C] bg-white hover:bg-red-50">
                    Batal
                </button>
                <button type="button"
                        id="confirmLogoutBtn"
                        class="inline-flex items-center justify-center rounded-xl border border-[#7A1C1C] px-4 py-1.5 text-xs font-semibold text-white bg-[#8D2121] hover:bg-[#741B1B]">
                    Ya, Logout
                </button>
            </div>
        </div>
    </div>

    {{-- ===== MODAL KONFIRMASI HAPUS (PROJECT / PROGRESS) ===== --}}
    <div id="confirmDeleteModal"
         class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40">
        <div class="mx-4 w-full max-w-sm rounded-2xl bg-white shadow-xl border border-red-100 overflow-hidden">
            <div class="flex items-center gap-3 px-4 py-3 bg-[#7A1C1C] text-white">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                         viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11 15h2v2h-2zm0-8h2v6h-2z"/>
                        <path d="M12 2C6.49 2 2 6.49 2 12s4.49 10 10 10
                                 10-4.49 10-10S17.51 2 12 2zm0 18
                                 c-4.41 0-8-3.59-8-8s3.59-8 8-8
                                 8 3.59 8 8-3.59 8-8 8z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-semibold">Konfirmasi Hapus</div>
                    <div class="text-xs text-white/80">Aksi ini tidak bisa dibatalkan.</div>
                </div>
            </div>
            <div class="px-4 py-4 text-sm text-gray-700" id="confirmDeleteMessage">
                Yakin ingin menghapus data ini?
            </div>
            <div class="flex justify-end gap-2 px-4 py-3 bg-[#FFF7F7]">
                <button type="button"
                        id="cancelDeleteBtn"
                        class="inline-flex items-center justify-center rounded-xl border border-red-200 px-4 py-1.5 text-xs font-semibold text-[#7A1C1C] bg-white hover:bg-red-50">
                    Batal
                </button>
                <button type="button"
                        id="confirmDeleteBtn"
                        class="inline-flex items-center justify-center rounded-xl border border-[#7A1C1C] px-4 py-1.5 text-xs font-semibold text-white bg-[#8D2121] hover:bg-[#741B1B]">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
<script>
  const sidebar      = document.getElementById('sidebar');
  const sidebarOpen  = document.getElementById('sidebarOpenBtn');
  const sidebarClose = document.getElementById('sidebarCloseBtn');
  const sbBackdrop   = document.getElementById('sidebarBackdrop');
  const pageWrapper  = document.getElementById('pageWrapper');
  const railLogo     = document.getElementById('railLogoBtn');
  const miniSidebar  = document.getElementById('miniSidebar');

  const add = (el, ...cls) => el && el.classList.add(...cls);
  const rm  = (el, ...cls) => el && el.classList.remove(...cls);

  const SIDEBAR_OPEN_KEY = 'dig.sidebar.open';
  const setPersist = (isOpen) => {
    try { localStorage.setItem(SIDEBAR_OPEN_KEY, isOpen ? '1' : '0'); } catch {}
  };
  const getPersist = () => {
    try { return localStorage.getItem(SIDEBAR_OPEN_KEY) === '1'; } catch { return false; }
  };

  const openSidebar = () => {
    rm(sidebar, '-translate-x-full');
    add(miniSidebar, 'md:hidden');
    rm(pageWrapper, 'md:ml-16');
    add(pageWrapper, 'ml-72', 'md:ml-72');
    rm(sbBackdrop, 'hidden');
    setPersist(true);
  };

  const closeSidebar = () => {
    add(sidebar, '-translate-x-full');
    rm(miniSidebar, 'md:hidden');
    rm(pageWrapper, 'ml-72', 'md:ml-72');
    add(pageWrapper, 'md:ml-16');
    add(sbBackdrop, 'hidden');
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
        rm(sidebar, '-translate-x-full');
        add(miniSidebar, 'md:hidden');
        rm(pageWrapper, 'md:ml-16');
        add(pageWrapper, 'ml-72', 'md:ml-72');
        rm(sbBackdrop, 'hidden');
      } else {
        add(sidebar, '-translate-x-full');
        rm(miniSidebar, 'md:hidden');
        rm(pageWrapper, 'ml-72', 'md:ml-72');
        add(pageWrapper, 'md:ml-16');
        rm(sbBackdrop, 'hidden');
      }
    } else {
      if (persistedOpen) {
        rm(sidebar, '-translate-x-full');
        add(miniSidebar, 'md:hidden');
        rm(pageWrapper, 'md:ml-16');
        rm(sbBackdrop, 'hidden');
      } else {
        add(sidebar, '-translate-x-full');
        add(miniSidebar, 'md:hidden');
        rm(pageWrapper, 'md:ml-16', 'ml-72', 'md:ml-72');
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

  document.querySelectorAll('.btn-toggle-progress').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-target');
      const el = document.getElementById(id);
      if (el) el.classList.toggle('hidden');
    });
  });
</script>
 <script>
        (function () {
            let pendingLogoutHref = null;
            let pendingDeleteForm = null;

            const logoutModal = document.getElementById('confirmLogoutModal');
            const deleteModal = document.getElementById('confirmDeleteModal');
            const deleteMsgEl = document.getElementById('confirmDeleteMessage');

            const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
            const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');

            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

            function openModal(modal) {
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal(modal) {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }

            // ====== LOGOUT HANDLER ======
            document.querySelectorAll('[data-confirm-logout="true"]').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    pendingLogoutHref = this.getAttribute('href');
                    openModal(logoutModal);
                });
            });

            confirmLogoutBtn?.addEventListener('click', function () {
                if (pendingLogoutHref) {
                    window.location.href = pendingLogoutHref;
                }
            });

            cancelLogoutBtn?.addEventListener('click', function () {
                pendingLogoutHref = null;
                closeModal(logoutModal);
            });

            // Klik di luar card = tutup modal logout
            logoutModal?.addEventListener('click', function (e) {
                if (e.target === logoutModal) {
                    pendingLogoutHref = null;
                    closeModal(logoutModal);
                }
            });

            // ====== DELETE HANDLER (project / progress) ======
            document.querySelectorAll('form[data-confirm-delete="true"]').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    pendingDeleteForm = this;

                    const msg = this.getAttribute('data-message');
                    if (msg && deleteMsgEl) {
                        deleteMsgEl.textContent = msg;
                    }

                    openModal(deleteModal);
                });
            });

            confirmDeleteBtn?.addEventListener('click', function () {
                if (pendingDeleteForm) {
                    const formToSubmit = pendingDeleteForm;
                    pendingDeleteForm = null;
                    closeModal(deleteModal);
                    formToSubmit.submit();
                }
            });

            cancelDeleteBtn?.addEventListener('click', function () {
                pendingDeleteForm = null;
                closeModal(deleteModal);
            });

            // Klik di luar card = tutup modal delete
            deleteModal?.addEventListener('click', function (e) {
                if (e.target === deleteModal) {
                    pendingDeleteForm = null;
                    closeModal(deleteModal);
                }
            });

            // ESC key untuk nutup modal (kalau ada yang kebuka)
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    if (logoutModal && !logoutModal.classList.contains('hidden')) {
                        pendingLogoutHref = null;
                        closeModal(logoutModal);
                    }
                    if (deleteModal && !deleteModal.classList.contains('hidden')) {
                        pendingDeleteForm = null;
                        closeModal(deleteModal);
                    }
                }
            });
        })();
    </script>
</body>
</html>
