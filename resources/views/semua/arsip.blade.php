@extends('layouts.dashboard')

@section('title', 'Arsip Project')
@section('pageTitle', 'Arsip')

@section('content')
    @php
        $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';
    @endphp

    {{-- ================== KONTEN UTAMA ================== --}}
    <main class="{{ $container }} py-6">
        {{-- Filter --}}
        <form method="GET" class="flex flex-wrap items-end gap-3 border-b pb-4">
            <div>
                <label class="text-xs text-gray-600">Kata Kunci</label>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="block w-[220px] rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none"
                       placeholder="Cari nama/deskripsi project">
            </div>
            <div>
                <label class="text-xs text-gray-600">Tanggal Mulai</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="block rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
            </div>
            <div>
                <label class="text-xs text-gray-600">Tanggal Selesai</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="block rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
            </div>
            <div>
                <label class="text-xs text-gray-600">Urutkan</label>
                <select name="sort" class="block rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
                    <option value="finished_desc" @selected(request('sort','finished_desc')==='finished_desc')>Terbaru selesai</option>
                    <option value="finished_asc"  @selected(request('sort')==='finished_asc')>Terlama selesai</option>
                    <option value="name_asc"      @selected(request('sort')==='name_asc')>Nama A-Z</option>
                    <option value="name_desc"     @selected(request('sort')==='name_desc')>Nama Z-A</option>
                </select>
            </div>
            <button class="h-9 px-5 rounded-[12px] text-sm font-semibold border-2 border-[#7A1C1C]  bg-[#FFF7F7] hover:bg-[#8D2121]/10">
                Terapkan
            </button>
        </form>

        {{-- ===== LIST ARSIP ===== --}}
        @forelse ($projects as $project)
            @php
                // Ambil realisasi terbaru per progress
                $latestPercents = [];
                foreach ($project->progresses as $pr) {
                    $last = $pr->updates->first() ?: $pr->updates->sortByDesc('update_date')->first();
                    $latestPercents[] = $last ? (int) ($last->progress_percent ?? ($last->percent ?? 0)) : 0;
                }
                $realization = count($latestPercents) ? (int) round(array_sum($latestPercents) / max(count($latestPercents), 1)) : 0;

                // Ring
                $size=88; $stroke=10; $r=$size/2-$stroke; $circ=2*M_PI*$r; $off=$circ*(1-$realization/100);

                // Status final
                $isMeet  = (bool) $project->meets_requirement;
                $statusText  = $isMeet ? 'Project Selesai, Memenuhi' : 'Project Selesai, Tidak Memenuhi';
                $statusColor = $isMeet ? '#166534' : '#7A1C1C';
                $statusBg    = $isMeet ? '#DCFCE7' : '#FEE2E2';

                $finishedAt = $project->completed_at ?? ($project->finished_at_calc ?? $project->updated_at);
            @endphp

            <section class="mt-6 rounded-2xl border-2 border-[#7A1C1C] bg-[#F2DCDC] p-5">
                <div class="grid md:grid-cols-[auto,1fr,auto] items-start gap-4">
                    {{-- Chip status --}}
                    <div class="text-xs font-semibold">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                              style="color: {{ $statusColor }}; background-color: {{ $statusBg }};">
                            {{ $statusText }}
                        </span>
                    </div>

                    {{-- Ring + Info --}}
                    <div class="flex items-center gap-5">
                        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}">
                            <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}" stroke="#E9D0D0" stroke-width="{{ $stroke }}" fill="none" opacity=".9"/>
                            <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}" stroke="#7A1C1C" stroke-width="{{ $stroke }}"
                                    stroke-linecap="round" stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $off }}"
                                    transform="rotate(-90 {{ $size/2 }} {{ $size/2 }})" fill="none"/>
                            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="16" font-weight="700" fill="#7A1C1C">{{ $realization }}%</text>
                        </svg>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-1 text-sm">
                            <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                                <span class="text-gray-600">Nama Project</span><span>:</span>
                                <span class="font-semibold">{{ $project->name }}</span>
                            </div>
                            <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                                <span class="text-gray-600">Penanggung Jawab (DIG)</span><span>:</span>
                                <span>{{ $project->digitalBanking->name ?? '-' }}</span>
                            </div>
                            <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                                <span class="text-gray-600">Penanggung Jawab (IT)</span><span>:</span>
                                <span>{{ $project->developer->name ?? '-' }}</span>
                            </div>
                            <div class="grid grid-cols-[auto_auto_1fr] gap-x-2">
                                <span class="text-gray-600">Deskripsi</span><span>:</span>
                                <span>{{ $project->description ?: '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- List Progress --}}
                <div class="mt-4">
                    <div class="scroll-thin grid md:grid-cols-2 gap-4 max-h-[280px] overflow-y-auto pr-1">
                        @forelse($project->progresses as $idx => $pr)
                            @php
                                $last = $pr->updates->sortByDesc('update_date')->first();
                                $realisasi = $last ? (int)($last->percent ?? $last->progress_percent ?? 0) : 0;
                            @endphp

                            <div class="rounded-2xl bg-[#E6CACA] p-4">
                                <div class="font-semibold mb-2">Progress {{ $idx+1 }}{{ $pr->name ? ' — '.$pr->name : '' }}</div>

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
                            </div>
                        @empty
                            <div class="col-span-2 text-sm text-gray-600">Tidak ada progress.</div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('dig.projects.show', $project->id) }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-[#7A1C1C] px-3 py-1.5 text-xs font-semibold text-[#7A1C1C] bg-white hover:bg-[#FFF2F2]">
                        Detail Informasi
                    </a>
                </div>
            </section>
        @empty
            <div class="mt-6">
                <div class="bg-[#EBD0D0] rounded-2xl px-6 py-8 flex items-center justify-center">
                    <div class="rounded-2xl bg-[#CFA8A8] px-5 py-3 text-white/95">Belum ada project yang diarsipkan.</div>
                </div>
            </div>
        @endforelse

        {{-- Pagination (opsional) --}}
        @if(method_exists($projects,'links'))
            <div class="mt-6">{{ $projects->withQueryString()->links() }}</div>
        @endif
    </main>
@endsection
