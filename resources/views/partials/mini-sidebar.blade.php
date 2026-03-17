{{-- 
    Mini Sidebar (Rail Icon) - Desktop Only
    Variables: $homeUrl, $isDashboardActive, $notifRoute, $progressRoute, $isProgressActive
--}}
<aside id="miniSidebar"
    class="hidden md:flex fixed inset-y-0 left-0 z-40 w-16 bg-white border-r shadow-xl flex-col items-center justify-between py-4">

    <div class="flex flex-col items-center gap-6">
        {{-- Logo / buka sidebar penuh --}}
        <button id="railLogoBtn" type="button" title="Buka Sidebar" aria-label="Buka Sidebar"
            class="rounded-xl p-2 hover:bg-[#FFF2F2] cursor-pointer">
            <img src="{{ asset('images/dki.png') }}" class="h-6 w-auto object-contain" alt="Logo" />
        </button>

        {{-- DASHBOARD --}}
        <a href="{{ $homeUrl }}"
            class="p-2 rounded-lg {{ $isDashboardActive ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
            title="Dashboard" aria-label="Dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                fill="{{ $isDashboardActive ? '#7A1C1C' : 'currentColor' }}" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
            </svg>
        </a>

        {{-- PROGRESS / PROJECT --}}
        <a href="{{ route($progressRoute ?? 'semua.progresses') }}"
            class="p-2 rounded-lg {{ ($isProgressActive ?? false) ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
            title="Project" aria-label="Project">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                fill="{{ ($isProgressActive ?? false) ? '#7A1C1C' : 'currentColor' }}"
                viewBox="0 0 24 24">
                <path d="M4 22h16V2H4v20zm3-5h2v3H7v-3zm4-7h2v10h-2V10zm4 3h2v7h-2v-7z" />
            </svg>
        </a>

        {{-- NOTIFIKASI --}}
        @if(Route::has($notifRoute))
        <a href="{{ route($notifRoute) }}"
            class="relative p-2 rounded-lg {{ request()->routeIs($notifRoute . '*') ? 'bg-[#FFF2F2] text-[#7A1C1C] border border-red-200' : 'text-gray-800 hover:bg-[#FFF2F2]' }}"
            title="Notifikasi" aria-label="Notifikasi">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                fill="{{ request()->routeIs($notifRoute . '*') ? '#7A1C1C' : 'currentColor' }}"
                viewBox="0 0 24 24">
                <path
                    d="M12 24a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z" />
            </svg>
            @if(($unreadNotifCount ?? 0) > 0)
                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full bg-[#7A1C1C] text-white text-[10px] font-bold px-1">
                    {{ ($unreadNotifCount ?? 0) > 99 ? '99+' : ($unreadNotifCount ?? 0) }}
                </span>
            @endif
        </a>
        @endif

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
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-none" viewBox="0 0 24 24"
                fill="currentColor">
                <path
                    d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1c-.6-.35-1.22-.6-1.87-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 110 7 3.5 3.5 0 010-7z" />
            </svg>
        </a>

        {{-- LOGOUT --}}
        <a href="/logout" data-confirm-logout="true" class="p-2 rounded-lg hover:bg-[#FFF2F2]" title="Log Out"
            aria-label="Log Out">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="black">
                <path
                    d="M3 3h10a1 1 0 0 1 1 1v5h-2V5H5v14h7v-4h2v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
                <path d="M14 12l5-5v3h4v4h-4v3l-5-5z" />
            </svg>
        </a>
    </div>
</aside>
