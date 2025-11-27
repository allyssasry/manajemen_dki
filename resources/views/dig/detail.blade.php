{{-- resources/views/dig/dashboard.blade.php --}}
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dashboard DIG</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html {
            scrollbar-gutter: stable;
        }

        body {
            overflow-x: hidden;
        }
    </style>
</head>

<body class="min-h-screen bg-white text-gray-900">
    @php
        use Illuminate\Support\Str;

        /* ========= USER, ROLE & RUTE DINAMIS ========= */
        $me = $me ?? auth()->user()?->fresh();
        $role = $me?->role;
        $isKepalaDivisi = $role === 'kepala_divisi';

        // Tentukan nama rute "dashboard/home" per role
        $homeRouteName = match ($role) {
            'it' => \Route::has('it.dashboard') ? 'it.dashboard' : null,
            'supervisor' => \Route::has('supervisor.dashboard') ? 'supervisor.dashboard' : null,
            'kepala_divisi' => \Route::has('kd.dashboard') ? 'kd.dashboard' : null,
            'digital_banking' => \Route::has('dig.dashboard') ? 'dig.dashboard' : null,
            default => \Route::has('dig.dashboard') ? 'dig.dashboard' : null,
        };

        // URL tujuan back button
        $homeUrl = $homeRouteName
            ? route($homeRouteName)
            : (\Route::has('dig.dashboard')
                ? route('dig.dashboard')
                : url('/'));
    @endphp

    @php
        $container = 'max-w-6xl mx-auto w-full px-5 md:px-6';
    @endphp

    <header class="w-full bg-[#8D2121] text-white">
        <div class="{{ $container }} flex items-center justify-between py-3">
            <span class="font-semibold text-sm md:text-base tracking-wide">
                Detail Informasi
            </span>
        </div>
    </header>

    <div class="max-w-6xl mx-auto px-5 py-6">
        {{-- ====== STATUS PROJECT + LOGIKA FINALISASI ====== --}}
        @php
            // 1) Cek apakah semua progress sudah capai target DAN dikonfirmasi
            $allMetAndConfirmed = $project->progresses->every(function ($pr) {
                $last = $pr->updates->sortByDesc('update_date')->first();
                $real = $last ? (int) ($last->percent ?? ($last->progress_percent ?? 0)) : 0;
                return $real >= (int) $pr->desired_percent && !is_null($pr->confirmed_at);
            });

            // 2) Default status project: SELALU "Dalam Proses" sampai DIG finalisasi
            $statusText = 'Dalam Proses';
            $statusColor = '#7A1C1C';
            $statusBg = '#FEF2F2';

            // 3) HANYA saat sudah difinalisasi oleh DIG, ubah status akhir
            if (!is_null($project->completed_at)) {
                if ($project->meets_requirement === true) {
                    $statusText = 'Project Selesai, Memenuhi';
                    $statusColor = '#166534';
                    $statusBg = '#F0FDF4';
                } elseif ($project->meets_requirement === false) {
                    $statusText = 'Project Selesai, Tidak Memenuhi';
                    $statusColor = '#7A1C1C';
                    $statusBg = '#FEE2E2';
                } else {
                    $statusText = 'Project Selesai';
                    $statusColor = '#334155';
                    $statusBg = '#F1F5F9';
                }
            }

            // 4) Flag final: project sudah diputus memenuhi / tidak memenuhi
            $finalized = !is_null($project->meets_requirement);

            // 5) Tombol finalisasi boleh muncul jika:
            //    - semua progress selesai & terkonfirmasi
            //    - BELUM ada keputusan
            //    - role yg buka = digital_banking
            //    - BUKAN kepala_divisi
            $canDecideCompletion =
                !$isKepalaDivisi &&
                $role === 'digital_banking' &&
                $allMetAndConfirmed &&
                is_null($project->meets_requirement);
        @endphp

        {{-- BARIS JUDUL/AKSI --}}
        <div class="flex items-center justify-between">
            <h1 class="text-[15px] md:text-[16px] font-semibold" style="color: {{ $statusColor }};">
                {{ $statusText }}
            </h1>
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center justify-center rounded-full px-5 py-2 text-xs md:text-sm font-semibold bg-[#8D2121] text-white hover:bg-[#6d1a1a]">
                Kembali
            </a>
        </div>

        {{-- RING + INFO HEADER + CTA TAMBAH PROGRESS --}}
        @php
            $latest = [];
            foreach ($project->progresses as $p) {
                $u = $p->updates->sortByDesc('update_date')->first();
                $latest[] = $u ? (int) ($u->percent ?? ($u->progress_percent ?? 0)) : 0;
            }
            $realization = count($latest) ? (int) round(array_sum($latest) / max(count($latest), 1)) : 0;
            $size = 84;
            $stroke = 10;
            $r = $size / 2 - $stroke;
            $circ = 2 * M_PI * $r;
            $off = $circ * (1 - $realization / 100);
        @endphp

        <div class="mt-2 grid grid-cols-1 md:grid-cols-[auto,1fr,auto] gap-6 items-center">
            {{-- RING --}}
            <div class="flex items-center gap-4">
                <svg width="{{ $size }}" height="{{ $size }}"
                    viewBox="0 0 {{ $size }} {{ $size }}">
                    <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}" stroke="#E5B9B9"
                        stroke-width="{{ $stroke }}" fill="none" opacity=".65" />
                    <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}"
                        stroke="#7A1C1C" stroke-width="{{ $stroke }}" stroke-linecap="round"
                        stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $off }}"
                        transform="rotate(-90 {{ $size / 2 }} {{ $size / 2 }})" fill="none" />
                    <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" class="fill-[#222]"
                        font-size="16" font-weight="700">{{ $realization }}%</text>
                </svg>
            </div>

            {{-- INFO UTAMA --}}
            <div class="grid sm:grid-cols-2 gap-y-1 gap-x-10 text-[13px] leading-5">
                <div><span class="inline-block w-40 text-gray-700">Nama Project</span> : <span
                        class="font-semibold">{{ $project->name }}</span></div>
                <div><span class="inline-block w-40 text-gray-700">Penanggung Jawab (DIG)</span> :
                    {{ optional($project->digitalBanking)->name ?? '-' }}</div>
                <div><span class="inline-block w-40 text-gray-700">Penanggung Jawab (Developer)</span> :
                    {{ optional($project->developer)->name ?? '-' }}</div>
                <div><span class="inline-block w-40 text-gray-700">Deskripsi</span> :
                    {{ $project->description ?: '-' }}</div>
            </div>

            {{-- AKSI PROJECT + CTA TAMBAH PROGRESS --}}
            <div class="flex flex-col items-end gap-2 w-full">
                @unless ($isKepalaDivisi)
                    {{-- ==== TOMBOL TAMBAH PROGRESS (LOCK SETELAH FINAL) ==== --}}
                    <div class="mt-4 flex justify-end w-full">
                        <button type="button" data-target="progressForm-{{ $project->id }}"
                            @if ($finalized) disabled
                  title="Project sudah difinalisasi, progress baru tidak dapat ditambahkan."
                  class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm shadow bg-[#7A1C1C]/40 text-white cursor-not-allowed opacity-60"
              @else
                  class="btn-toggle-progress inline-flex items-center gap-2 rounded-xl bg-[#7A1C1C] text-white px-3 py-2 text-sm shadow" @endif>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z" />
                            </svg>
                            Tambah Progress
                        </button>
                    </div>
                    {{-- ==== /TOMBOL TAMBAH PROGRESS ==== --}}
                @endunless

                {{-- TOMBOL FINALISASI (hanya saat layak, hanya DIG, bukan kepala divisi) --}}
                @if ($canDecideCompletion)
                    <div class="flex items-center gap-2 mt-1">
                        <form method="POST" action="{{ route('projects.setCompletion', $project->id) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="meets" value="1">
                            <button class="px-3 py-1.5 text-xs rounded-full bg-green-700 text-white hover:opacity-90">
                                Memenuhi
                            </button>
                        </form>
                        <form method="POST" action="{{ route('projects.setCompletion', $project->id) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="meets" value="0">
                            <button class="px-3 py-1.5 text-xs rounded-full bg-[#7A1C1C] text-white hover:opacity-90">
                                Tidak Memenuhi
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- ====== LAMPIRAN PROJECT (VIEW ONLY, SEMUA ROLE BISA LIHAT) --}}
        @if ($project->attachments && $project->attachments->isNotEmpty())
            <section class="mt-4">
                <div class="text-[13px] font-semibold text-gray-800 mb-1">Lampiran Project</div>
                <p class="text-[12px] text-gray-600 mb-2">
                    Dokumen pendukung project (BRD, desain, screenshot, dsb.). Klik untuk membuka.
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($project->attachments as $att)
                        @php
                            $isPdf =
                                str_contains(strtolower($att->mime_type ?? ''), 'pdf') ||
                                Str::endsWith(strtolower($att->original_name), '.pdf');
                            $url = route('attachments.show', $att->id);
                        @endphp
                        <a href="{{ $url }}" target="_blank"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs border border-[#C89898] bg-[#FFF7F7] hover:bg-[#FDEEEE]">
                            @if ($isPdf)
                                <span
                                    class="inline-flex w-5 h-5 items-center justify-center rounded-full bg-red-600 text-white text-[10px]">PDF</span>
                            @else
                                <span
                                    class="inline-flex w-5 h-5 items-center justify-center rounded-full bg-amber-500 text-white text-[10px]">IMG</span>
                            @endif
                            <span class="truncate max-w-[180px]"
                                title="{{ $att->original_name }}">{{ $att->original_name }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- FORM TAMBAH PROGRESS (HIDDEN, HANYA JIKA BELUM FINAL & BUKAN KEPALA DIVISI) --}}
        @if (!$isKepalaDivisi && !$finalized)
            <div id="progressForm-{{ $project->id }}"
                class="hidden mt-3 rounded-xl bg-white p-4 border border-[#E7C9C9]">
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
                    <input type="number" name="desired_percent" required min="1" max="100"
                        placeholder="Target %"
                        class="rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none" />

                    <button
                        class="rounded-xl border-2 border-[#7A1C1C] bg-[#E2B9B9] px-4 py-2 font-semibold hover:bg-[#D9AFAF]">
                        Tambah
                    </button>
                </form>
            </div>
        @endif

        {{-- KARTU PROGRESS --}}
        <div class="mt-6 space-y-5">
            @forelse($project->progresses as $i => $pr)
                @php
                    $last = $pr->updates->sortByDesc('update_date')->first();
                    $realisasi = $last ? (int) ($last->percent ?? ($last->progress_percent ?? 0)) : 0;
                    $canConfirm = $realisasi >= (int) $pr->desired_percent && !$pr->confirmed_at;

                    // Hanya pembuat progress
                    $isOwner = (int) ($pr->created_by ?? 0) === (int) auth()->id();
                    $creator = $pr->creator ?? null;
                    $ownerRoleLabel =
                        optional($creator)->role === 'digital_banking'
                            ? 'DIG'
                            : (optional($creator)->role === 'it'
                                ? 'IT'
                                : '—');

                    // Flag "Tidak Memenuhi" per progress
                    $endDate = $pr->end_date ? \Illuminate\Support\Carbon::parse($pr->end_date)->startOfDay() : null;
                    $isOverdue = $endDate ? $endDate->lt(now()->startOfDay()) : false;
                    $isUnmet = $isOverdue && is_null($pr->confirmed_at) && $realisasi < (int) $pr->desired_percent;

                    // Flag umum: boleh ubah progress ini atau tidak
                    $canMutate = !$finalized && $isOwner; // edit/hapus/update/catatan
                    $canConfirmNow = !$finalized && $isOwner && $canConfirm; // konfirmasi
                @endphp

                <section class="rounded-[16px] bg-[#E3BDBD]/60 border border-[#C99E9E] px-5 py-4">
                    {{-- HEAD ROW --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-[14px] font-semibold text-[#2d2d2d]">
                            {{-- ✅ Progress + nomor + nama progress --}}
                            <span>
                                Progress {{ $i + 1 }} — {{ $pr->name }}
                            </span>

                            {{-- ✅ Status di sampingnya --}}
                            @if ($pr->confirmed_at)
                                <span class="ml-2 inline-flex items-center rounded-full bg-green-100 text-green-700 px-2 py-0.5 text-[11px]">
                                    Selesai
                                </span>
                            @else
                                <span class="ml-2 inline-flex items-center rounded-full bg-[#FEE2E2] text-[#7A1C1C] px-2 py-0.5 text-[11px]">
                                    Dalam Proses
                                </span>
                                @if ($isUnmet)
                                    <span
                                        class="ml-2 inline-flex items-center rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-[11px] font-semibold">
                                        Tidak Memenuhi
                                    </span>
                                @endif
                            @endif
                        </div>

                        {{-- AKSI (EDIT / HAPUS / KONFIRMASI) – disembunyikan untuk kepala divisi --}}
                        @unless ($isKepalaDivisi)
                            <div class="flex items-center gap-2">
                                {{-- TOGGLE EDIT PROGRESS --}}
                                <button type="button"
                                    class="px-3 py-1.5 text-xs rounded-lg border bg-white/70 hover:bg-white btn-edit-progress disabled:opacity-50"
                                    data-target="editProgress-{{ $pr->id }}"
                                    data-progress-id="{{ $pr->id }}"
                                    @if (!$canMutate) disabled
                        title="{{ $finalized ? 'Project sudah difinalisasi, progress tidak dapat diubah.' : 'Hanya pembuat progress yang dapat mengedit' }}" @endif>
                                    Edit
                                </button>

                                {{-- HAPUS PROGRESS --}}
                                <form method="POST" action="{{ route('progresses.destroy', $pr->id) }}"
                                    data-confirm-delete="true" data-message="Hapus progress ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="px-3 py-1.5 text-xs rounded-lg border bg-white/70 hover:bg-white disabled:opacity-50"
                                        @if (!$canMutate) disabled
                          title="{{ $finalized ? 'Project sudah difinalisasi, progress tidak dapat dihapus.' : 'Hanya pembuat progress yang dapat menghapus' }}" @endif>
                                        Hapus
                                    </button>
                                </form>

                                {{-- KONFIRMASI SELESAI --}}
                                @if ($pr->confirmed_at)
                                    <span
                                        class="rounded-full bg-[#7A1C1C] text-white px-4 py-1 text-[12px] font-semibold">Selesai</span>
                                @else
                                    <form method="POST" action="{{ route('progresses.confirm', $pr->id) }}">
                                        @csrf
                                        <button
                                            class="rounded-full bg-[#7A1C1C] text-white px-4 py-1 text-[12px] font-semibold disabled:opacity-50"
                                            @if (!$canConfirmNow) disabled
                            title="{{ $finalized
                                ? 'Project sudah difinalisasi, progress tidak dapat dikonfirmasi.'
                                : ($isOwner
                                    ? 'Belum mencapai target'
                                    : 'Hanya pembuat progress yang dapat konfirmasi') }}" @endif>
                                            Konfirmasi
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endunless
                    </div>

                    {{-- BODY GRID (info / riwayat / formulir) --}}
                    <div class="grid md:grid-cols-[1fr,1fr,340px] gap-6">
                        {{-- INFO TIMELINE + RIWAYAT CHIP --}}
                        <div class="text-[13px] leading-6">
                            <div>
                                <span class="inline-block w-40 text-gray-700">Timeline Mulai</span> :
                                {{ $pr->start_date ? \Illuminate\Support\Carbon::parse($pr->start_date)->timezone('Asia/Jakarta')->format('d M Y') : '-' }}
                            </div>
                            <div>
                                <span class="inline-block w-40 text-gray-700">Timeline Selesai</span> :
                                {{ $pr->end_date ? \Illuminate\Support\Carbon::parse($pr->end_date)->timezone('Asia/Jakarta')->format('d M Y') : '-' }}
                                @if ($isOverdue && is_null($pr->confirmed_at))
                                    <span
                                        class="ml-2 inline-flex items-center rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-[11px] font-semibold">
                                        Telat timeline
                                    </span>
                                @endif
                            </div>

                            <div><span class="inline-block w-40 text-gray-700">Target Progress</span> :
                                {{ $pr->desired_percent }}%</div>
                            <div><span class="inline-block w-40 text-gray-700">Realisasi Progress</span> :
                                {{ $realisasi }}%</div>

                            {{-- Label pembuat progress --}}
                            <div class="mt-2 text-xs text-gray-600">
                                Dibuat oleh <b>{{ $ownerRoleLabel }}</b> — {{ $creator->name ?? '—' }}
                            </div>

                            <div class="mt-3">
                                <div class="text-[12px] font-semibold text-gray-700 mb-1">Riwayat Progress Harian</div>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($pr->updates->sortByDesc('update_date')->take(6) as $up)
                                        <span
                                            class="px-3 py-1 rounded-full border border-[#C89898] bg:white/80 text-[11px]">
                                            {{ \Illuminate\Support\Carbon::parse($up->update_date)->format('d M') }} :
                                            {{ $up->percent ?? $up->progress_percent }}%
                                        </span>
                                    @empty
                                        <span class="text-[12px] text-gray-500">Belum ada riwayat.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- CATATAN DB & IT --}}
                        <div>
                            <div class="text-[12px] font-semibold text-gray-700 mb-1">Catatan Digital Banking & IT
                            </div>
                            <div
                                class="rounded-[12px] bg-white/80 border border-[#C89898] p-3 h-[140px] overflow-auto text-[13px]">
                                @forelse($pr->notes as $note)
                                    <div class="mb-2">
                                        <div class="text-[11px] text-gray-500">
                                            {{ strtoupper($note->role) }} •
                                            {{ \Illuminate\Support\Carbon::parse($note->created_at)->format('d M Y') }}
                                        </div>
                                        <div>{{ $note->body }}</div>
                                    </div>
                                @empty
                                    <div class="text-gray-500">Belum ada catatan.</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- PANEL KANAN: UPDATE & CATATAN --}}
                        <div class="rounded-[14px] bg-white border border-[#E0BEBE] p-4">
                            <div class="flex items-center justify-end mb-2">
                                @if ($pr->confirmed_at)
                                    <span
                                        class="inline-block text-[11px] rounded-full bg-green-100 text-green-700 px-2 py-0.5">Progress
                                        Selesai</span>
                                @endif
                            </div>

                            @if ($finalized)
                                <p class="mb-3 text-[11px] text-gray-500">
                                    Project sudah difinalisasi, update progress dan catatan tidak dapat dilakukan.
                                </p>
                            @endif

                            {{-- FORM EDIT PROGRESS (INLINE, HIDDEN) - tidak ditampilkan untuk kepala divisi --}}
                            @unless ($isKepalaDivisi)
                                <div id="editProgress-{{ $pr->id }}" class="hidden mb-4">
                                    <div class="text-[12px] text-gray-700 font-semibold mb-1">Edit Progress</div>
                                    <form method="POST" action="{{ route('progresses.update', $pr->id) }}"
                                        class="grid grid-cols-1 gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input name="name" value="{{ old('name', $pr->name) }}" required
                                            class="rounded-xl bg-[#F6EAEA] border border-[#C89898] px-3 py-2 text-[13px]"
                                            placeholder="Nama progress" @if (!$canMutate) disabled @endif>
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="date" name="start_date"
                                                value="{{ old('start_date', $pr->start_date) }}" required
                                                class="rounded-xl bg-[#F6EAEA] border border-[#C89898] px-3 py-2 text-[13px]"
                                                @if (!$canMutate) disabled @endif>
                                            <input type="date" name="end_date"
                                                value="{{ old('end_date', $pr->end_date) }}" required
                                                class="rounded-xl bg-[#F6EAEA] border border-[#C89898] px-3 py-2 text-[13px]"
                                                @if (!$canMutate) disabled @endif>
                                        </div>

                                        {{-- EDIT TARGET %: BISA KETIK 1–100 --}}
                                        <input type="number" name="desired_percent" min="1" max="100"
                                            value="{{ old('desired_percent', $pr->desired_percent) }}" required
                                            placeholder="Target %"
                                            class="rounded-xl bg-[#F6EAEA] border border-[#C89898] px-3 py-2 text-[13px]"
                                            @if (!$canMutate) disabled @endif>

                                        <div class="flex justify-end gap-2">
                                            {{-- BUTTON BATAL --}}
                                            <button type="button"
                                                class="h-[40px] px-4 rounded-full border border-gray-300 bg-white text-sm"
                                                data-cancel-edit="editProgress-{{ $pr->id }}"
                                                data-progress-id="{{ $pr->id }}">
                                                Batal
                                            </button>

                                            {{-- BUTTON SIMPAN --}}
                                            <button
                                                class="inline-flex items-center justify-center
                             h-[40px] min-w-[160px] px-5
                             rounded-full border-2 border-[#7A1C1C]
                             bg-[#E2B9B9] hover:bg-[#D9AFAF]
                             font-semibold text-sm whitespace-nowrap disabled:opacity-50"
                                                @if (!$canMutate) disabled
                        title="{{ $finalized ? 'Project sudah difinalisasi, progress tidak dapat diubah.' : 'Hanya pembuat progress yang dapat menyimpan perubahan' }}" @endif>
                                                Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- WRAPPER UNTUK UPDATE & CATATAN (DITUTUP SAAT EDIT) --}}
                                <div id="updateNote-{{ $pr->id }}">
                                    {{-- UPDATE PROGRESS (tgl otomatis harian) --}}
                                    <div class="text-[12px] text-gray-700 font-semibold mb-1">Update Progress</div>
                                    <form method="POST" action="{{ route('progresses.updates.store', $pr->id) }}"
                                        class="space-y-2">
                                        @csrf
                                        <input type="date" name="update_date" value="{{ now()->toDateString() }}"
                                            readonly
                                            class="w-full rounded-[10px] bg-[#F6EAEA] border border-[#C89898] px-3 py-2 text-[13px]">
                                        <div class="flex gap-2">
                                            <input type="number" name="percent" min="0" max="100"
                                                placeholder="Progress %"
                                                class="w-full rounded-[10px] bg-[#F6EAEA] border border-[#C89898] px-3 py-2 text-[13px]"
                                                required @if (!$canMutate) disabled @endif>
                                            <button
                                                class="rounded-[10px] bg-[#7A1C1C] text-white px-4 py-2 text-[12px] font-semibold disabled:opacity-50"
                                                @if (!$canMutate) disabled
                              title="{{ $finalized ? 'Project sudah difinalisasi, update progress tidak dapat dilakukan.' : 'Hanya pembuat progress yang dapat melakukan update' }}" @endif>
                                                Update
                                            </button>
                                        </div>
                                    </form>

                                    {{-- TAMBAH CATATAN --}}
                                    <div class="mt-4 text-[12px] text-gray-700 font-semibold mb-1">Tambah Catatan</div>
                                    <form method="POST" action="{{ route('progresses.notes.store', $pr->id) }}"
                                        class="space-y-2">
                                        @csrf
                                        <textarea name="body" rows="3" placeholder="Catatan"
                                            class="w-full rounded-[10px] bg-[#F6EAEA] border border-[#C89898] px-3 py-2 text-[13px]"
                                            @if (!$canMutate) disabled @endif></textarea>
                                        <button
                                            class="w-full rounded-[12px] border-2 border-[#7A1C1C] bg-[#E2B9B9] hover:bg-[#D9AFAF] text-[13px] py-2 font-semibold disabled:opacity-50"
                                            @if (!$canMutate) disabled
                            title="{{ $finalized ? 'Project sudah difinalisasi, catatan baru tidak dapat ditambahkan.' : '' }}" @endif>
                                            Simpan Catatan
                                        </button>
                                    </form>
                                </div>
                            @endunless

                            @if ($isKepalaDivisi)
                                <p class="mt-2 text-[11px] text-gray-500">
                                    Kepala Divisi hanya dapat melihat detail project dan progress tanpa mengubah data.
                                </p>
                            @endif
                        </div>
                    </div>
                </section>
            @empty
                <div class="text-sm text-gray-600">Belum ada progress.</div>
            @endforelse
        </div>
    </div>

     {{-- ===== MODAL KONFIRMASI HAPUS (PROJECT / PROGRESS) ===== --}}
    <div id="confirmDeleteModal"
         class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40">
        <div class="mx-4 w-full max-w-sm rounded-2xl bg-white shadow-xl border border-red-100 overflow-hidden">
            <div class="flex items-center gap-3 px-4 py-3 bg-[#7A1C1C] text-white">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                         viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11 15h2v2h-2zm0-8h2v6h-2z"/>
                        <path d="M12 2C6.49 2 2 6.49 2 12s4.49 10 10 10
                                 10-4.49 10-10S17.51 2 12 2zm0 18
                                 c-4.41 0-8-3.59-8-8s3.59-8 8-8
                                 8 3.59 8 8-3.59 8-8 8z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-semibold">Konfirmasi Hapus</div>
                    <div class="text-xs text-white/80">Aksi ini tidak bisa dibatalkan.</div>
                </div>
            </div>
            <div class="px-4 py-4 text-sm text-gray-700" id="confirmDeleteMessage">
                Yakin ingin menghapus data ini?
            </div>
            <div class="flex justify-end gap-2 px-4 py-3 bg-[#FFF7F7]">
                <button type="button"
                        id="cancelDeleteBtn"
                        class="inline-flex items-center justify-center rounded-xl border border-red-200 px-4 py-1.5 text-xs font-semibold text-[#7A1C1C] bg-white hover:bg-red-50">
                    Batal
                </button>
                <button type="button"
                        id="confirmDeleteBtn"
                        class="inline-flex items-center justify-center rounded-xl border border-[#7A1C1C] px-4 py-1.5 text-xs font-semibold text-white bg-[#8D2121] hover:bg-[#741B1B]">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
    {{-- Toggle script untuk form Tambah Progress & Edit Progress --}}
    <script>
        // toggle form tambah progress (project) - pakai class .btn-toggle-progress
        document.querySelectorAll('.btn-toggle-progress').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.disabled) return;
                const id = btn.getAttribute('data-target');
                const el = document.getElementById(id);
                if (!el) return;
                el.classList.toggle('hidden');
            });
        });

        // toggle setiap form edit progress (inline) + sembunyikan Update & Catatan saat edit aktif
        document.querySelectorAll('.btn-edit-progress').forEach(b => {
            b.addEventListener('click', () => {
                if (b.disabled) return;
                const target = b.getAttribute('data-target');
                const progressId = b.getAttribute('data-progress-id');
                const editEl = document.getElementById(target);
                const updateNoteEl = document.getElementById('updateNote-' + progressId);
                if (!editEl) return;

                const willShow = editEl.classList.contains('hidden'); // kondisi sebelum toggle
                editEl.classList.toggle('hidden');

                if (updateNoteEl) {
                    if (willShow) {
                        // Saat edit dibuka → update & catatan ditutup
                        updateNoteEl.classList.add('hidden');
                    } else {
                        // Saat edit ditutup lagi → update & catatan muncul
                        updateNoteEl.classList.remove('hidden');
                    }
                }
            });
        });

        // tombol Batal di form edit progress → tutup edit & buka lagi update+catatan
        document.querySelectorAll('[data-cancel-edit]').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-cancel-edit');
                const progressId = btn.getAttribute('data-progress-id');
                const editEl = document.getElementById(target);
                const updateNoteEl = document.getElementById('updateNote-' + progressId);

                if (editEl) editEl.classList.add('hidden');
                if (updateNoteEl) updateNoteEl.classList.remove('hidden');
            });
        });
    </script>
    <script>
        (function() {
            let pendingLogoutHref = null;
            let pendingDeleteForm = null;

            const logoutModal = document.getElementById('confirmLogoutModal');
            const deleteModal = document.getElementById('confirmDeleteModal');
            const deleteMsgEl = document.getElementById('confirmDeleteMessage');

            const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
            const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');

            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

            function openModal(modal) {
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal(modal) {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }

            // ====== LOGOUT HANDLER ======
            document.querySelectorAll('[data-confirm-logout="true"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    pendingLogoutHref = this.getAttribute('href');
                    openModal(logoutModal);
                });
            });

            confirmLogoutBtn?.addEventListener('click', function() {
                if (pendingLogoutHref) {
                    window.location.href = pendingLogoutHref;
                }
            });

            cancelLogoutBtn?.addEventListener('click', function() {
                pendingLogoutHref = null;
                closeModal(logoutModal);
            });

            // Klik di luar card = tutup modal logout
            logoutModal?.addEventListener('click', function(e) {
                if (e.target === logoutModal) {
                    pendingLogoutHref = null;
                    closeModal(logoutModal);
                }
            });

            // ====== DELETE HANDLER (project / progress) ======
            document.querySelectorAll('form[data-confirm-delete="true"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    pendingDeleteForm = this;

                    const msg = this.getAttribute('data-message');
                    if (msg && deleteMsgEl) {
                        deleteMsgEl.textContent = msg;
                    }

                    openModal(deleteModal);
                });
            });

            confirmDeleteBtn?.addEventListener('click', function() {
                if (pendingDeleteForm) {
                    const formToSubmit = pendingDeleteForm;
                    pendingDeleteForm = null;
                    closeModal(deleteModal);
                    formToSubmit.submit();
                }
            });

            cancelDeleteBtn?.addEventListener('click', function() {
                pendingDeleteForm = null;
                closeModal(deleteModal);
            });

            // Klik di luar card = tutup modal delete
            deleteModal?.addEventListener('click', function(e) {
                if (e.target === deleteModal) {
                    pendingDeleteForm = null;
                    closeModal(deleteModal);
                }
            });

            // ESC key untuk nutup modal (kalau ada yang kebuka)
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (logoutModal && !logoutModal.classList.contains('hidden')) {
                        pendingLogoutHref = null;
                        closeModal(logoutModal);
                    }
                    if (deleteModal && !deleteModal.classList.contains('hidden')) {
                        pendingDeleteForm = null;
                        closeModal(deleteModal);
                    }
                }
            });
        })();
    </script>
</body>
</html>
