{{--
    Layout Utama untuk Aplikasi
    ------------------------------------------
    Digunakan oleh semua halaman dengan sidebar
    
    Sections yang tersedia:
    - @section('title', 'Judul Halaman')
    - @section('pageTitle', 'Judul Navbar')
    - @section('headStyles') - CSS tambahan (optional)
    - @section('headScripts') - JS di head (optional) 
    - @section('content') - konten utama halaman
    - @section('scripts') - JS di akhir body (optional)
--}}
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Early Sync Script untuk anti-glitch sidebar --}}
    @include('partials.early-sync-script')

    {{-- Base Styles --}}
    @include('partials.base-styles')

    {{-- Additional Head Styles --}}
    @yield('headStyles')

    {{-- Additional Head Scripts --}}
    @yield('headScripts')
</head>

@php
    $container = $container ?? 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';
    $me = $me ?? auth()->user()?->fresh();
    $role = $me?->role;
    
    // Role label untuk display
    $roleLabel = match($role) {
        'it' => 'IT',
        'digital_banking' => 'DIG',
        'supervisor' => 'Supervisor',
        'kepala_divisi' => 'Kepala Divisi',
        default => 'User'
    };

    // Dashboard route berdasarkan role
    $homeRouteName = match($role) {
        'it' => \Route::has('it.dashboard') ? 'it.dashboard' : null,
        'kepala_divisi' => \Route::has('kd.dashboard') ? 'kd.dashboard' : null,
        'supervisor' => \Route::has('supervisor.dashboard') ? 'supervisor.dashboard' : null,
        'digital_banking' => \Route::has('dig.dashboard') ? 'dig.dashboard' : null,
        default => \Route::has('dig.dashboard') ? 'dig.dashboard' : null
    };
    $homeUrl = $homeRouteName ? route($homeRouteName) : url('/');
    $isDashboardActive = $homeRouteName ? request()->routeIs($homeRouteName) : url()->current() === $homeUrl;

    // Notification route berdasarkan role
    $notifRoute = match($role) {
        'it' => 'it.notifications',
        'kepala_divisi' => 'kd.notifications',
        'supervisor' => 'supervisor.notifications',
        default => 'dig.notifications'
    };

    // Progress/Project route berdasarkan role
    $progressRoute = match($role) {
        'kepala_divisi' => \Route::has('kd.progresses') ? 'kd.progresses' : 'semua.progresses',
        default => 'semua.progresses'
    };
    $isProgressActive = request()->routeIs($progressRoute . '*') || request()->routeIs('semua.progresses*');

    // Hitung jumlah notifikasi belum dibaca
    $unreadNotifCount = $me ? $me->unreadNotifications()->count() : 0;

    // Avatar
    $initial = urlencode(mb_substr($me?->name ?? ($me?->username ?? 'U'), 0, 1));
    $fallbackSvg = 'data:image/svg+xml;utf8,' . rawurlencode(
        '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">' .
        '<rect width="100%" height="100%" rx="12" ry="12" fill="#7A1C1C"/>' .
        '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="28" fill="#fff">' .
        $initial . '</text></svg>'
    );
    $rawUrl = $me?->avatar_url_public;
    $extraKey = $me?->avatar_cache_key ?? ($me?->updated_at?->timestamp ?? time());
    $avatarUrl = $rawUrl ? ($rawUrl . (str_contains($rawUrl, '?') ? '&' : '?') . 'ck=' . $extraKey) : $fallbackSvg;
    
    // Page title untuk navbar
    $pageTitle = View::hasSection('pageTitle') ? View::yieldContent('pageTitle') : 'Dashboard';
@endphp

<body class="min-h-screen bg-white text-gray-900">
    {{-- Mini Sidebar (Rail Icon) --}}
    @include('partials.mini-sidebar', [
        'homeUrl' => $homeUrl,
        'isDashboardActive' => $isDashboardActive,
        'notifRoute' => $notifRoute,
        'unreadNotifCount' => $unreadNotifCount,
        'progressRoute' => $progressRoute,
        'isProgressActive' => $isProgressActive
    ])

    {{-- Sidebar Backdrop (Mobile) --}}
    <div id="sidebarBackdrop" class="hidden fixed inset-0 z-40 bg-black/30 md:hidden"></div>

    {{-- Full Sidebar (Drawer) --}}
    @include('partials.sidebar', [
        'homeUrl' => $homeUrl,
        'isDashboardActive' => $isDashboardActive,
        'notifRoute' => $notifRoute,
        'unreadNotifCount' => $unreadNotifCount,
        'progressRoute' => $progressRoute,
        'isProgressActive' => $isProgressActive
    ])

    {{-- Page Wrapper --}}
    <div id="pageWrapper" class="md:ml-16">
        {{-- Navbar --}}
        @include('partials.navbar', [
            'pageTitle' => $pageTitle,
            'container' => $container,
            'me' => $me,
            'roleLabel' => $roleLabel,
            'avatarUrl' => $avatarUrl,
            'fallbackSvg' => $fallbackSvg
        ])

        {{-- Main Content --}}
        <main>
            @yield('content')
        </main>
    </div>

    {{-- Modal Konfirmasi Logout --}}
    @include('partials.logout-modal')

    {{-- Modal Konfirmasi Delete --}}
    @include('partials.delete-modal')

    {{-- Base Scripts (Sidebar Toggle, etc) --}}
    @include('partials.base-scripts')

    {{-- Additional Scripts --}}
    @yield('scripts')
</body>
</html>
