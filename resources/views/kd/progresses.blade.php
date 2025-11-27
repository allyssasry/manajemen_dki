{{-- resources/views/kd/progresses.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Progress | Kepala Divisi</title>
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

<body class="min-h-screen bg-[#FFFFFF] text-gray-900">
  {{-- seed kelas agar Tailwind memuat util & jaga margin saat sidebar terbuka --}}
  <div class="hidden ml-72 md:ml-72"></div>

  @php
    // ====== USER & AVATAR ======
    $me        = $me ?? auth()->user()?->fresh();
    $role      = $me?->role;
    $roleLabel = $role === 'it' ? 'IT' : ($role === 'digital_banking' ? 'DIG' : ($role === 'kepala_divisi' ? 'Kepala Divisi' : 'User'));

    $initial = urlencode(mb_substr($me?->name ?? $me?->username ?? 'U', 0, 1));
    $fallbackSvg = "data:image/svg+xml;utf8,".rawurlencode(
      '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">'.
      '<rect width="100%" height="100%" rx="12" ry="12" fill="#7A1C1C"/>'.
      '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="28" fill="#fff">'.$initial.'</text>'.
      '</svg>'
    );
    $rawUrl    = $me?->avatar_url_public;
    $extraKey  = $me?->avatar_cache_key ?? ($me?->updated_at?->timestamp ?? time());
    $avatarUrl = $rawUrl ? ($rawUrl.(str_contains($rawUrl,'?') ? '&' : '?').'ck='.$extraKey) : $fallbackSvg;

    // ====== LAYOUT CONTAINER (samakan dengan DIG) ======
    $container = 'max-w-6xl mx-auto w-full px-5';

    // ====== TAB FILTER ======
    $q = $status ?? request('status','all'); // all | in_progress | done
    $tabCls = fn($v) => $q===$v ? 'bg-[#7A1C1C] text-white'
                                : 'bg-white text-[#7A1C1C] hover:bg-[#FFF2F2]';
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
         title="Progress" aria-label="Progress">
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
          <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1c-.6-.35-1.22-.6-1.87-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"/>
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
                      d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1c-.6-.35-1.22-.6-1.87-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 110 7 3.5 3.5 0 010-7z" />
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
    <header class="sticky top-0 z-30 bg-[#8D2121] backdrop-blur">
      <div class="{{ $container }} py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
          {{-- Tombol open sidebar (mobile) --}}
          <button id="sidebarOpenBtn"
                  class="p-2 rounded-xl border border-red-200 text-red-700 md:hidden"
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

    {{-- ================== KONTEN KD (ISI TETAP) ================== --}}
    <main class="{{ $container }} pb-10">
      {{-- Filter tab --}}
      <div class="mt-3 flex gap-3">
        <a href="{{ route('kd.progresses',['status'=>'all']) }}"
           class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tabCls('all') }} grid place-items-center">Semua</a>
        <a href="{{ route('kd.progresses',['status'=>'in_progress']) }}"
           class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tabCls('in_progress') }} grid place-items-center">Dalam Proses</a>
        <a href="{{ route('kd.progresses',['status'=>'done']) }}"
           class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tabCls('done') }} grid place-items-center">Telah Selesai</a>
      </div>

      {{-- DAFTAR PROJECT --}}
      @forelse($projects as $project)
        @php
          $latestPercents = [];
          foreach ($project->progresses as $pr) {
            $u = $pr->updates->sortByDesc('update_date')->first();
            $latestPercents[] = $u ? (int)($u->percent ?? $u->progress_percent ?? 0) : 0;
          }
          $realization = count($latestPercents) ? (int) round(array_sum($latestPercents)/max(count($latestPercents),1)) : 0;
          $size=88; $stroke=10; $r=$size/2-$stroke; $circ=2*M_PI*$r; $off=$circ*(1-$realization/100);

          $allMetAndConfirmed = $project->progresses->every(function ($p) {
            $u = $p->updates->sortByDesc('update_date')->first();
            $real = $u ? (int)($u->percent ?? $u->progress_percent ?? 0) : 0;
            return $real >= (int)$p->desired_percent && !is_null($p->confirmed_at);
          });

          $finishedAt = $allMetAndConfirmed
            ? optional($project->progresses->max('confirmed_at'))->timezone('Asia/Jakarta')
            : null;
        @endphp

        <section class="mt-6 rounded-2xl border-2 border-[#7A1C1C] bg-[#FFFAFA] p-5">
          <div class="flex items-center justify-between text-xs font-semibold mb-2">
            <span class="{{ $allMetAndConfirmed ? 'text-green-700' : 'text-[#7A1C1C]' }}">
              {{ $allMetAndConfirmed ? 'Project Telah Selesai' : 'Dalam Proses' }}
            </span>
            <span class="text-gray-600">
              {{ $finishedAt ? $finishedAt->translatedFormat('d F Y') : '' }}
            </span>
          </div>

          <div class="grid md:grid-cols-[auto,1fr] items-start gap-5">
            <div class="flex items-center gap-4">
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
                <span class="text-gray-700">Nama Project</span><span>:</span>
                <span class="font-semibold ">{{ $project->name }}</span>
              </div>
              <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                <span class="text-gray-700">Penanggung Jawab (DIG)</span><span>:</span>
                <span>{{ $project->digitalBanking->name ?? '-' }}</span>
              </div>
              <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                <span class="text-gray-700">Penanggung Jawab (IT)</span><span>:</span>
                <span>{{ $project->developer->name ?? '-' }}</span>
              </div>
              <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                <span class="text-gray-700">Deskripsi</span><span>:</span>
                <span>{{ $project->description ?: '-' }}</span>
              </div>
            </div>
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
                  
          <div class="mt-4">
            <div class="scroll-thin grid md:grid-cols-2 gap-4 max-h-[240px] overflow-y-auto pr-1">
              @forelse($project->progresses as $idx => $pr)
                @php
                  $last    = $pr->updates->sortByDesc('update_date')->first();
                  $real    = $last ? (int)($last->percent ?? $last->progress_percent ?? 0) : 0;
                  $creator = $pr->creator;
                  $ownerRoleLabel = $creator?->role === 'digital_banking' ? 'DIG' : ($creator?->role === 'it' ? 'IT' : '—');
                @endphp
                {{-- card progress --}}
                <div class="rounded-xl border border-[#7A1C1C] bg-[#8D2121] p-4 text-white">
                  <div class="font-semibold mb-2">Progress {{ $idx+1 }} — {{ $pr->name }}</div>
                  <div class="text-sm grid gap-1">
                    <div><span class="inline-block w-36">Timeline Mulai</span>: {{ $pr->start_date }}</div>
                    <div><span class="inline-block w-36">Timeline Selesai</span>: {{ $pr->end_date }}</div>
                    <div><span class="inline-block w-36">Target Progress</span>: {{ $pr->desired_percent }}%</div>
                    <div><span class="inline-block w-36">Realisasi Progress</span>: {{ $real }}%</div>
                    <div class="text-xs mt-1">
                      *Dibuat oleh <strong>{{ $ownerRoleLabel }}</strong> — {{ $creator?->name ?? '—' }}
                    </div>
                  </div>
                </div>
              @empty
                <div class="col-span-2 text-sm text-white/80">Belum ada progress.</div>
              @endforelse
            </div>
          </div>

          <div class="mt-4 flex justify-end">
            <a href="{{ route('kd.projects.show', $project->id) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-[#7A1C1C] px-3 py-1.5 text-xs font-semibold text-[#7A1C1C] bg-white hover:bg-[#FFF2F2]">
              Detail Informasi
            </a>
          </div>
        </section>
      @empty
        <div class="mt-6">
          <div class="bg-[#EBD0D0] rounded-2xl px-6 py-8 flex items-center justify-center">
            <div class="rounded-2xl bg-[#CFA8A8] px-5 py-3 text-white/95">Tidak ada project untuk ditampilkan</div>
          </div>
        </div>
      @endforelse
    </main>
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
  {{-- ===== SCRIPTS (handle sidebar) – DISAMAKAN DENGAN ACCOUNT SETTINGS ===== --}}
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
