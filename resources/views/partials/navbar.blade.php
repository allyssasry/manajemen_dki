{{-- 
    Navbar Component
    Variables: $pageTitle, $container, $me, $roleLabel, $avatarUrl, $fallbackSvg
--}}
<header class="sticky top-0 z-30 bg-[#8D2121]">
    <div class="{{ $container }} py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            {{-- Mobile Sidebar Toggle --}}
            <button id="sidebarOpenBtn"
                class="p-2 rounded-xl border border-red-200 text-red-50 hover:bg-red-700/20 md:hidden"
                title="Buka Sidebar" aria-label="Buka Sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path d="M3 3h18v2H3V3zm0 6h18v2H3V9zm0 6h18v2H3v-2z" />
                </svg>
            </button>
            <span class="text-lg md:text-xl font-bold text-white select-none">{{ $pageTitle }}</span>
        </div>

        {{-- User Profile --}}
        <div class="flex items-center gap-3 pl-4 border-l border-white/30">
            <img src="{{ $avatarUrl }}" class="w-8 h-8 rounded-full object-cover bg-white" alt="Avatar"
                loading="lazy" referrerpolicy="no-referrer"
                onerror="this.onerror=null;this.src='{{ $fallbackSvg }}';">
            <div class="leading-tight hidden md:block">
                <div class="text-[13px] font-semibold text-white max-w-[160px] truncate">
                    {{ $me?->name ?? ($me?->username ?? 'User') }}</div>
                <div class="text-[11px] text-white/80">{{ $roleLabel }}</div>
            </div>
        </div>
    </div>
</header>
