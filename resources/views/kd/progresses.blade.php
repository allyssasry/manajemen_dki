{{-- resources/views/kd/progresses.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Project | Kepala Divisi')
@section('pageTitle', 'Project')

@section('content')
    @php
        $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';
        $q = $status ?? request('status','all'); // all | in_progress | done
        $tabCls = fn($v) => $q===$v ? 'bg-[#7A1C1C] text-white'
                                    : 'bg-white text-[#7A1C1C] hover:bg-[#FFF2F2]';
    @endphp

    <div class="{{ $container }} pb-10">
        {{-- Filter tab --}}
        <div class="mt-3 flex gap-3">
            <a href="{{ route('kd.progresses',['status'=>'all']) }}"
               class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tabCls('all') }} grid place-items-center">Semua</a>
            <a href="{{ route('kd.progresses',['status'=>'in_progress']) }}"
               class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tabCls('in_progress') }} grid place-items-center">Dalam Proses</a>
            <a href="{{ route('kd.progresses',['status'=>'done']) }}"
               class="rounded-[12px] h-9 px-5 text-sm font-semibold border-2 border-[#7A1C1C] {{ $tabCls('done') }} grid place-items-center">Telah Selesai</a>
        </div>

        {{-- DAFTAR PROJECT --}}
        @forelse($projects as $project)
            @php
                $latestPercents = [];
                foreach ($project->progresses as $pr) {
                    $u = $pr->updates->sortByDesc('update_date')->first();
                    $latestPercents[] = $u ? (int)($u->percent ?? $u->progress_percent ?? 0) : 0;
                }
                $realization = count($latestPercents) ? (int) round(array_sum($latestPercents)/max(count($latestPercents),1)) : 0;
                $size=88; $stroke=10; $r=$size/2-$stroke; $circ=2*M_PI*$r; $off=$circ*(1-$realization/100);

                $allMetAndConfirmed = $project->progresses->every(function ($p) {
                    $u = $p->updates->sortByDesc('update_date')->first();
                    $real = $u ? (int)($u->percent ?? $u->progress_percent ?? 0) : 0;
                    return $real >= (int)$p->desired_percent && !is_null($p->confirmed_at);
                });

                $finishedAt = $allMetAndConfirmed
                    ? optional($project->progresses->max('confirmed_at'))->timezone('Asia/Jakarta')
                    : null;
            @endphp

            <section class="mt-6 rounded-2xl border-2 border-[#7A1C1C] bg-[#FFFAFA] p-5">
                <div class="flex items-center justify-between text-xs font-semibold mb-2">
                    <span class="{{ $allMetAndConfirmed ? 'text-green-700' : 'text-[#7A1C1C]' }}">
                        {{ $allMetAndConfirmed ? 'Project Telah Selesai' : 'Dalam Proses' }}
                    </span>
                    <span class="text-gray-600">
                        {{ $finishedAt ? $finishedAt->translatedFormat('d F Y') : '' }}
                    </span>
                </div>

                <div class="grid md:grid-cols-[auto,1fr] items-start gap-5">
                    <div class="flex items-center gap-4">
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
                            <span class="text-gray-700">Nama Project</span><span>:</span>
                            <span class="font-semibold ">{{ $project->name }}</span>
                        </div>
                        <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                            <span class="text-gray-700">Penanggung Jawab (DIG)</span><span>:</span>
                            <span>{{ $project->digitalBanking->name ?? '-' }}</span>
                        </div>
                        <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                            <span class="text-gray-700">Penanggung Jawab (IT)</span><span>:</span>
                            <span>{{ $project->developer->name ?? '-' }}</span>
                        </div>
                        <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                            <span class="text-gray-700">Deskripsi</span><span>:</span>
                            <span>{{ $project->description ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- LAMPIRAN PROJECT --}}
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
                                    <span class="truncate max-w-[140px]" title="{{ $att->original_name }}">{{ $att->original_name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-4">
                    <div class="scroll-thin grid md:grid-cols-2 gap-4 max-h-[240px] overflow-y-auto pr-1">
                        @forelse($project->progresses as $idx => $pr)
                            @php
                                $last    = $pr->updates->sortByDesc('update_date')->first();
                                $real    = $last ? (int)($last->percent ?? $last->progress_percent ?? 0) : 0;
                                $creator = $pr->creator;
                                $ownerRoleLabel = $creator?->role === 'digital_banking' ? 'DIG' : ($creator?->role === 'it' ? 'IT' : '—');
                            @endphp
                            {{-- card progress --}}
                            <div class="rounded-xl border border-[#7A1C1C] bg-[#8D2121] p-4 text-white">
                                <div class="font-semibold mb-2">Progress {{ $idx+1 }} — {{ $pr->name }}</div>
                                <div class="text-sm grid gap-1">
                                    <div><span class="inline-block w-36">Timeline Mulai</span>: {{ $pr->start_date }}</div>
                                    <div><span class="inline-block w-36">Timeline Selesai</span>: {{ $pr->end_date }}</div>
                                    <div><span class="inline-block w-36">Target Progress</span>: {{ $pr->desired_percent }}%</div>
                                    <div><span class="inline-block w-36">Realisasi Progress</span>: {{ $real }}%</div>
                                    <div class="text-xs mt-1">
                                        *Dibuat oleh <strong>{{ $ownerRoleLabel }}</strong> — {{ $creator?->name ?? '—' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-sm text-white/80">Belum ada progress.</div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('kd.projects.show', $project->id) }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-[#7A1C1C] px-3 py-1.5 text-xs font-semibold text-[#7A1C1C] bg-white hover:bg-[#FFF2F2]">
                        Detail Informasi
                    </a>
                </div>
            </section>
        @empty
            <div class="mt-6">
                <div class="bg-[#EBD0D0] rounded-2xl px-6 py-8 flex items-center justify-center">
                    <div class="rounded-2xl bg-[#CFA8A8] px-5 py-3 text-white/95">Tidak ada project untuk ditampilkan</div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
