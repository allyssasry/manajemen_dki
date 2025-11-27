<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Notifikasi | Kepala Divisi</title>
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
  {{-- seed kelas supaya Tailwind load util & jaga margin saat sidebar terbuka --}}
  <div class="hidden ml-72 md:ml-72"></div>

  @php
    use Illuminate\Support\Carbon;

    // ==== USER & AVATAR ====
    $me        = $me ?? auth()->user()?->fresh();
    $role      = $me?->role;
    $roleLabel = $role === 'it'
      ? 'IT'
      : ($role === 'digital_banking'
          ? 'DIG'
          : ($role === 'kepala_divisi' ? 'Kepala Divisi' : 'User'));

    $initial = urlencode(mb_substr($me?->name ?? $me?->username ?? 'U', 0, 1));
    $fallbackSvg = "data:image/svg+xml;utf8,".rawurlencode(
      '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">'.
      '<rect width="100%" height="100%" rx="12" ry="12" fill="#7A1C1C"/>'.
      '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="28" fill="#fff">'.$initial.'</text>' .
      '</svg>'
    );
    $rawUrl    = $me?->avatar_url_public;
    $extraKey  = $me?->avatar_cache_key ?? ($me?->updated_at?->timestamp ?? time());
    $avatarUrl = $rawUrl ? ($rawUrl.(str_contains($rawUrl,'?') ? '&' : '?').'ck='.$extraKey) : $fallbackSvg;

    // Container layout (samain dengan halaman lain KD)
    $container = 'max-w-6xl mx-auto w-full px-5';

    // ==== DATA NOTIF UNTUK KEPALA DIVISI ====
    $norm = fn($v) => strtolower(trim((string) $v));

    $user = auth()->user();
    $allNotifications = $user
      ? $user->notifications()->latest()->take(50)->get()
      : collect();

    // Filter: hanya 3 tipe yang dibutuhkan KD
    $filtered = $allNotifications->filter(function($n) use ($norm) {
      $d   = $n->data ?? [];
      $typ = $norm(data_get($d, 'type'));
      $tgt = $norm(data_get($d, 'target_role') ?? '');

      $allowedTypes = [
        'dig_project_created',       // DIG membuat project
        'it_project_created',        // IT membuat project
        'dig_completion_decision',   // DIG mengonfirmasi project selesai
      ];

      $isAllowedType = in_array($typ, $allowedTypes, true);

      // Kalau kamu pakai target_role di data notif, KD ikut baca kalau:
      // - target_role kosong (global) ATAU
      // - target_role = 'kepala_divisi' ATAU 'supervisor'
      $targetKD = ($tgt === '' || $tgt === 'kepala_divisi' || $tgt === 'supervisor');

      return $isAllowedType && $targetKD;
    });

    $unreadCount = $filtered->whereNull('read_at')->count();

    // Group by tanggal (Y-m-d) biar enak dibaca
    $nowJak = Carbon::now('Asia/Jakarta');
    $groupedByDate = $filtered
      ->groupBy(function($n) {
        return optional($n->created_at)->timezone('Asia/Jakarta')->format('Y-m-d');
      })
      ->sortKeysDesc();
  @endphp

  {{-- ===== MINI SIDEBAR (rail ikon) – Kepala Divisi (KD) ===== --}}
  <aside id="miniSidebar"
    class="hidden md:flex fixed inset-y-0 left-0 z-40 w-16 bg-white border-r shadow-xl flex-col items-center justify-between py-4">

    {{-- TOP: logo + menu utama --}}
    <div class="flex flex-col items-center gap-6">
      {{-- Logo / buka sidebar penuh --}}
      <button id="railLogoBtn" type="button" title="Buka Sidebar" aria-label="Buka Sidebar"
              class="rounded-xl p-2 hover:bg-[#FFF2F2] transition cursor-pointer">
        <img src="{{ asset('images/dki.png') }}" class="h-6 w-auto object-contain" alt="Logo" />
      </button>

      {{-- DASHBOARD KD --}}
      <a href="{{ route('kd.dashboard') }}"
         class="p-2 rounded-lg {{ request()->routeIs('kd.dashboard')
              ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200'
              : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
         title="Dashboard" aria-label="Dashboard">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
             fill="{{ request()->routeIs('kd.dashboard') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
          <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
      </a>

      {{-- PROGRESS KD --}}
      <a href="{{ route('kd.progresses') }}"
         class="p-2 rounded-lg {{ request()->routeIs('kd.progresses*')
              ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200'
              : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
         title="Project" aria-label="Project">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
             fill="{{ request()->routeIs('kd.progresses*') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
          <path d="M4 22h16V2H4v20zm3-5h2v3H7v-3zm4-7h2v10h-2V10zm4 3h2v7h-2v-7z" />
        </svg>
      </a>

      {{-- NOTIFIKASI KD --}}
      <a href="{{ route('kd.notifications') }}"
         class="p-2 rounded-lg {{ request()->routeIs('kd.notifications*')
              ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200'
              : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
         title="Notifikasi" aria-label="Notifikasi">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
             fill="{{ request()->routeIs('kd.notifications*') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
          <path d="M12 24a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z"/>
        </svg>
      </a>

      {{-- ARSIP (pakai global semua.arsip) --}}
      <a href="{{ route('semua.arsip') }}"
         class="p-2 rounded-lg {{ request()->routeIs('semua.arsip*')
              ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200'
              : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
         title="Arsip" aria-label="Arsip">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
             fill="{{ request()->routeIs('semua.arsip*') ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
          <path d="M3 3h18v4H3V3zm1 4h16v14H4V7zm2 2v10h12V9H6zm3 3h6v2H9v-2z" />
        </svg>
      </a>
    </div>

    {{-- BOTTOM: Pengaturan Akun + Log Out --}}
    <div class="flex flex-col items-center gap-4">
      {{-- PENGATURAN AKUN --}}
      <a href="{{ route('account.setting') }}"
         class="p-2 rounded-lg {{ request()->routeIs('account.setting*')
              ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200'
              : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
         title="Pengaturan Akun" aria-label="Pengaturan Akun">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-none"
             viewBox="0 0 24 24"
             fill="{{ request()->routeIs('account.setting*') ? '#7A1C1C' : 'currentColor' }}">
          <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1a8.523 8.523 0 0 0-1.874-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 110 7 3.5 3.5 0 010-7z"/>
        </svg>
      </a>

      {{-- LOGOUT --}}
     <a href="/logout"
   data-confirm-logout="true"
   class="p-2 rounded-lg hover:bg-[#FFF2F2]" title="Log Out" aria-label="Log Out">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
             viewBox="0 0 24 24" fill="black">
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
             bg-white border-r shadow-xl flex flex-col transition-transform duration-300 ease-out">

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
          <a href="{{ route('kd.dashboard') }}"
             class="flex items-center gap-3 px-5 py-2.5 rounded-xl
                    {{ request()->routeIs('kd.dashboard') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
                 fill="{{ request()->routeIs('kd.dashboard') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
              <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
            <span>Dashboard</span>
          </a>

          {{-- PROGRESS --}}
          <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Project</div>
          <a href="{{ route('kd.progresses') }}"
             class="flex items-center gap-3 px-5 py-2.5 rounded-xl
                    {{ request()->routeIs('kd.progresses*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
                 fill="{{ request()->routeIs('kd.progresses*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
              <path d="M4 22h16V2H4v20zm3-5h2v3H7v-3zm4-7h2v10h-2V10zm4 3h2v7h-2v-7z" />
            </svg>
            <span>Project</span>
          </a>

          {{-- NOTIFIKASI --}}
          <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Notifikasi</div>
          <a href="{{ route('kd.notifications') }}"
             class="flex items-center gap-3 px-5 py-2.5 rounded-xl
                    {{ request()->routeIs('kd.notifications*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
                 fill="{{ request()->routeIs('kd.notifications*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
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
              <path
                  d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537-1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1a8.523 8.523 0 0 0-1.874-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 110 7 3.5 3.5 0 010-7z" />
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

  {{-- ===== WRAPPER KONTEN ===== --}}
  <div id="pageWrapper" class="transition-all duration-300 ml-0">
    {{-- NAVBAR --}}
    <header class="sticky top-0 z-30 bg-[#8D2121] backdrop-blur border-b">
      <div class="{{ $container }} py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
          {{-- hamburger untuk mobile --}}
          <button id="sidebarOpenBtn"
                  class="p-2 rounded-xl border border-red-200 text-red-50 hover:bg-red-700/50 md:hidden"
                  title="Buka Sidebar" aria-label="Buka Sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M3 3h18v2H3V3zm0 6h18v2H3V9zm0 6h18v2H3v-2z"/>
            </svg>
          </button>
          <span class="text-lg md:text-xl font-semibold text-white select-none">Notifikasi</span>
        </div>

        <div class="hidden md:flex items-center gap-3 pl-4 border-l border-white/30">
          <img src="{{ $avatarUrl }}" class="w-8 h-8 rounded-full object-cover bg-white" alt="Avatar"
               loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='{{ $fallbackSvg }}';">
          <div class="leading-tight">
            <div class="text-[13px] font-semibold text-white truncate max-w-[140px]">{{ $me?->name ?? ($me?->username ?? 'User') }}</div>
            <div class="text-[11px] text-white font-light">{{ $roleLabel }}</div>
          </div>
        </div>
      </div>
    </header>

    {{-- HEADER: badge total & tombol tandai semua --}}
    <div class="{{ $container }} pt-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        @if($unreadCount > 0)
          <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 rounded-full bg-[#7A1C1C] text-white text-xs px-2">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
          </span>
          <span class="text-sm text-gray-700">Notifikasi belum terbaca</span>
        @else
          <span class="text-sm text-gray-600">Tidak ada notifikasi baru.</span>
        @endif
      </div>

      @if($filtered->count() > 0)
        <form method="POST" action="{{ route('kd.notifications.readAll') }}">
          @csrf
          <button class="text-xs md:text-sm rounded-lg border px-3 py-1 bg-white hover:bg-red-50 border-red-200 text-[#7A1C1C]">
            Tandai semua terbaca
          </button>
        </form>
      @endif
    </div>

    {{-- DAFTAR NOTIFIKASI, GROUP BY TANGGAL --}}
    <div class="{{ $container }} pb-10 pt-4">
      @if($groupedByDate->isEmpty())
        <div class="py-12 text-center text-sm text-gray-600">
          Belum ada notifikasi terkait project dari DIG/IT.
        </div>
      @else
        <div class="space-y-6">
          @foreach($groupedByDate as $dateKey => $items)
            @php
              $dt      = Carbon::parse($dateKey, 'Asia/Jakarta');
              $isToday = $dt->isSameDay($nowJak);
              $label   = $isToday ? 'Hari Ini' : $dt->translatedFormat('d M Y');
            @endphp

            <section>
              <div class="mb-3 flex items-center gap-2">
                <h2 class="text-base font-semibold">{{ $label }}</h2>
              </div>

              <div class="space-y-3">
                @foreach($items as $n)
                  @php
                    $d        = $n->data ?? [];
                    $type     = $norm(data_get($d,'type'));
                    $pName    = data_get($d,'project_name', 'Project');
                    $pId      = data_get($d,'project_id');
                    $decision = $norm(data_get($d,'decision'));
                    $statusLabel = data_get($d,'status_label');
                    $headline = data_get($d,'message', '');

                    $created  = optional($n->created_at)->timezone('Asia/Jakarta');
                    $dateText = $created ? $created->translatedFormat('d M Y') : '-';
                    $timeText = $created ? $created->format('H.i') : '-';
                    $isUnread = is_null($n->read_at);

                    // Map tampilan per type
                    if ($type === 'dig_project_created') {
                      $title = 'DIG membuat project baru';
                      $badgeText = 'Project baru (DIG)';
                      $badgeCls  = 'bg-blue-100 text-blue-700';
                    } elseif ($type === 'it_project_created') {
                      $title = 'IT membuat project baru';
                      $badgeText = 'Project baru (IT)';
                      $badgeCls  = 'bg-sky-100 text-sky-700';
                    } elseif ($type === 'dig_completion_decision') {
                      $isMeet = ($decision === 'memenuhi');
                      $title = 'DIG mengonfirmasi penyelesaian project';
                      $badgeText = $statusLabel ?: ($isMeet ? 'Project selesai, Memenuhi' : 'Project selesai, Tidak Memenuhi');
                      $badgeCls  = $isMeet
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700';
                    } else {
                      $title = 'Notifikasi';
                      $badgeText = null;
                      $badgeCls = '';
                    }
                  @endphp

                  <div class="rounded-xl px-5 py-4 border {{ $isUnread ? 'border-[#7A1C1C] bg-[#F2DCDC]' : 'border-[#E7C9C9] bg-white' }}">
                    <div class="flex items-start justify-between gap-4">
                      <div class="min-w-0">
                        <div class="text-[15px] font-semibold">{{ $title }}</div>
                        <div class="mt-1 text-sm">
                          <div><span class="font-semibold">Nama Project :</span> {{ $pName }}</div>
                          <div class="mt-1 text-xs text-gray-600">
                            {{ $dateText }} • {{ $timeText }} WIB
                          </div>
                          @if($headline)
                            <div class="mt-2 text-sm text-gray-700">{{ $headline }}</div>
                          @endif
                        </div>
                        @if($badgeText)
                          <div class="mt-2">
                            <span class="inline-flex items-center text-[11px] px-2 py-0.5 rounded-full font-semibold {{ $badgeCls }}">
                              {{ $badgeText }}
                            </span>
                          </div>
                        @endif
                      </div>

                      {{-- (opsional) tombol "lihat project" kalau ada route & id --}}
                      @if(function_exists('route') && Route::has('semua.progresses') && $pId)
                        <div class="shrink-0 text-right">
                          <div class="text-xs text-gray-600">{{ $timeText }}</div>
                          {{-- kalau ada halaman detail project khusus KD, ganti route ini --}}
                          <a href="{{ route('semua.progresses') }}#project-{{ $pId }}"
                             class="mt-2 inline-flex text-xs underline text-[#7A1C1C]">
                            Lihat Project
                          </a>
                        </div>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            </section>
          @endforeach
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
  {{-- ===== SCRIPTS (handle sidebar) – sama kayak halaman KD lain ===== --}}
  <script>
    const sidebar      = document.getElementById('sidebar');
    const sidebarClose = document.getElementById('sidebarCloseBtn');
    const sbBackdrop   = document.getElementById('sidebarBackdrop');
    const pageWrapper  = document.getElementById('pageWrapper');
    const railLogo     = document.getElementById('railLogoBtn');
    const sidebarOpen  = document.getElementById('sidebarOpenBtn'); // tombol hamburger mobile

    const add = (el, ...cls) => el && el.classList.add(...cls);
    const rm  = (el, ...cls) => el && el.classList.remove(...cls);

    // key khusus KD supaya persist antar halaman KD
    const SIDEBAR_OPEN_KEY = 'kd.sidebar.open';

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
    sidebarClose && sidebarClose.addEventListener('click', closeSidebar);
    sbBackdrop  && sbBackdrop.addEventListener('click', closeSidebar);
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
