<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $user = Auth::user();

        // === Anchor minggu untuk KPI ring ===
        $weekParam = request('week'); // ?week=YYYY-MM-DD (opsional)
        $anchor    = $weekParam ? Carbon::parse($weekParam) : now();
        $start     = (clone $anchor)->startOfWeek(Carbon::MONDAY);
        $end       = (clone $anchor)->endOfWeek(Carbon::SUNDAY);

        // === Daftar project untuk LIST DI DASHBOARD IT ===
        // PENTING: tambahkan 'attachments'
        $projects = Project::with([
                'progresses.updates',
                'digitalBanking',
                'developer',
                'attachments',      // ⬅⬅⬅ ini yang bikin lampiran kebaca di Blade
            ])
            ->latest()
            ->get();

        // === KPI Ring Mingguan (akumulasi) ===
        $projectsInWeek = Project::with(['progresses.updates'])
            ->whereHas('progresses', function ($q) use ($start, $end) {
                $q->whereBetween('end_date', [
                    $start->toDateString(),
                    $end->toDateString(),
                ]);
            })
            ->get();

        $isDone = function ($p) {
            // kalau sudah ada meets_requirement / completed_at, anggap selesai
            if (!is_null($p->meets_requirement) || !is_null($p->completed_at)) {
                return true;
            }

            $p->loadMissing('progresses.updates');
            if ($p->progresses->count() === 0) {
                return false;
            }

            return $p->progresses->every(function ($pr) {
                $last = $pr->updates->sortByDesc('update_date')->first();
                $real = $last ? (int) ($last->percent ?? $last->progress_percent ?? 0) : 0;

                return !is_null($pr->confirmed_at)
                    && $real >= (int) $pr->desired_percent;
            });
        };

        $totalWeek       = $projectsInWeek->count();
        $doneWeek        = $projectsInWeek->filter($isDone)->count();
        $accumulationPct = $totalWeek > 0
            ? (int) round(($doneWeek / $totalWeek) * 100)
            : 0;

        // === KPI Arsip (harus sama dengan halaman Arsip) ===
        $meetCount    = Project::archivedStrict()
            ->where('meets_requirement', 1)
            ->count();

        $notMeetCount = Project::archivedStrict()
            ->where('meets_requirement', 0)
            ->count();

        return view('it.dashboard', [
            'projects'        => $projects,

            // KPI ring
            'weekAnchor'      => $anchor->toDateString(),
            'weekStart'       => $start,
            'weekEnd'         => $end,
            'totalInWeek'     => $totalWeek,
            'accumulationPct' => $accumulationPct,

            // KPI arsip
            'meetCount'       => $meetCount,
            'notMeetCount'    => $notMeetCount,

            // untuk avatar dan role di navbar
            'me'              => $user->fresh(),
        ]);
    }
}
