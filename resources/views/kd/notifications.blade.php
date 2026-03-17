{{-- resources/views/kd/notifications.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Notifikasi | Kepala Divisi')
@section('pageTitle', 'Notifikasi')

@section('content')
    @php
        use Illuminate\Support\Carbon;
        use App\Models\Project;

        $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';
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

            // KD ikut baca kalau:
            // - target_role kosong (global) ATAU
            // - target_role = 'kepala_divisi' ATAU 'supervisor'
            $targetKD = ($tgt === '' || $tgt === 'kepala_divisi' || $tgt === 'supervisor');

            return $isAllowedType && $targetKD;
        });

        // Hapus notifikasi yang project-nya sudah dihapus
        $projectIds = $filtered
            ->map(fn($n) => data_get($n->data, 'project_id'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!empty($projectIds)) {
            $existingProjectIds = Project::whereIn('id', $projectIds)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();

            $filtered = $filtered->filter(function($n) use ($existingProjectIds) {
                $pid = (int) (data_get($n->data, 'project_id') ?? 0);
                if ($pid === 0) return true;
                return in_array($pid, $existingProjectIds, true);
            });
        }

        $unreadCount = $filtered->whereNull('read_at')->count();

        $nowJak = Carbon::now('Asia/Jakarta');
        $groupedByDate = $filtered
            ->groupBy(function($n) {
                return optional($n->created_at)->timezone('Asia/Jakarta')->format('Y-m-d');
            })
            ->sortKeysDesc();
    @endphp

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
                        $dt            = Carbon::parse($dateKey, 'Asia/Jakarta');
                        $isToday       = $dt->isSameDay($nowJak);
                        $label         = $isToday ? 'Hari Ini' : $dt->translatedFormat('d M Y');
                        $unreadInGroup = $items->whereNull('read_at')->count();
                    @endphp

                    <section>
                        <div class="mb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-semibold">{{ $label }}</h2>
                            </div>
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

                                        <div class="shrink-0 text-right">
                                            <div class="text-xs text-gray-600">{{ $timeText }}</div>

                                            <div class="mt-2 flex items-center gap-2 justify-end">
                                                @if(function_exists('route') && Route::has('kd.progresses') && $pId)
                                                    <a href="{{ route('kd.progresses') }}#project-{{ $pId }}"
                                                       class="text-xs underline text-[#7A1C1C]">
                                                        Lihat Project
                                                    </a>
                                                @endif

                                                {{-- TANDAI TERBACA / TERBACA --}}
                                                <form method="POST" action="{{ route('kd.notifications.read', $n->id) }}">
                                                    @csrf
                                                    <button type="submit" class="text-xs underline text-[#7A1C1C]">
                                                        {{ $n->read_at ? 'Terbaca' : 'Tandai terbaca' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif
    </div>
@endsection
