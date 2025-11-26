{{-- resources/views/account/settings.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Pengaturan Akun</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    html { scrollbar-gutter: stable; }
    body { overflow-x: hidden; background:#F8ECEC }

    .ring-theme { box-shadow: 0 0 0 2px #7A1C1C inset; }
    .readonly .editable-field { background:#F5EAEA; cursor:not-allowed; }
    .readonly #avatarLabel { pointer-events:none; opacity:.6; }

    /* Utility untuk matikan transisi sementara (dipakai di firstPaint) */
    .no-transition,
    .no-transition * {
      transition: none !important;
    }
  </style>
</head>
<body class="min-h-screen bg-[#FFFFFF] text-gray-900">

@php
  $me   = auth()->user();
  $role = $me?->role;
  $roleLabel = $role === 'it' ? 'IT' : ($role === 'digital_banking' ? 'DIG' : ($role === 'supervisor' ? 'Supervisor' : 'User'));

  // ===== Avatar fallback
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

  // ===== Rute dashboard dinamis per role
  $homeRouteName = match ($role) {
      'it'              => (\Route::has('it.dashboard') ? 'it.dashboard' : null),
      'supervisor'      => (\Route::has('supervisor.dashboard') ? 'supervisor.dashboard' : null),
      'digital_banking' => (\Route::has('dig.dashboard') ? 'dig.dashboard' : null),
      default           => (\Route::has('dig.dashboard') ? 'dig.dashboard' : null),
  };
  $homeUrl = $homeRouteName ? route($homeRouteName) : url('/');

  // Untuk state "aktif" di menu dashboard (menyesuaikan nama route per role)
  $isDashboardActive = $homeRouteName
      ? request()->routeIs($homeRouteName)
      : url()->current() === $homeUrl;

  // Notifikasi route per role
  $notifRoute = match ($role) {
      'it'              => 'it.notifications',
      'supervisor'      => 'supervisor.notifications',
      default           => 'dig.notifications',
  };
@endphp

{{-- ====== MINI RAIL (ikon) ====== --}}
<aside id="miniSidebar"
  class="hidden md:flex fixed inset-y-0 left-0 z-40 w-16 bg-white border-r shadow-xl flex-col items-center justify-between py-4">
  <div class="flex flex-col items-center gap-6">
    <button id="railLogoBtn" type="button" title="Buka Sidebar" aria-label="Buka Sidebar"
            class="rounded-xl p-2 hover:bg-[#FFF2F2] cursor-pointer">
      <img src="{{ asset('images/dki.png') }}" class="h-6 w-auto object-contain" alt="Logo" />
    </button>

    {{-- DASHBOARD (dinamis per role) --}}
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
        <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537-1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1c-.6-.35-1.22-.6-1.87-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"/>
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

{{-- ============== SIDEBAR (penuh) ============== --}}
<div id="sidebarBackdrop" class="hidden fixed inset-0 z-40 bg-black/30 md:hidden"></div>

<aside id="sidebar"
  class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] -translate-x-full transition-transform duration-300 ease-out
         bg-white border-r shadow-xl flex flex-col">

  @php
    // ulangi variabel avatar untuk blade section ini jika diperlukan
    $me = $me ?? auth()->user()->fresh();
    $role = $me?->role;
    $roleLabel = $role === 'it' ? 'IT' : ($role === 'digital_banking' ? 'DIG' : ($role === 'supervisor' ? 'Supervisor' : 'User'));
  @endphp

  <div class="px-5 pt-5 pb-4 border-b">
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
    {{-- DASHBOARD (dinamis per role) --}}
    <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-3 mb-1">Dashboard</div>
    <a href="{{ $homeUrl }}"
       class="flex items-center gap-3 px-5 py-2.5 transition-all duration-150 rounded-xl
              {{ $isDashboardActive ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
           fill="{{ $isDashboardActive ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
        <path d="M3 12l9-9 9 9v9a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-9z" />
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

    {{-- NOTIFIKASI (dinamis per role) --}}
    <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Notifikasi</div>
    @php $notifHas = \Route::has($notifRoute); @endphp
    @if($notifHas)
      <a href="{{ route($notifRoute) }}"
         class="flex items-center gap-3 px-5 py-2.5 transition-all duration-150 rounded-xl
                {{ request()->routeIs($notifRoute.'*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none"
             fill="{{ request()->routeIs($notifRoute.'*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
          <path d="M12 24a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z"/>
        </svg>
        <span>Notifikasi</span>
      </a>
    @endif

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
        <path d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537-1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1c-.6-.35-1.22-.6-1.87-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z"/>
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

{{-- ====== PAGE WRAPPER ====== --}}
<div id="pageWrapper" class="transition-all duration-300 ml-0">
  {{-- NAVBAR --}}
  <header class="sticky top-0 z-30 bg-[#8D2121]">
    <div class="max-w-6xl mx-auto px-5 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <button id="sidebarOpenBtn" class="p-2 rounded-xl border border-red-200 text-red-50 hover:bg-red-700/50 md:hidden" title="Buka Sidebar">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 3h18v2H3V3zm0 6h18v2H3V9zm0 6h18v2H3v-2z"/>
          </svg>
        </button>
        <span class="text-lg md:text-xl font-bold text-white select-none">Pengaturan Akun</span>
      </div>

      <div class="flex items-center gap-3 pl-4 border-l border-white/20">
        <img src="{{ $avatarUrl }}" class="w-8 h-8 rounded-full object-cover bg-white"
             alt="Avatar" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='{{ $fallbackSvg }}';">
        <div class="leading-tight hidden md:block">
          <div class="text-[13px] font-semibold text-white max-w-[160px] truncate">{{ $me?->name ?? ($me?->username ?? 'User') }}</div>
          <div class="text-[11px] text-white/90">{{ $roleLabel }}</div>
        </div>
      </div>
    </div>
  </header>

  {{-- KONTEN --}}
  <main class="max-w-6xl mx-auto px-5 py-6">
    <div id="accountCard" class="readonly rounded-2xl border border-[#C89898] bg-[#FFF5F5] p-5">
      <div class="flex items-start justify-between mb-4">
        <h2 class="text-base font-semibold">Informasi Personal</h2>
        <button type="button" id="toggleEditBtn"
                class="px-3 py-1.5 rounded-full bg-emerald-600 text-white text-xs font-semibold hover:opacity-90">
          Edit
        </button>
      </div>

      <form id="accountForm" action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 md:grid-cols-12 gap-6">
        @csrf @method('PUT')

        {{-- KOLOM KIRI --}}
        <div class="md:col-span-7 space-y-4">
          @php
            $fg = $me?->first_name ?: (explode(' ', $me?->name ?? '')[0] ?? '');
            $lg = $me?->last_name  ?: (trim(implode(' ', array_slice(explode(' ', $me?->name ?? ''),1))) ?: '');
          @endphp
          @foreach ([
            ['label'=>'Nama Awalan','name'=>'first_name','value'=>$fg],
            ['label'=>'Nama Terakhir','name'=>'last_name','value'=>$lg],
            ['label'=>'Username','name'=>'username','value'=>$me?->username],
            ['label'=>'Email','name'=>'email','value'=>$me?->email],
            ['label'=>'Alamat','name'=>'address','value'=>$me?->address],
            ['label'=>'No. Telepon','name'=>'phone','value'=>$me?->phone],
          ] as $f)
            <div>
              <label class="block text-xs mb-1">{{ $f['label'] }}:</label>
              <input type="text" name="{{ $f['name'] }}" value="{{ old($f['name'], $f['value']) }}"
                     class="editable-field w-full rounded-xl bg-white border border-[#C89898] px-4 py-2.5 outline-none focus:ring-2 focus:ring-[#7A1C1C]/30">
            </div>
          @endforeach

          <div id="formActions" class="pt-2 flex gap-3 hidden">
            <button type="submit"
              class="inline-flex justify-center min-w-[160px] h-10 items-center rounded-full border-2 border-[#7A1C1C] bg-[#E2B9B9] hover:bg-[#D9AFAF] text-black text-sm font-semibold">
              Simpan Perubahan
            </button>
            <button type="button" id="cancelEditBtn"
              class="inline-flex justify-center min-w-[160px] h-10 items-center rounded-full border-2 border-[#7A1C1C] bg-white text-[#7A1C1C] text-sm font-semibold">
              Batalkan Perubahan
            </button>
          </div>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="md:col-span-5">
          <div class="rounded-2xl overflow-hidden bg-white shadow-sm border border-[#C89898]">
            <img id="avatarPreview" src="{{ $avatarUrl }}" alt="Foto Profil"
                 class="w-full aspect-[4/3] object-cover" onerror="this.onerror=null;this.src='{{ $fallbackSvg }}';">
          </div>

          <label id="avatarLabel" class="mt-3 block">
            <input id="avatarInput" type="file" name="avatar" accept="image/*" class="hidden editable-field-file">
            <span class="inline-flex items-center justify-center w-full rounded-full bg-[#7A1C1C] text-white px-4 py-2 text-sm font-semibold shadow hover:opacity-95 cursor-pointer">
              Ubah Foto Profil
            </span>
          </label>
          <p class="text-[11px] text-gray-600 mt-2 text-center">*Upload PNG, JPG, JPEG. Maks 2MB</p>
        </div>
      </form>
    </div>
  </main>
</div>

{{-- ====== SCRIPT ====== --}}
<script>
  // ==== SIDEBAR (disamakan dengan Notifikasi IT) ====
  const sidebar      = document.getElementById('sidebar');
  const sidebarClose = document.getElementById('sidebarCloseBtn');
  const sbBackdrop   = document.getElementById('sidebarBackdrop');
  const pageWrapper  = document.getElementById('pageWrapper');
  const railLogo     = document.getElementById('railLogoBtn');
  const sidebarOpen  = document.getElementById('sidebarOpenBtn'); // tombol hamburger mobile

  const add = (el, ...cls) => el && el.classList.add(...cls);
  const rm  = (el, ...cls) => el && el.classList.remove(...cls);

  // Pakai key yang sama seperti halaman IT Notifikasi
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

  // ==== TOGGLE MODE EDIT FORM ====
  const card = document.getElementById('accountCard');
  const form = document.getElementById('accountForm');
  const editBtn = document.getElementById('toggleEditBtn');
  const cancelBtn = document.getElementById('cancelEditBtn');
  const actions = document.getElementById('formActions');
  const fields = form.querySelectorAll('.editable-field');
  const fileInput = form.querySelector('.editable-field-file');
  const avatar = document.getElementById('avatarPreview');
  let editing = false;

  function setEditing(state) {
    editing = state;
    fields.forEach(f => f.disabled = !state);
    if (fileInput) fileInput.disabled = !state;
    card.classList.toggle('readonly', !state);
    actions.classList.toggle('hidden', !state);
    if (editBtn) editBtn.style.display = state ? 'none' : 'inline-block';
  }
  setEditing(false);

  editBtn?.addEventListener('click', () => setEditing(true));
  cancelBtn?.addEventListener('click', () => {
    form.reset();
    avatar.src = @json($avatarUrl);
    setEditing(false);
  });

  const input = document.getElementById('avatarInput');
  input?.addEventListener('change', e => {
    if (!editing) return;
    const file = e.target.files?.[0];
    if (file) avatar.src = URL.createObjectURL(file);
  });
</script>
</body>
</html>
