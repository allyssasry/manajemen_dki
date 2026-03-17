{{--
    Layout Simple untuk Form Pages
    ------------------------------------------
    Layout untuk halaman form tanpa sidebar (project create/edit, dll)
    
    Sections:
    - @section('title', 'Judul')
    - @section('content')
    - @section('scripts')
--}}
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Form')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Poppins", sans-serif; }
        html { scrollbar-gutter: stable; }
        .scroll-thin::-webkit-scrollbar { width: 6px; height: 6px; }
        .scroll-thin::-webkit-scrollbar-thumb { background: #c89898; border-radius: 9999px; }
        .scroll-thin::-webkit-scrollbar-track { background: transparent; }
    </style>
    
    @yield('headStyles')
</head>

<body class="min-h-screen bg-[#FFFAFA] text-gray-900">
    @php
        $container = 'max-w-5xl mx-auto w-full px-5 md:px-8';
        $meUser = auth()->user();
    @endphp

    {{-- Simple Header --}}
    <header class="w-full bg-[#8D2121] text-white">
        <div class="{{ $container }} flex items-center justify-between py-3">
            <span class="font-semibold text-sm md:text-base tracking-wide">
                Bank Jakarta — Satuan Kerja Digital Banking
            </span>

            <div class="flex items-center gap-2">
                <div class="text-right text-xs leading-tight hidden sm:block">
                    <div class="font-semibold">{{ $meUser->name ?? 'User' }}</div>
                    <div class="text-white/80 text-[11px]">{{ strtoupper($meUser->role ?? '-') }}</div>
                </div>
                <div class="w-8 h-8 rounded-full bg-white/20 border border-white/40 overflow-hidden flex items-center justify-center text-xs">
                    {{ mb_substr($meUser->name ?? ($meUser->username ?? 'U'), 0, 1) }}
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="py-7">
        <div class="{{ $container }}">
            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>
