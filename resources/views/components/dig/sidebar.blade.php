{{-- Full Sidebar (Drawer) - Untuk Mobile & Desktop --}}
{{-- BACKDROP (mobile) --}}
<div id="sidebarBackdrop" class="hidden fixed inset-0 z-40 bg-black/30 md:hidden"></div>

{{-- SIDEBAR PENUH (drawer) --}}
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
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-none"
                fill="{{ request()->routeIs('dig.dashboard') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
            </svg>
            <span>Dashboard</span>
        </a>

        {{-- PROGRESS --}}
        <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Project</div>
        <a href="{{ route('semua.progresses') }}"
            class="flex items-center gap-3 px-5 py-2.5 rounded-xl
              {{ request()->routeIs('semua.progresses*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-none"
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
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-none"
                fill="{{ request()->routeIs('dig.notifications*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
                <path
                    d="M12 24a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 24zm6.36-6V11c0-3.07-1.64-5.64-4.5-6.32V4a1.86 1.86 0 1 0-3.72 0v.68C7.28 5.36 5.64 7.92 5.64 11v7L4 19v1h16v-1l-1.64-1z" />
            </svg>
            <span>Notifikasi</span>
        </a>

        {{-- ARSIP --}}
        <div class="px-5 text-[11px] uppercase tracking-wider text-gray-400 mt-5 mb-1">Arsip</div>
        <a href="{{ route('semua.arsip') }}"
            class="flex items-center gap-3 px-5 py-2.5 rounded-xl
              {{ request()->routeIs('semua.arsip*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'hover:bg-[#FFF2F2] text-gray-800' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-none"
                fill="{{ request()->routeIs('semua.arsip*') ? '#7A1C1C' : 'black' }}" viewBox="0 0 24 24">
                <path d="M3 3h18v4H3V3zm1 4h16v14H4V7zm2 2v10h12V9H6zm3 3h6v2H9v-2z" />
            </svg>
            <span>Arsip</span>
        </a>
    </nav>

    <div class="border-t p-3 bg-white text-sm">
        <a href="{{ route('account.setting') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl
              {{ request()->routeIs('account.setting*') ? 'bg-[#FFF2F2] text-[#7A1C1C] font-semibold' : 'text-gray-800 hover:bg-[#FFF2F2]' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-none" viewBox="0 0 24 24"
                fill="currentColor">
                <path
                    d="M11.983 1.25c-.455 0-.83.325-.91.774l-.303 1.71a8.518 8.518 0 0 0-1.874.77l-1.537-1.1a.916.916 0 0 0-1.14.08L4.02 4.684a.916.916 0 0 0-.08 1.14l1.1 1.537a8.523 8.523 0 0 0-.77 1.874l-1.71.303a.916.916 0 0 0-.774.91v1.92c0 .455.325.83.774.91l1.71.303a8.518 8.518 0 0 0 .77 1.874l-1.1 1.537a.916.916 0 0 0 .08 1.14l1.199 1.199a.916.916 0 0 0 1.14.08l1.537-1.1c.6.35 1.22.6 1.87.77l.303 1.71c.08.449.455.774.91.774h1.92c.455 0 .83-.325.91-.774l.303-1.71a8.518 8.518 0 0 0 1.874-.77l1.537 1.1a.916.916 0 0 0 1.14-.08l1.199-1.199a.916.916 0 0 0 .08-1.14l-1.1-1.537a8.523 8.523 0 0 0 .77-1.874l1.71-.303a.916.916 0 0 0 .774-.91v-1.92a.916.916 0 0 0-.774-.91l-1.71-.303a8.518 8.518 0 0 0-.77-1.874l1.1-1.537a.916.916 0 0 0-.08-1.14L18.8 3.4a.916.916 0 0 0-1.14-.08l-1.54 1.1a8.523 8.523 0 0 0-1.874-.77l-.3-1.71a.916.916 0 0 0-.91-.77h-1.92zM12 8.5a3.5 3.5 0 110 7 3.5 3.5 0 010-7z" />
            </svg>
            <span>Pengaturan Akun</span>
        </a>

        <a href="/logout" data-confirm-logout="true"
            class="flex items-center gap-3 px-3 py-2 rounded-xl transition hover:bg-[#FFF2F2] text-gray-900"
            title="Log Out" aria-label="Log Out">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-none" fill="black"
                viewBox="0 0 24 24">
                <path
                    d="M3 3h10a1 1 0 0 1 1 1v5h-2V5H5v14h7v-4h2v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
                <path d="M14 12l5-5v3h4v4h-4v3l-5-5z" />
            </svg>
            <span>Log Out</span>
        </a>
    </div>
</aside>
