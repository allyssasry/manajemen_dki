{{-- 
    Notifications List Partial
    Digunakan di halaman notifications DIG, IT, dll
    Variables: $notifications (paginator)
--}}
@php
    use App\Models\Project;
    use App\Models\Progress;
    
    $norm = fn($v) => strtolower(trim((string) $v));

    // Filter: notif buat DIG yang dikirim dari IT
    $isForDigFromIt = function($n) use ($norm) {
        $d   = $n->data ?? [];
        $typ = $norm(data_get($d,'type'));
        $by  = $norm(data_get($d,'by_role'));
        $dev = $norm(data_get($d,'developer_role') ?? '');
        $tgt = $norm(data_get($d,'target_role') ?? '');

        $allowedTypes = ['it_project_created', 'progress_confirmed'];

        $isAllowedType = in_array($typ, $allowedTypes, true);
        $fromIT        = ($by === 'it') || ($dev === 'it');
        $targetDIG     = ($tgt === '' || $tgt === 'digital_banking');

        return $isAllowedType && $fromIT && $targetDIG;
    };

    // Ambil item dari paginator (halaman ini)
    $allItems = isset($notifications)
        ? collect($notifications->items() ?? [])
        : collect();

    $nowJak = \Illuminate\Support\Carbon::now('Asia/Jakarta');
    $sevenDaysAgoJak = $nowJak->copy()->subDays(7)->startOfDay();

    // Filter: hanya 7 hari ke belakang + khusus DIG dari IT
    $filtered = $allItems
        ->filter($isForDigFromIt)
        ->filter(function($n) use ($sevenDaysAgoJak) {
            $createdJak = optional($n->created_at)->timezone('Asia/Jakarta');
            return $createdJak && $createdJak->gte($sevenDaysAgoJak);
        });

    // ====== HAPUS NOTIFIKASI YANG PROJECT / PROGRESS-NYA SUDAH DIHAPUS ======
    $projectIds = $filtered
        ->map(fn($n) => data_get($n->data, 'project_id'))
        ->filter()
        ->unique()
        ->values()
        ->all();

    $progressIds = $filtered
        ->map(fn($n) => data_get($n->data, 'progress_id'))
        ->filter()
        ->unique()
        ->values()
        ->all();

    $existingProjectIds = !empty($projectIds)
        ? Project::whereIn('id', $projectIds)->pluck('id')->map(fn($id) => (int) $id)->all()
        : [];

    $existingProgressIds = !empty($progressIds)
        ? Progress::whereIn('id', $progressIds)->pluck('id')->map(fn($id) => (int) $id)->all()
        : [];

    $filtered = $filtered->filter(function($n) use ($existingProjectIds, $existingProgressIds) {
        $pid   = (int) (data_get($n->data, 'project_id') ?? 0);
        $prgId = (int) (data_get($n->data, 'progress_id') ?? 0);

        if ($pid === 0 && $prgId === 0) return true;
        if ($pid !== 0 && !in_array($pid, $existingProjectIds, true)) return false;
        if ($prgId !== 0 && !in_array($prgId, $existingProgressIds, true)) return false;

        return true;
    });

    // Group by tanggal (YYYY-mm-dd), lalu sort desc
    $groupedByDate = $filtered
        ->groupBy(function($n) {
            return optional($n->created_at)->timezone('Asia/Jakarta')->format('Y-m-d');
        })
        ->sortKeysDesc();
@endphp

@if($groupedByDate->isEmpty())
    {{-- EMPTY STATE GLOBAL --}}
    <div class="py-12 text-center text-sm text-gray-600">
        Belum ada notifikasi dalam 7 hari terakhir.
    </div>
@else
    @foreach($groupedByDate as $dateKey => $group)
        @php
            $dt       = \Illuminate\Support\Carbon::parse($dateKey, 'Asia/Jakarta');
            $isToday  = $dt->isSameDay($nowJak);
            $label    = $isToday ? 'Hari Ini' : $dt->translatedFormat('d M Y');
            $unreadInGroup = $group->whereNull('read_at')->count();
        @endphp

        <section class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-semibold">{{ $label }}</h2>
                @if($unreadInGroup > 0)
                    <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 rounded-full bg-[#7A1C1C] text-white text-xs px-2">
                        {{ $unreadInGroup > 99 ? '99+' : $unreadInGroup }}
                    </span>
                @endif
            </div>

            <div class="space-y-3">
                @foreach($group as $n)
                    @include('dig.partials.notification-card', ['n' => $n, 'norm' => $norm])
                @endforeach
            </div>
        </section>
    @endforeach

    {{-- PAGINATION --}}
    @if(isset($notifications) && $notifications->lastPage() > 1)
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
@endif
