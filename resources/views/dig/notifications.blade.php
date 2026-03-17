@extends('layouts.dashboard')

@section('title', 'Notifikasi DIG')
@section('pageTitle', 'Notifikasi')

@section('content')
    @php
        use App\Models\Project;
        use App\Models\Progress;

        // Container konsisten
        $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';

        /* ========= OLAH DATA NOTIFIKASI UNTUK DIG ========= */
        // Koleksi notif "Hari Ini" (dari controller)
        $todayCollection = $today ?? collect();

        // semua item "sebelum hari ini" (maks 7 hari terakhir dari controller)
        $allItems = isset($notifications) ? collect($notifications->items() ?? []) : collect();

        // hindari duplikasi: buang dari $allItems yang sudah ada di $todayCollection
        $previous = $allItems->reject(function ($n) use ($todayCollection) {
            return $todayCollection->contains('id', $n->id);
        });

        // ==== FILTER: sembunyikan notif yang project/progress-nya sudah dihapus ====
        $combined = $todayCollection->concat($previous);

        // ambil daftar project_id yang muncul di data notif
        $projectIds = $combined
            ->map(function ($n) {
                return data_get($n->data, 'project_id');
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        // ambil daftar progress_id yang muncul di data notif
        $progressIds = $combined
            ->map(function ($n) {
                return data_get($n->data, 'progress_id');
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        // project yang masih eksis di DB
        $existingProjectIds = !empty($projectIds)
            ? Project::whereIn('id', $projectIds)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all()
            : [];

        // progress yang masih eksis di DB
        $existingProgressIds = !empty($progressIds)
            ? Progress::whereIn('id', $progressIds)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all()
            : [];

        // Filter: sembunyikan kalau project/progress sudah dihapus
        $filterExisting = function ($n) use ($existingProjectIds, $existingProgressIds) {
            $pid = (int) (data_get($n->data, 'project_id') ?? 0);
            $prgId = (int) (data_get($n->data, 'progress_id') ?? 0);

            if ($pid === 0 && $prgId === 0) {
                return true;
            }

            if ($pid !== 0 && !in_array($pid, $existingProjectIds, true)) {
                return false;
            }

            if ($prgId !== 0 && !in_array($prgId, $existingProgressIds, true)) {
                return false;
            }

            return true;
        };

        $todayCollection = $todayCollection->filter($filterExisting);
        $previous = $previous->filter($filterExisting);

        // Group previous by date
        $nowJak = \Illuminate\Support\Carbon::now('Asia/Jakarta');
        $groupedByDate = $previous
            ->groupBy(function ($n) use ($nowJak) {
                $dt = optional($n->created_at)->timezone('Asia/Jakarta');
                if (!$dt) {
                    return 'Unknown';
                }
                return $dt->translatedFormat('d M Y');
            })
            ->sortKeysDesc();

        // Hitung unread
        $todayUnread = $todayCollection->whereNull('read_at')->count();
        $totalUnread = $todayUnread + $previous->whereNull('read_at')->count();
        $hasAny = $todayCollection->count() > 0 || $previous->count() > 0;
    @endphp

    {{-- HEADER LIST (badge + tandai semua) --}}
    <div class="{{ $container }} py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            @if ($totalUnread > 0)
                <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 rounded-full bg-[#7A1C1C] text-white text-xs px-2">
                    {{ $totalUnread > 99 ? '99+' : $totalUnread }} belum dibaca
                </span>
            @endif
        </div>
        <form method="POST" action="{{ route('dig.notifications.readAll') }}">
            @csrf
            <button class="text-sm rounded-lg border px-3 py-1 bg-white hover:bg-red-50 border-red-200 text-[#7A1C1C]">
                Tandai semua terbaca
            </button>
        </form>
    </div>

    {{-- LIST NOTIFIKASI --}}
    <main class="{{ $container }} py-6">
        @if (!$hasAny)
            {{-- KALAU BENAR-BENAR TIDAK ADA NOTIFIKASI --}}
            <div class="py-12 text-center text-sm text-gray-600">
                Belum ada notifikasi.
            </div>
        @else
            {{-- HARI INI --}}
            @if ($todayCollection->count() > 0)
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-base font-semibold">Hari Ini</h2>
                        @if ($todayUnread > 0)
                        @endif
                    </div>

                    <div class="space-y-3">
                        @forelse($todayCollection as $n)
                            @php
                                $d = $n->data ?? [];
                                $type = strtolower($d['type'] ?? '');
                                $pName = $d['project_name'] ?? 'Project';
                                $pId = $d['project_id'] ?? null;
                                $progName = $d['progress_name'] ?? 'Progress';
                                $message = $d['message'] ?? '';
                                $late = (bool) ($d['late'] ?? false);
                                $isUnread = is_null($n->read_at);

                                $created = optional($n->created_at)->timezone('Asia/Jakarta');
                                $dateText = $created ? $created->format('d M Y') : '-';
                                $timeText = $created ? $created->format('H.i') : '-';
                            @endphp

                            <div class="rounded-xl px-5 py-4 border {{ $isUnread ? 'border-[#7A1C1C] bg-[#F2DCDC]' : 'border-[#E7C9C9] bg-white' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        @if ($type === 'it_project_created')
                                            <div class="text-[15px] font-semibold">Project Baru dari IT</div>
                                            <div class="mt-1 text-sm">
                                                <div><span class="font-semibold">Nama Project</span>: {{ $pName }}</div>
                                                <div class="mt-1"><span class="font-semibold">Tanggal</span>: {{ $dateText }} • {{ $timeText }} WIB</div>
                                                @if ($message)
                                                    <div class="text-gray-700 mt-1">{{ $message }}</div>
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                    Project baru dari IT
                                                </span>
                                            </div>
                                        @elseif($type === 'progress_confirmed')
                                            <div class="text-[15px] font-semibold">IT Mengonfirmasi Progress</div>
                                            <div class="mt-1 text-sm">
                                                <div><span class="font-semibold">Nama Project</span>: {{ $pName }}</div>
                                                <div><span class="font-semibold">Progress</span>: {{ $progName }}</div>
                                                <div class="mt-1"><span class="font-semibold">Tanggal</span>: {{ $dateText }} • {{ $timeText }} WIB</div>
                                                @if ($message)
                                                    <div class="text-gray-700 mt-1">{{ $message }}</div>
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                @if ($late)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                        IT Tidak Memenuhi Target
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                        IT Telah Mengonfirmasi
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-[15px] font-semibold">Notifikasi</div>
                                            <div class="mt-1 text-sm">
                                                <div class="text-gray-700">{{ $message ?: 'Ada pembaruan dari IT.' }}</div>
                                                <div class="mt-1 text-xs text-gray-600">{{ $dateText }} • {{ $timeText }} WIB</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="text-right shrink-0">
                                        <div class="text-xs text-gray-600">{{ $timeText }}</div>
                                        <div class="mt-2 flex items-center gap-2 justify-end">
                                            @if ($pId)
                                                <a href="{{ route('dig.projects.show', $pId) }}" class="text-xs underline text-[#7A1C1C]">Lihat Project</a>
                                            @endif
                                            @if ($isUnread)
                                                <form method="POST" action="{{ route('dig.notifications.read', $n->id) }}">
                                                    @csrf
                                                    <button class="text-xs underline text-[#7A1C1C]">Tandai terbaca</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
            @endif

            {{-- RIWAYAT (maks 7 hari ke belakang, selain Hari Ini) --}}
            @if ($groupedByDate->count() > 0)
                <div class="mt-8 space-y-6">
                    @foreach ($groupedByDate as $dateLabel => $items)
                        @php
                            $unreadInGroup = $items->whereNull('read_at')->count();
                        @endphp
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <h2 class="text-base font-semibold">{{ $dateLabel }}</h2>
                                @if ($unreadInGroup > 0)
                                    <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 rounded-full bg-[#7A1C1C] text-white text-xs px-2">
                                        {{ $unreadInGroup > 99 ? '99+' : $unreadInGroup }}
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-3">
                                @foreach ($items as $n)
                                    @php
                                        $d = $n->data ?? [];
                                        $type = strtolower($d['type'] ?? '');
                                        $pName = $d['project_name'] ?? 'Project';
                                        $pId = $d['project_id'] ?? null;
                                        $progName = $d['progress_name'] ?? 'Progress';
                                        $message = $d['message'] ?? '';
                                        $late = (bool) ($d['late'] ?? false);
                                        $isUnread = is_null($n->read_at);

                                        $created = optional($n->created_at)->timezone('Asia/Jakarta');
                                        $dateText = $created ? $created->format('d M Y') : '-';
                                        $timeText = $created ? $created->format('H.i') : '-';
                                    @endphp

                                    <div class="rounded-xl px-5 py-4 border {{ $isUnread ? 'border-[#7A1C1C] bg-[#FDF3F3]' : 'border-[#E7C9C9] bg-white' }}">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0">
                                                @if ($type === 'it_project_created')
                                                    <div class="text-[15px] font-semibold">Project Baru dari IT</div>
                                                    <div class="mt-1 text-sm">
                                                        <div><span class="font-semibold">Nama Project</span>: {{ $pName }}</div>
                                                        @if ($message)
                                                            <div class="text-gray-700 mt-1">{{ $message }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                            Project baru dari IT
                                                        </span>
                                                    </div>
                                                @elseif($type === 'progress_confirmed')
                                                    <div class="text-[15px] font-semibold">IT Mengonfirmasi Progress</div>
                                                    <div class="mt-1 text-sm">
                                                        <div><span class="font-semibold">Nama Project</span>: {{ $pName }}</div>
                                                        <div><span class="font-semibold">Progress</span>: {{ $progName }}</div>
                                                        @if ($message)
                                                            <div class="text-gray-700 mt-1">{{ $message }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2">
                                                        @if ($late)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                                IT Tidak Memenuhi Target
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                                IT Telah Mengonfirmasi
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-[15px] font-semibold">Notifikasi</div>
                                                    <div class="mt-1 text-sm">
                                                        <div class="text-gray-700">{{ $message ?: 'Ada pembaruan dari IT.' }}</div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="text-right shrink-0">
                                                <div class="text-xs text-gray-600">{{ $timeText }}</div>
                                                <div class="mt-2 flex items-center gap-2 justify-end">
                                                    @if ($pId)
                                                        <a href="{{ route('dig.projects.show', $pId) }}" class="text-xs underline text-[#7A1C1C]">Lihat Project</a>
                                                    @endif
                                                    @if ($isUnread)
                                                        <form method="POST" action="{{ route('dig.notifications.read', $n->id) }}">
                                                            @csrf
                                                            <button class="text-xs underline text-[#7A1C1C]">Tandai terbaca</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- PAGINATION --}}
            @if (isset($notifications) && $notifications->lastPage() > 1)
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        @endif
    </main>
@endsection
