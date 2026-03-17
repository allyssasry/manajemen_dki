<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Notifikasi DIG</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    html { scrollbar-gutter: stable; }
    body { overflow-x: hidden; }
    .scroll-thin::-webkit-scrollbar{width:6px;height:6px}
    .scroll-thin::-webkit-scrollbar-thumb{background:#cda7a7;border-radius:9999px}
    .scroll-thin::-webkit-scrollbar-track{background:transparent}
    .no-transition, .no-transition * { transition: none !important; }
  </style>
</head>

<body class="min-h-screen bg-[#FFFAFA] text-gray-900">
@php
  use App\Models\Project;   // <-- TAMBAHAN
  use App\Models\Progress;  // <-- TAMBAHAN

  $me   = $me ?? auth()->user()?->fresh();
  $role = $me?->role;
  $roleLabel = $role === 'it' ? 'IT' : ($role === 'digital_banking' ? 'DIG' : ($role === 'supervisor' ? 'Supervisor' : 'User'));

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

  // Satu sumber layout container (konsisten dengan dashboard)
  $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';
@endphp

{{-- ================== MINI SIDEBAR (RAIL) ================== --}}
@php $iconColor = '#7A1C1C'; @endphp
{{-- ===== MINI SIDEBAR (rail ikon) ===== --}}
<aside id="miniSidebar"
    class="hidden md:flex fixed inset-y-0 left-0 z-40 w-16 bg-white border-r shadow-xl flex-col items-center justify-between py-4">

    <div class="flex flex-col items-center gap-6">
        {{-- Logo / buka sidebar penuh --}}
        <button id="railLogoBtn" type="button" title="Buka Sidebar" aria-label="Buka Sidebar"
            class="rounded-xl p-2 hover:bg-[#FFF2F2] cursor-pointer">
            <img src="{{ asset('images/dki.png') }}" class="h-6 w-auto object-contain" alt="Logo" />
        </button>

        {{-- DASHBOARD --}}
        <a href="{{ route('dig.dashboard') }}"
           class="p-2 rounded-lg {{ request()->routeIs('dig.dashboard') ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
           title="Dashboard" aria-label="Dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                 fill="{{ request()->routeIs('dig.dashboard') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
              <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
        </a>

        {{-- PROGRESS --}}
        <a href="{{ route('semua.progresses') }}"
           class="p-2 rounded-lg {{ request()->routeIs('semua.progresses*') ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
           title="Progress" aria-label="Progress">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                 fill="{{ request()->routeIs('semua.progresses*') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
                <path d="M4 22h16V2H4v20zm3-5h2v3H7v-3zm4-7h2v10h-2V10zm4 3h2v7h-2v-7z" />
            </svg>
        </a>

        {{-- NOTIFIKASI --}}
        <a href="{{ route('dig.notifications') }}"
           class="p-2 rounded-lg {{ request()->routeIs('dig.notifications*') ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
           title="Notifikasi" aria-label="Notifikasi">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                 fill="{{ request()->routeIs('dig.notifications*') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
                <path d="M12 24a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z"/>
            </svg>
        </a>

        {{-- ARSIP --}}
        <a href="{{ route('semua.arsip') }}"
           class="p-2 rounded-lg {{ request()->routeIs('semua.arsip*') ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
           title="Arsip" aria-label="Arsip">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                 fill="{{ request()->routeIs('semua.arsip*') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
                <path d="M3 3h18v4H3V3zm1 4h16v14H4V7zm2 2v10h12V9H6zm3 3h6v2H9v-2z" />
            </svg>
        </a>
    </div>

    <div class="flex flex-col items-center gap-4">
        {{-- PENGATURAN --}}
        <a href="{{ route('account.setting') }}"
           class="p-2 rounded-lg {{ request()->routeIs('account.setting*') ? 'bg-[#FFF2F2] text-[#7A1C1C]' : 'hover:bg-[#FFF2F2] text-gray-800' }}"
           title="Pengaturan Akun" aria-label="Pengaturan Akun">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1a8.523 8.523 0 0 0-1.874-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"/>
            </svg>
        </a>

        {{-- LOGOUT --}}
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


{{-- ===== BACKDROP (mobile) ===== --}}
<div id="sidebarBackdrop" class="hidden fixed inset-0 z-40 bg-black/30 md:hidden"></div>

{{-- ===== SIDEBAR PENUH (drawer) ===== --}}
<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] -translate-x-full
           bg-white border-r shadow-xl flex flex-col">

    <div class="px-5 pt-5 pb-4 border-b bg-white">
        <div class="flex items-center">
            <img src="https://website-api.bankdki.co.id/integrations/storage/page-meta-data/007UlZbO3Oe6PivLltdFiQax6QH5kWDvb0cKPdn4.png"
                class="h-8 w-auto object-contain" alt="Bank Jakarta">
            <button id="sidebarCloseBtn" class="ml-auto p-2 rounded-lg border hover:bg-red-50 text-red-700"
                title="Tutup" aria-label="Tutup sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.71 2.89 18.3 9.17 12 2.89 5.71 4.3 4.29l6.29 6.29 6.3-6.29z" />
                </svg>
            </button>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-3 text-sm font-medium text-gray-700">
        {{-- DASHBOARD --}}
        <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-3 mb-1">Dashboard</div>
        <a href="{{ route('dig.dashboard') }}"
           class="flex items-center gap-3 px-5 py-2.5 rounded-xl
                  {{ request()->routeIs('dig.dashboard') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
                 fill="{{ request()->routeIs('dig.dashboard') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
              <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
            <span>Dashboard</span>
        </a>

        {{-- PROGRESS --}}
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

        {{-- NOTIFIKASI --}}
        <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Notifikasi</div>
        <a href="{{ route('dig.notifications') }}"
           class="flex items-center gap-3 px-5 py-2.5 rounded-xl
                  {{ request()->routeIs('dig.notifications*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
                 fill="{{ request()->routeIs('dig.notifications*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
              <path d="M12 24a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z"/>
            </svg>
            <span>Notifikasi</span>
        </a>

        {{-- ARSIP --}}
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

    <div class="mt-6 mb-9 px-3 space-y-1 text-sm text-gray-900">
        <a href="{{ route('account.setting') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-xl
                  {{ request()->routeIs('account.setting*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'text-gray-800 hover:bg-[#FFF2F2]' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" viewBox="0 0 24 24" fill="currentColor">
        <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1a8.523 8.523 0 0 0-1.874-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"/>
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

{{-- ================== WRAPPER & HEADER ================== --}}
<div id="pageWrapper" class="transition-all duration-300 ml-0">
  <header class="sticky top-0 z-30 bg-[#8D2121] backdrop-blur">
    <div class="{{ $container }} py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        {{-- Tombol open sidebar (mobile) --}}
        <button id="sidebarOpenBtn"
                class="p-2 rounded-xl border border-red-200 text-red-50 bg-transparent/0 hover:bg-red-50/10 md:hidden"
                title="Buka Sidebar" aria-label="Buka Sidebar">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 3h18v2H3V3zm0 6h18v2H3V9zm0 6h18v2H3v-2z"/>
          </svg>
        </button>
        <span class="text-lg md:text-xl font-bold text-white select-none">Notifikasi</span>
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

  {{-- ================== KONTEN ================== --}}
  <div class="{{ $container }}">
    {{-- HEADER LIST --}}
    <div class="py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        {{-- (bisa tambahin badge total unread kalau mau) --}}
      </div>

      <form method="POST" action="{{ route('dig.notifications.readAll') }}">
        @csrf
        <button class="text-sm rounded-lg border px-3 py-1 bg-white hover:bg-red-50 border-red-200 text-[#7A1C1C]">
          Tandai semua terbaca
        </button>
      </form>
    </div>

    <main class="py-4">
      @php
        $norm = fn($v) => strtolower(trim((string) $v));

        // Filter: notif buat DIG yang dikirim dari IT
        $isForDigFromIt = function($n) use ($norm) {
          $d   = $n->data ?? [];
          $typ = $norm(data_get($d,'type'));
          $by  = $norm(data_get($d,'by_role'));
          $dev = $norm(data_get($d,'developer_role') ?? '');
          $tgt = $norm(data_get($d,'target_role') ?? '');

          $allowedTypes = ['it_project_created', 'progress_confirmed'];

          $isAllowedType = in_array($typ, $allowedTypes, true);
          $fromIT        = ($by === 'it') || ($dev === 'it');
          $targetDIG     = ($tgt === '' || $tgt === 'digital_banking');

          return $isAllowedType && $fromIT && $targetDIG;
        };

        // Ambil item dari paginator (halaman ini)
        $allItems = isset($notifications)
          ? collect($notifications->items() ?? [])
          : collect();

        $nowJak = \Illuminate\Support\Carbon::now('Asia/Jakarta');
        $sevenDaysAgoJak = $nowJak->copy()->subDays(7)->startOfDay();

        // Filter: hanya 7 hari ke belakang + khusus DIG dari IT
        $filtered = $allItems
          ->filter($isForDigFromIt)
          ->filter(function($n) use ($sevenDaysAgoJak) {
            $createdJak = optional($n->created_at)->timezone('Asia/Jakarta');
            return $createdJak && $createdJak->gte($sevenDaysAgoJak);
          });

        // ====== HAPUS NOTIFIKASI YANG PROJECT / PROGRESS-NYA SUDAH DIHAPUS ======
        // Ambil semua project_id & progress_id dari data notif
        $projectIds = $filtered
          ->map(fn($n) => data_get($n->data, 'project_id'))
          ->filter()
          ->unique()
          ->values()
          ->all();

        $progressIds = $filtered
          ->map(fn($n) => data_get($n->data, 'progress_id'))
          ->filter()
          ->unique()
          ->values()
          ->all();

        $existingProjectIds = !empty($projectIds)
          ? Project::whereIn('id', $projectIds)->pluck('id')->map(fn($id) => (int) $id)->all()
          : [];

        $existingProgressIds = !empty($progressIds)
          ? Progress::whereIn('id', $progressIds)->pluck('id')->map(fn($id) => (int) $id)->all()
          : [];

        $filtered = $filtered->filter(function($n) use ($existingProjectIds, $existingProgressIds) {
          $pid   = (int) (data_get($n->data, 'project_id') ?? 0);
          $prgId = (int) (data_get($n->data, 'progress_id') ?? 0);

          // Kalau notif tidak punya project_id & progress_id, biarkan tampil (notif umum)
          if ($pid === 0 && $prgId === 0) {
            return true;
          }

          // Kalau punya project_id tapi project sudah dihapus => sembunyikan notif
          if ($pid !== 0 && !in_array($pid, $existingProjectIds, true)) {
            return false;
          }

          // Kalau punya progress_id tapi progress sudah dihapus => sembunyikan notif
          if ($prgId !== 0 && !in_array($prgId, $existingProgressIds, true)) {
            return false;
          }

          return true;
        });

        // Group by tanggal (YYYY-mm-dd), lalu sort desc
        $groupedByDate = $filtered
          ->groupBy(function($n) {
            return optional($n->created_at)->timezone('Asia/Jakarta')->format('Y-m-d');
          })
          ->sortKeysDesc();
      @endphp

      @if($groupedByDate->isEmpty())
        {{-- EMPTY STATE GLOBAL --}}
        <div class="py-12 text-center text-sm text-gray-600">
          Belum ada notifikasi dalam 7 hari terakhir.
        </div>
      @else
        @foreach($groupedByDate as $dateKey => $group)
          @php
            $dt       = \Illuminate\Support\Carbon::parse($dateKey, 'Asia/Jakarta');
            $isToday  = $dt->isSameDay($nowJak);
            // Label section: Hari Ini untuk hari ini, selain itu tanggal
            $label    = $isToday
              ? 'Hari Ini'
              : $dt->translatedFormat('d M Y'); // contoh: 24 Nov 2025

            $unreadInGroup = $group->whereNull('read_at')->count();
          @endphp

          <section class="mb-8">
            <div class="flex items-center justify-between mb-3">
              <h2 class="text-base font-semibold">{{ $label }}</h2>
              @if($unreadInGroup > 0)
                <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 rounded-full bg-[#7A1C1C] text-white text-xs px-2">
                  {{ $unreadInGroup > 99 ? '99+' : $unreadInGroup }}
                </span>
              @endif
            </div>

            <div class="space-y-3">
              @foreach($group as $n)
                @php
                  $d         = $n->data ?? [];
                  $type      = $norm(data_get($d,'type'));
                  $late      = (bool) data_get($d,'late', false);
                  $pId       = data_get($d,'project_id');
                  $pName     = data_get($d,'project_name', 'Project');
                  $progId    = data_get($d,'progress_id');
                  $progName  = data_get($d,'progress_name', 'Progress');
                  $headline  = data_get($d,'message', '');
                  $created   = optional($n->created_at)->timezone('Asia/Jakarta');
                  $timeText  = $created ? $created->format('H.i') : '-';
                  $dateText  = $created ? $created->translatedFormat('d M Y') : '-';
                  $dateTime  = $created ? $created->format('d M Y, H.i') : '-';
                  $isUnread  = is_null($n->read_at);
                @endphp

                @if($type === 'progress_confirmed')
                  {{-- KARTU: IT KONFIRMASI PROGRESS --}}
                  <div class="rounded-xl px-5 py-4 border {{ $isUnread ? 'border-[#7A1C1C] bg-[#F2DCDC]' : 'border-[#E7C9C9] bg-white' }}">
                    <div class="flex items-center justify-between">
                      <div class="text-[15px] font-semibold">
                        {{ $timeText }}
                        <span class="text-[12px] font-normal text-gray-600">(jam dikonfirmasi)</span>
                      </div>
                      <div class="text-[14px] font-semibold text-right">
                        {{ $headline ?: 'IT telah mengonfirmasi' }} {{ $progName }}
                      </div>
                    </div>

                    <div class="mt-3 text-[14px] leading-6">
                      <div><span class="font-semibold">Nama Project :</span> {{ $pName }}</div>
                      <div>
                        @if(function_exists('route') && Route::has('dig.projects.show') && $pId)
                          <a class="underline text-[#0a58ca]" href="{{ route('dig.projects.show', $pId) }}">{{ $progName }}</a>
                        @else
                          <span class="underline">{{ $progName }}</span>
                        @endif
                      </div>
                      <div class="mt-1 text-xs text-gray-600">
                        {{ $dateText }} • {{ $timeText }} WIB
                      </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                      <div>
                        @if($late)
                          <span class="inline-flex items-center text-[11px] px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                            IT Tidak Memenuhi Target
                          </span>
                        @else
                          <span class="inline-flex items-center text-[11px] px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                            IT Telah Mengonfirmasi
                          </span>
                        @endif
                      </div>
                      <form method="POST" action="{{ route('dig.notifications.read', $n->id) }}">
                        @csrf
                        <button class="text-xs underline text-[#7A1C1C]">
                          {{ $n->read_at ? 'Terbaca' : 'Tandai terbaca' }}
                        </button>
                      </form>
                    </div>
                  </div>

                @elseif($type === 'it_project_created')
                  {{-- KARTU: IT MEMBUAT PROJECT BARU --}}
                  <div class="rounded-xl px-5 py-4 border {{ $isUnread ? 'border-[#7A1C1C] bg-[#F2DCDC]' : 'border-[#E7C9C9] bg-white' }}">
                    <div class="flex items-center justify-between">
                      <div class="text-[15px] font-semibold">
                        {{ $timeText }}
                        <span class="text-[12px] font-normal text-gray-600">(jam dibuat)</span>
                      </div>
                      <div class="text-[14px] font-semibold text-right">
                        {{ $headline ?: 'IT membuat project baru' }}
                      </div>
                    </div>

                    <div class="mt-3 text-[14px] leading-6">
                      <div><span class="font-semibold">Nama Project :</span> {{ $pName }}</div>
                      <div>
                        @if(function_exists('route') && Route::has('dig.projects.show') && $pId)
                          <a class="underline text-[#0a58ca]" href="{{ route('dig.projects.show', $pId) }}">Lihat detail project</a>
                        @endif
                      </div>
                      <div class="mt-1 text-xs text-gray-600">
                        {{ $dateText }} • {{ $timeText }} WIB
                      </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                      <div>
                        <span class="inline-flex items-center text-[11px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                          Project baru dari IT
                        </span>
                      </div>
                      <form method="POST" action="{{ route('dig.notifications.read', $n->id) }}">
                        @csrf
                        <button class="text-xs underline text-[#7A1C1C]">
                          {{ $n->read_at ? 'Terbaca' : 'Tandai terbaca' }}
                        </button>
                      </form>
                    </div>
                  </div>

                @else
                  {{-- FALLBACK UMUM --}}
                  <div class="rounded-xl px-5 py-4 border {{ $isUnread ? 'border-[#7A1C1C] bg-[#F2DCDC]' : 'border-[#E7C9C9] bg-white' }}">
                    <div class="flex items-center justify-between">
                      <div class="text-[13px] font-semibold text-gray-700">
                        {{ $dateTime }}
                      </div>
                      <div class="text-[14px] font-semibold text-right">
                        Notifikasi
                      </div>
                    </div>
                    <div class="mt-3 text-[14px] leading-6">
                      <div class="text-gray-700">{{ $headline ?: 'Ada pembaruan dari IT.' }}</div>
                    </div>
                    <div class="mt-3 flex items-center justify-end">
                      <form method="POST" action="{{ route('dig.notifications.read', $n->id) }}">
                        @csrf
                        <button class="text-xs underline text-[#7A1C1C]">
                          {{ $n->read_at ? 'Terbaca' : 'Tandai terbaca' }}
                        </button>
                      </form>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          </section>
        @endforeach

        {{-- PAGINATION --}}
        @if(isset($notifications) && $notifications->lastPage() > 1)
          <div class="mt-4">
            {{ $notifications->links() }}
          </div>
        @endif
      @endif
    </main>
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

{{-- ================== SCRIPT ================== --}}
<script>
  const sidebar      = document.getElementById('sidebar');
  const sidebarOpen  = document.getElementById('sidebarOpenBtn'); // mobile
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
