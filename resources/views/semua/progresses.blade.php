@extends('layouts.dashboard')

@section('title', 'Project')
@section('pageTitle', 'Project')

@section('content')
    @php
        $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';
        $me = $me ?? auth()->user()?->fresh();

        $q = request('status','all');        // all | in_progress | meet | not_meet
        $mine = request('mine','0');         // '1' atau '0'
        $mineActive = $mine === '1';
        $tab = fn($v) => $q===$v ? 'bg-[#7A1C1C] text-white' : 'bg-white text-[#7A1C1C] hover:bg-[#FFF2F2]';
        $mineBtnClass = $mineActive ? 'bg-[#7A1C1C] text-white' : 'bg-white text-[#7A1C1C] hover:bg-[#FFF2F2]';
        $buildUrl = function (string $status, bool $toggleMine = false) use ($mineActive) {
            $params = ['status' => $status];
            $params['mine'] = $toggleMine ? ($mineActive ? null : 1) : ($mineActive ? 1 : null);
            return route('semua.progresses', array_filter($params, fn($v)=>!is_null($v)));
        };
    @endphp

    <div class="{{ $container }}">
        <div class="py-3 flex items-center gap-3">
                <a href="{{ $buildUrl('all', false) }}"
               class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tab('all') }} grid place-items-center">Semua</a>
                <a href="{{ $buildUrl('in_progress', false) }}"
               class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tab('in_progress') }} grid place-items-center">Dalam Proses</a>
                <a href="{{ $buildUrl('meet', false) }}"
               class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tab('meet') }} grid place-items-center">Project Selesai, Memenuhi</a>
                <a href="{{ $buildUrl('not_meet', false) }}"
               class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tab('not_meet') }} grid place-items-center">Project Selesai, Tidak Memenuhi</a>

            <a href="{{ $buildUrl($q, true) }}"
               class="ml-auto rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $mineBtnClass }} grid place-items-center">
                Tugas Saya
            </a>
        </div>

        @if (session('success'))
            <div class="mt-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mt-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <div class="font-semibold mb-1">Terjadi kesalahan:</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        @php $hasAny = false; @endphp
        @forelse ($projects as $project)
            @php
                $isFinalizedByDIG = !is_null($project->meets_requirement);
                $isMeet           = $isFinalizedByDIG && (int)$project->meets_requirement === 1;
                $isNotMeet        = $isFinalizedByDIG && (int)$project->meets_requirement === 0;

                $skipByTab = false;
                if ($q === 'in_progress' && $isFinalizedByDIG) $skipByTab = true;
                if ($q === 'meet'        && !$isMeet)          $skipByTab = true;
                if ($q === 'not_meet'    && !$isNotMeet)       $skipByTab = true;
            @endphp
            @if($skipByTab) @continue @endif
            @php
                $currentId = (int) auth()->id();
                $mineById = ((int)($project->digital_banking_id ?? 0) === $currentId)
                         || ((int)($project->developer_id ?? 0) === $currentId);

                $mineByName = false;
                if(!$mineById) {
                    $meName = trim((string)($me?->name ?? ''));
                    $dbName = trim((string)($project->digitalBanking->name ?? ''));
                    $itName = trim((string)($project->developer->name ?? ''));
                    $mineByName = $meName !== '' && ($meName === $dbName || $meName === $itName);
                }
                $isMine = $mineById || $mineByName;
                if ($mineActive && !$isMine) { @endphp @continue @php }
                $hasAny = true;

                $latestPercents = [];
                foreach ($project->progresses as $pr) {
                    $last = $pr->updates->first() ?: $pr->updates->sortByDesc('update_date')->first();
                    $latestPercents[] = $last ? (int) ($last->progress_percent ?? ($last->percent ?? 0)) : 0;
                }
                $realization = count($latestPercents) ? (int) round(array_sum($latestPercents) / max(count($latestPercents), 1)) : 0;

                $size=88; $stroke=10; $r=$size/2-$stroke; $circ=2*M_PI*$r; $off=$circ*(1-$realization/100);

                if ($isFinalizedByDIG) {
                    $statusText  = $isMeet ? 'Project Selesai, Memenuhi' : 'Project Selesai, Tidak Memenuhi';
                    $statusBg    = $isMeet ? '#DCFCE7' : '#FEE2E2';
                    $statusColor = $isMeet ? '#166534' : '#7A1C1C';
                } else {
                    $statusText  = 'Dalam Proses';
                    $statusBg    = '#FEE2E2';
                    $statusColor = '#7A1C1C';
                }

                $isDig = $me?->role === 'digital_banking';
                $canDecideCompletion = $project->can_decide_completion
                    ?? (function() use ($project){
                        $all = $project->progresses->every(function ($p) {
                            $last = $p->updates->first() ?: $p->updates->sortByDesc('update_date')->first();
                            $real = $last ? (int) ($last->progress_percent ?? ($last->percent ?? 0)) : 0;
                            return $real >= (int) $p->desired_percent && !is_null($p->confirmed_at);
                        });
                        return $all && is_null($project->meets_requirement);
                    })();
            @endphp

            <section class="mt-5 rounded-2xl border-2 border-[#7A1C1C] bg-white p-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-[12px] font-semibold"
                          style="background: {{ $statusBg }}; color: {{ $statusColor }};">
                        {{ $statusText }}
                    </span>

                    @if($isDig && $canDecideCompletion)
                        <div class="ml-auto flex items-center gap-2">
                            <form method="POST" action="{{ route('projects.setCompletion', $project->id) }}"
                                  class="inline-block" title="Tandai project ini sebagai selesai dan memenuhi">
                                @csrf @method('PATCH')
                                <input type="hidden" name="meets" value="1">
                                <button type="submit"
                                        class="px-3 py-1.5 text-xs rounded-full bg-green-700 text-white hover:opacity-90">
                                    Selesai, Memenuhi
                                </button>
                            </form>

                            <form method="POST" action="{{ route('projects.setCompletion', $project->id) }}"
                                  class="inline-block" title="Tandai project ini sebagai selesai dan tidak memenuhi">
                                @csrf @method('PATCH')
                                <input type="hidden" name="meets" value="0">
                                <button type="submit"
                                        class="px-3 py-1.5 text-xs rounded-full bg-[#7A1C1C] text-white hover:opacity-90">
                                    Selesai, Tidak Memenuhi
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="mt-3 grid md:grid-cols-[auto,1fr,auto] items-start gap-4">
                    <div class="flex items-center">
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
                            <span class="text-gray-700 font-medium">Nama Project</span><span>:</span>
                            <span class="font-semibold">{{ $project->name }}</span>
                        </div>
                        <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                            <span class="text-gray-700">Penanggung Jawab (Digital Banking)</span><span>:</span>
                            <span>{{ $project->digitalBanking->name ?? '-' }}</span>
                        </div>
                        <div class="grid grid-cols-[auto_auto_1fr] gap-x-2 md:col-span-5">
                            <span class="text-gray-700">Penanggung Jawab (Developer)</span><span>:</span>
                            <span>{{ $project->developer->name ?? '-' }}</span>
                        </div>
                        <div class="grid grid-cols-[auto_auto_1fr] gap-x-2 md:col-span-2">
                            <span class="text-gray-700">Deskripsi</span><span>:</span>
                            <span>{{ $project->description ?: '-' }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 justify-end">
                        <a href="{{ route('projects.edit', $project->id) }}"
                           class="p-2 rounded-lg bg-white/60 hover:bg-white border" title="Edit Project">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM22.61 5.64c.39-.39.39-1.02 0-1.41l-2.83-2.83a.9959.9959 0 0 0-1.41 0L16.13 3.04l3.75 3.75 2.73-2.73z"/>
                            </svg>
                        </a>
                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                              data-confirm-delete="true"
                              data-message="Yakin ingin menghapus project ini? Aksi ini tidak bisa dibatalkan.">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 rounded-lg bg-white/60 hover:bg-white border" title="Hapus Project">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6 7h12v12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7zm3-4h6l1 1h4v2H4V4h4l1-1z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

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
                                    <span class="truncate max-w-[160px]" title="{{ $att->original_name }}">{{ $att->original_name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-4 flex justify-end">
                    <button type="button"
                        data-target="progressForm-{{ $project->id }}"
                        onclick="document.getElementById('progressForm-{{ $project->id }}').classList.toggle('hidden')"
                        @if($isFinalizedByDIG)
                            disabled
                            title="Project sudah difinalisasi, progress baru tidak dapat ditambahkan."
                            class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm shadow bg-[#7A1C1C]/40 text-white cursor-not-allowed opacity-60"
                        @else
                            class="inline-flex items-center gap-2 rounded-xl bg-[#7A1C1C] text-white px-3 py-2 text-sm shadow hover:bg-[#5a1515] cursor-pointer"
                        @endif
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z" />
                        </svg>
                        Tambah Progress
                    </button>
                </div>

                <div id="progressForm-{{ $project->id }}" class="hidden mt-3 rounded-xl bg-white p-4 border border-[#E7C9C9]">
                    <div class="font-semibold mb-2">Tambah Progress untuk Project ini</div>
                    <form method="POST" action="{{ route('projects.progresses.store', $project->id) }}"
                          class="grid grid-cols-1 md:grid-cols-5 gap-2">
                        @csrf
                        <input name="name" required placeholder="Nama Progress"
                            class="rounded-xl bg-[#E2B9B9]/40 border border-[#C89898] px-3 py-2 outline-none md:col-span-2">
                        <input type="date" name="start_date" required
                            class="rounded-xl bg-[#E2B9B9]/40 border border-[#C89898] px-3 py-2 outline-none">
                        <input type="date" name="end_date" required
                            class="rounded-xl bg-[#E2B9B9]/40 border border-[#C89898] px-3 py-2 outline-none">
                        <div class="rounded-xl bg-[#F8E9E9] border border-[#C89898] px-3 py-2 text-xs text-[#7A1C1C]">
                            Target dihitung otomatis.
                        </div>
                        <button class="rounded-xl border-2 border-[#7A1C1C] bg-[#E2B9B9] px-4 py-2 font-semibold hover:bg-[#D9AFAF]">
                            Tambah
                        </button>
                    </form>
                </div>

                <div class="mt-4">
                    <div class="scroll-thin grid md:grid-cols-2 gap-4 max-h-[280px] overflow-y-auto pr-1">
                        @forelse($project->progresses as $pr)
                            @php
                                $last              = $pr->updates->sortByDesc('update_date')->first();
                                $realisasi         = $last ? (int)($last->percent ?? $last->progress_percent ?? 0) : 0;
                                $isOwner           = (int)($pr->created_by ?? 0) === (int)auth()->id();
                                $alreadyConfirmed  = !is_null($pr->confirmed_at);
                                $isDigProgress     = $me?->role === 'digital_banking';
                                $canUpdate         = $isOwner && $isDigProgress && !$alreadyConfirmed;

                                $endDate   = $pr->end_date ? \Illuminate\Support\Carbon::parse($pr->end_date)->startOfDay() : null;
                                $isOverdue = $endDate ? $endDate->lt(now()->startOfDay()) : false;
                                $isUnmet   = $isOverdue && !$pr->confirmed_at && ($realisasi < (int)$pr->desired_percent);

                                $canUpdate = $canUpdate && !$isOverdue;
                                $updateDisabledReason = $isOverdue
                                    ? 'Tidak bisa update: sudah lewat timeline selesai'
                                    : ($alreadyConfirmed ? 'Sudah dikonfirmasi' : ($isOwner ? 'Hanya DIG yang bisa update' : 'Bukan pembuat progress'));
                            @endphp

                            <div class="rounded-2xl bg-[#F7E4E4] p-4 border border-[#E7C9C9]">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="font-semibold">
                                        Progress {{ $loop->iteration }}{{ $pr->name ? ' — '.$pr->name : '' }}
                                        @if($isUnmet)
                                            <span class="ml-2 inline-flex items-center rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-[11px] font-semibold">Tidak Memenuhi</span>
                                        @endif
                                    </div>

                                    @if($isOwner)
                                        <div class="flex gap-2">
                                            <button type="button"
                                                class="px-3 py-1.5 text-xs rounded-lg border bg-white/70 hover:bg-white"
                                                onclick="document.getElementById('editProgress-{{ $pr->id }}').classList.toggle('hidden')">
                                                Edit
                                            </button>
                                            <form method="POST"
                                                  action="{{ route('progresses.destroy', $pr->id) }}"
                                                  data-confirm-delete="true"
                                                  data-message="Hapus progress ini?">
                                                @csrf @method('DELETE')
                                                <button class="px-3 py-1.5 text-xs rounded-lg border bg-white/70 hover:bg-white">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                @if($isUnmet)
                                    <div class="mb-2 text-[12px] rounded-lg border border-red-300 bg-red-50 text-red-700 px-3 py-2">
                                        Melewati timeline selesai, realisasi belum mencapai target & belum dikonfirmasi.
                                    </div>
                                @endif

                                <div class="text-sm">
                                    <div class="grid grid-cols-[auto,1fr] gap-x-4 gap-y-1">
                                        <span>Timeline Mulai</span>
                                        <span>: {{ $pr->start_date ? \Illuminate\Support\Carbon::parse($pr->start_date)->timezone('Asia/Jakarta')->format('d M Y') : '-' }}</span>

                                        <span>Timeline Selesai</span>
                                        <span>: {{ $pr->end_date ? \Illuminate\Support\Carbon::parse($pr->end_date)->timezone('Asia/Jakarta')->format('d M Y') : '-' }}</span>

                                        <span>Target Progress</span>    <span>: {{ (int)$pr->desired_percent }}%</span>
                                        <span>Realisasi Progress</span> <span>: {{ $realisasi }}%</span>
                                    </div>
                                </div>

                                <div id="editProgress-{{ $pr->id }}" class="hidden mt-3">
                                    <form method="POST" action="{{ route('progresses.update', $pr->id) }}"
                                          class="grid grid-cols-1 md:grid-cols-5 gap-2 bg-white/70 rounded-xl p-3 border">
                                        @csrf @method('PUT')
                                        <input name="name" value="{{ old('name', $pr->name) }}" required
                                            class="rounded-xl bg-white border px-3 py-2 outline-none md:col-span-2" placeholder="Nama progress"
                                            @unless($isOwner) disabled @endunless>
                                        <input type="date" name="start_date" value="{{ old('start_date', $pr->start_date) }}" required
                                            class="rounded-xl bg-white border px-3 py-2 outline-none" @unless($isOwner) disabled @endunless>
                                        <input type="date" name="end_date" value="{{ old('end_date', $pr->end_date) }}" required
                                            class="rounded-xl bg-white border px-3 py-2 outline-none" @unless($isOwner) disabled @endunless>
                                        <div class="rounded-xl bg-[#F8E9E9] border border-[#C89898] px-3 py-2 text-xs text-[#7A1C1C] flex items-center">
                                            Target: {{ (int)$pr->desired_percent }}% (otomatis 100÷23)
                                        </div>
                                        <button class="h-[40px] min-w-[140px] px-4 rounded-full border-2 border-[#7A1C1C] bg-[#E2B9B9] hover:bg-[#D9AFAF] text-xs font-semibold"
                                            @unless($isOwner) disabled @endunless>
                                            Simpan Perubahan
                                        </button>
                                    </form>
                                </div>

                                <div class="mt-3">
                                    <form method="POST" action="{{ route('progresses.updates.store', $pr->id) }}" class="flex flex-wrap gap-3 items-center">
                                        @csrf
                                        <input type="date" name="update_date" value="{{ now()->toDateString() }}"
                                            class="rounded-xl border px-3 py-2 text-sm" @unless($canUpdate) disabled @endunless>
                                        <input type="number" name="percent" min="0" max="100" placeholder="%"
                                            class="rounded-xl border px-3 py-2 text-sm w-28" @unless($canUpdate) disabled @endunless required>
                                        <button
                                            class="rounded-xl bg-[#7A1C1C] text-white px-4 py-2 text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                                            @unless($canUpdate) disabled @endunless
                                            title="{{ $canUpdate ? '' : $updateDisabledReason }}">
                                            Update Progress
                                        </button>
                                    </form>

                                    <div class="mt-2">
                                        @if(!$alreadyConfirmed)
                                            <form method="POST" action="{{ route('progresses.confirm', $pr->id) }}">
                                                @csrf
                                                <button
                                                    class="rounded-xl bg-green-700 text-white px-4 py-2 text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                                                    {{ ($isOwner && $realisasi >= (int)$pr->desired_percent) ? '' : 'disabled' }}
                                                    title="{{ $isOwner ? 'Belum mencapai target' : 'Hanya pembuat progress yang dapat konfirmasi' }}">
                                                    Konfirmasi
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-semibold">
                                                Sudah dikonfirmasi
                                            </span>
                                        @endif
                                    </div>

                                    @if($isOverdue && !$alreadyConfirmed)
                                        <div class="mt-2">
                                            <span class="inline-flex items-center rounded-full bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold">
                                                Telat dari timeline
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-sm text-gray-600">Belum ada progress.</div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('dig.projects.show', $project->id) }}"
                       class="inline-flex items-center gap-2 rounded-[12px] border border-[#7A1C1C] px-4 py-2 text-sm font-semibold text-[#7A1C1C] bg-white hover:bg-[#FFF2F2]">
                        Detail Informasi
                    </a>
                </div>
            </section>
        @empty
        @endforelse

        @if(!$hasAny)
            <div class="mt-4">
                <div class="bg-[#EBD0D0] rounded-xl px-6 py-8 flex items-center justify-center border border-[#E7C9C9]">
                    <div class="rounded-xl bg-[#CFA8A8] px-5 py-3 text-white/95">
                        Tidak ada project.
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.btn-toggle-progress').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-target');
            const el = document.getElementById(id);
            if (el) el.classList.toggle('hidden');
        });
    });
</script>
@endsection
