{{-- resources/views/kd/dashboard.blade.php --}}
@extends('layouts.dashboard')

@section('pageTitle', 'Dashboard Kepala Divisi')

@section('styles')
<style>
    /* Sticky header table untuk pengalaman scroll yang enak */
    .table-sticky thead th { position: sticky; top: 0; z-index: 10; }
</style>
@endsection

@section('content')
    {{-- BANNER --}}
    <section class="relative h-[260px] md:h-[320px] overflow-hidden">
      <img src="https://i.pinimg.com/736x/c5/43/71/c543719c97d9efa97da926387fa79d1f.jpg" class="w-full h-full object-cover" alt="Banner" />
      <div class="absolute inset-0 bg-black/30"></div>
      <div class="absolute inset-0 flex items-center justify-center">
        <h1 class="text-white text-2xl md:text-3xl font-bold">Selamat Datang di Timeline Progress</h1>
      </div>
    </section>

    @php
      use Illuminate\Support\Collection;
      use Carbon\Carbon;
      use Illuminate\Support\Str; 

      // Pastikan $projects ada (dari controller). Jika tidak, jadikan koleksi kosong
      $projects = $projects ?? collect();

      // ====== VARIABEL BULAN ======
      $monthStartParam = request('month_start');
      $monthEndParam   = request('month_end');

      try {
        $periodStart = $monthStartParam
          ? Carbon::createFromFormat('Y-m', $monthStartParam)->startOfMonth()
          : Carbon::now()->startOfMonth();
      } catch (\Throwable $e) { $periodStart = Carbon::now()->startOfMonth(); }

      try {
        $periodEnd = $monthEndParam
          ? Carbon::createFromFormat('Y-m', $monthEndParam)->endOfMonth()
          : Carbon::now()->endOfMonth();
      } catch (\Throwable $e) { $periodEnd = Carbon::now()->endOfMonth(); }

      if ($periodEnd->lt($periodStart)) {
        $tmp = $periodStart->copy();
        $periodStart = $periodEnd->copy()->startOfMonth();
        $periodEnd   = $tmp->endOfMonth();
      }

      $monthStartVal = $periodStart->format('Y-m');
      $monthEndVal   = $periodEnd->format('Y-m');
      $ws = $periodStart->format('m/Y');
      $we = $periodEnd->format('m/Y');

      // Range harian utk filter completed_at
      $rangeStart = $periodStart->copy()->startOfDay();
      $rangeEnd   = $periodEnd->copy()->endOfDay();

      // ====== SCOPE (disiapkan kalau nanti mau dipakai, default all) ======
      $scope   = request('scope', 'all');  // 'all' | 'mine'
      $userId  = (int) auth()->id();

      $mine = $projects->filter(function($p) use ($userId) {
        return (int)($p->digital_banking_id ?? 0) === $userId
            || (int)($p->created_by ?? 0) === $userId;
      });
      $all  = $projects;

      // ====== FILTER BERDASARKAN RENTANG BULAN (pakai completed_at) ======
      $filterByPeriod = function(Collection $col) use ($rangeStart, $rangeEnd) {
        return $col->filter(function($p) use ($rangeStart, $rangeEnd) {
          if (empty($p->completed_at)) {
            return false;
          }
          try {
            $completed = Carbon::parse($p->completed_at)->startOfDay();
          } catch (\Throwable $e) {
            return false;
          }
          return $completed->between($rangeStart, $rangeEnd);
        });
      };

      $allPeriod  = $filterByPeriod($all);
      $minePeriod = $filterByPeriod($mine);

      // ====== KPI (berbasis project selesai dalam periode) ======
      $calc = function(Collection $col) {
        $completed   = $col->whereNotNull('completed_at');
        $meet        = $completed->where('meets_requirement', true)->count();
        $notMeet     = $completed->where('meets_requirement', false)->count();
        $completedCt = $completed->count();
        $acc         = $completedCt > 0 ? (int) round(($meet / $completedCt) * 100) : 0;
        return [$meet, $notMeet, $completedCt, $acc];
      };

      // Semua data untuk periode terpilih
      [$meetAll,  $notAll,  $completedAll,  $accAll]  = $calc($allPeriod);
      [$meetMine, $notMine, $completedMine, $accMine] = $calc($minePeriod);

      // Kalau suatu saat KD mau ada scope "mine", sudah siap
      [$meetCount, $notMeetCount, $completedCount, $acc] = $scope === 'mine'
        ? [$meetMine, $notMine, $completedMine, $accMine]
        : [$meetAll,  $notAll,  $completedAll,  $accAll];

      // cincin KPI
      $size = 120; $stroke = 12; $r = $size/2 - $stroke; $circ = 2 * M_PI * $r; $off = $circ * (1 - $acc/100);

      // ====== STATUS PROJECT untuk tabel "Recent Project" (pakai semua project yang dikirim controller) ======
      $projectStatus = [];
      $today = \Illuminate\Support\Carbon::now('Asia/Jakarta')->startOfDay();

      foreach ($projects as $p) {
        $allConfirmed = true;
        $anyNotMeet   = false;
        $anyProgress  = false;
        $isLate       = false;
        $accP         = [];

        foreach (($p->progresses ?? []) as $pr) {
          $anyProgress = true;
          $last = $pr->updates->first(); // diasumsikan relation sudah latest()
          $real = $last ? (int)($last->progress_percent ?? $last->percent ?? 0) : 0;
          $accP[] = $real;

          if (is_null($pr->confirmed_at)) $allConfirmed = false;
          if ($real < (int)$pr->desired_percent) $anyNotMeet = true;

          if (!empty($pr->end_date)) {
            $end = \Illuminate\Support\Carbon::parse($pr->end_date, 'Asia/Jakarta')->endOfDay();
            if ($end->lt($today) && is_null($pr->confirmed_at) && $real < (int)$pr->desired_percent) {
              $isLate = true;
            }
          }
        }

        $currentAvg = count($accP) ? (int) round(array_sum($accP)/max(count($accP),1)) : 0;

        $label = 'To Do';
        if ($isLate) {
          $label = 'Late';
        } elseif ($anyProgress && $currentAvg > 0 && $currentAvg < 100) {
          $label = 'In Progress';
        }
        if ($allConfirmed && !$anyNotMeet) $label = 'Done';

        $projectStatus[$p->id] = $label;
      }
    @endphp

    {{-- ===== KARTU METRIK (KPI) ===== --}}
    <section class="max-w-6xl mx-auto px-5 mt-6 md:mt-8">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

        {{-- 1) Project Akumulasi (cincin akurasi) + filter bulan --}}
        <div class="rounded-2xl bg-[#8D2121] border border-red-200 p-4 min-h-[140px] md:col-span-6">
          <div class="flex items-center justify-between mb-3">
            <div class="font-semibold text-white text-sm">Project Akumulasi</div>
            <form method="GET" action="{{ route('kd.dashboard') }}" class="flex items-center gap-2">
              <input type="month" name="month_start" value="{{ $monthStartVal }}"
                     class="h-8 rounded-lg border border-white/30 bg-[#FFF7F7] px-2 text-xs"
                     title="Pilih bulan awal">
              <span class="text-xs text-white/50 ">s/d</span>
              <input type="month" name="month_end" value="{{ $monthEndVal }}"
                     class="h-8 rounded-lg border border-white/30 bg-[#FFF7F7] px-2 text-xs"
                     title="Pilih bulan akhir">
              <button class="h-8 px-3 rounded-lg bg-white text-[#7A1C1C] text-xs font-semibold">Terapkan</button>
            </form>
          </div>

          <div class="flex items-center gap-4">
            {{-- cincin KPI --}}
            <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}">
              <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}" stroke="rgba(255,255,255,0.25)" stroke-width="{{ $stroke }}" fill="none"/>
              <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}" stroke="#FFFFFF" stroke-width="{{ $stroke }}"
                      stroke-linecap="round" stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $off }}"
                      transform="rotate(-90 {{ $size/2 }} {{ $size/2 }})" fill="none"/>
              <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="18" font-weight="700" fill="#fff">
                {{ $acc }}%
              </text>
            </svg>

            <div class="text-xs text-white/90 min-w-0 break-words">
              <div>Periode: <span class="font-semibold">{{ $ws }}–{{ $we }}</span></div>
              <div>Scope aktif: <span class="font-semibold">{{ $scope==='mine' ? 'Tugas saya' : 'Semua' }}</span></div>
            </div>
          </div>
        </div>

        {{-- 2) Project Selesai, Memenuhi (dalam periode terpilih) --}}
        <div class="rounded-2xl bg-[#8D2121] text-white p-5 grid min-h-[180px] md:col-span-3">
          <div class="flex items-center justify-between mb-3">
            <div class="font-semibold text-sm">Project Selesai, Memenuhi</div>
            <form method="GET" action="{{ route('kd.dashboard') }}" class="flex items-center gap-2">
              <input type="hidden" name="month_start" value="{{ $monthStartVal }}">
              <input type="hidden" name="month_end" value="{{ $monthEndVal }}">
            </form>
          </div>
          <div class="text-5xl font-bold place-self-center">{{ $meetCount }}</div>
          <div class="text-[11px] text-white/90 mt-2 text-center">Semua: {{ $meetAll }}</div>
        </div>

        {{-- 3) Project Selesai, Tidak Memenuhi (dalam periode terpilih) --}}
        <div class="rounded-2xl bg-[#8D2121] text-white p-5 grid min-h-[180px] md:col-span-3">
          <div class="flex items-center justify-between mb-3">
            <div class="font-semibold text-sm">Project Selesai, Tidak Memenuhi</div>
            <form method="GET" action="{{ route('kd.dashboard') }}" class="flex items-center gap-2">
              <input type="hidden" name="month_start" value="{{ $monthStartVal }}">
              <input type="hidden" name="month_end" value="{{ $monthEndVal }}">
            </form>
          </div>
          <div class="text-5xl font-bold place-self-center">{{ $notMeetCount }}</div>
          <div class="text-[11px] text-white/90 mt-2 text-center">Semua: {{ $notAll }}</div>
        </div>

      </div>
    </section>

    {{-- ===== RECENT PROJECT (SCROLLABLE, HEADER FIXED) ===== --}}
    <section class="max-w-6xl mx-auto px-5 mt-6">
      <div class="rounded-t-xl bg-[#7A1C1C] text-white px-5 py-3 flex items-center justify-between">
        <div class="font-semibold">Recent Project</div>
        <a href="{{ route('kd.progresses') }}"
           class="inline-flex items-center gap-2 rounded-full bg-white text-[#7A1C1C] px-4 py-1.5 text-sm font-semibold">
          See All
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13 5l7 7-7 7v-4H4v-6h9V5z"/></svg>
        </a>
      </div>

      {{-- Card + table scroll, tinggi dibatasi agar ~10 baris terlihat --}}
      <div class="rounded-b-xl bg-[#7A1C1C] text-white overflow-hidden">
        <div class="max-h-[420px] overflow-y-auto table-sticky">
          <table class="min-w-full text-sm">
            <thead class="bg-[#7A1C1C]">
              <tr>
                <th class="px-6 py-3 text-left font-semibold border-b border-white/20">Nama Project</th>
                <th class="px-6 py-3 text-left font-semibold border-b border-white/20">Penanggung Jawab (Developer)</th>
                <th class="px-6 py-3 text-left font-semibold border-b border-white/20">Penanggung Jawab (DIG)</th>
                <th class="px-6 py-3 text-left font-semibold border-b border-white/20">Status</th>
                {{-- KOLOM BARU: Lampiran --}}
                <th class="px-6 py-3 text-left font-semibold border-b border-white/20">Lampiran</th>
              </tr>
            </thead>
            <tbody>
              @forelse($projects as $p)
                @php
                  $devName = $p->developer->name ?? '-';
                  $digName = $p->digitalBanking->name ?? '-';
                  $status  = $projectStatus[$p->id] ?? 'To Do';

                  $badge = match($status) {
                    'Done'        => ['bg' => 'bg-green-300', 'text' => 'Done'],
                    'In Progress' => ['bg' => 'bg-blue-300',  'text' => 'In Progress'],
                    'Late'        => ['bg' => 'bg-yellow-300','text' => 'Late'],
                    default       => ['bg' => 'bg-gray-300',  'text' => 'To Do'],
                  };

                  $attachments = $p->attachments ?? collect();
                @endphp
                <tr class="odd:bg-[#7A1C1C] even:bg-[#8a2a2a]">
                  <td class="px-6 py-3 border-t border-white/10">{{ $p->name }}</td>
                  <td class="px-6 py-3 border-t border-white/10">{{ $devName }}</td>
                  <td class="px-6 py-3 border-t border-white/10">{{ $digName }}</td>
                  <td class="px-6 py-3 border-t border-white/10">
                    <span class="inline-flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full {{ $badge['bg'] }}"></span> {{ $badge['text'] }}
                    </span>
                  </td>
                  <td class="px-6 py-3 border-t border-white/10">
                    @if($attachments->isNotEmpty())
                      <div class="flex flex-wrap gap-1">
                        @foreach($attachments as $att)
                          @php
                            $isPdf =
                              str_contains(strtolower($att->mime_type ?? ''), 'pdf') ||
                              Str::endsWith(strtolower($att->original_name ?? ''), '.pdf');

                            $url = route('attachments.show', $att->id);
                          @endphp
                          <a href="{{ $url }}" target="_blank"
                             class="inline-flex items-center gap-1 rounded-full border border-white/40 bg-white/10 px-2 py-0.5 text-[11px] hover:bg-white/20">
                            @if($isPdf)
                              <span class="inline-flex w-5 h-5 items-center justify-center rounded-full bg-red-600 text-white text-[9px] leading-none">
                                PDF
                              </span>
                            @else
                              <span class="inline-flex w-5 h-5 items-center justify-center rounded-full bg-amber-500 text-white text-[9px] leading-none">
                                IMG
                              </span>
                            @endif
                            <span class="truncate max-w-[120px]" title="{{ $att->original_name }}">
                              {{ $att->original_name }}
                            </span>
                          </a>
                        @endforeach
                      </div>
                    @else
                      <span class="text-[11px] text-white/70">-</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-6 py-4 text-center text-white/90">Belum ada project.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <div class="pb-10"></div>
@endsection
