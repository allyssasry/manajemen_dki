<?php
// app/Http/Controllers/ProgressController.php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Notifications\ProgressConfirmed;               // existing (notifikasi ke DIG)
use App\Notifications\SupervisorStatusNotification;    // existing (notifikasi ke Supervisor)
use App\Notifications\ProgressChangedNotification;     // NEW: notifikasi ke IT
use App\Support\NotifyTargets;                         // NEW: helper target IT

class ProgressController extends Controller
{
    use AuthorizesRequests;

    /**
     * GET /progresses (route name: semua.progresses)
     */
    public function index(Request $request)
    {
        $status = strtolower($request->get('status', 'all'));

        $base = Project::query()
            ->with([
                'progresses' => function ($q) {
                    $q->with([
                        'creator',
                        'updates' => fn($u) => $u->orderByDesc('update_date'),
                    ]);
                },
                'digitalBanking',
                'developer',
            ]);

        if ($status === 'done') {
            $base->finalized()->allProgressConfirmed();
        } elseif ($status === 'in_progress') {
            $base->where(function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNull('completed_at')
                       ->whereNull('meets_requirement');
                })->orWhere(function ($qq) {
                    $qq->finalized()->hasUnconfirmedOrNoProgress();
                });
            });
        }

        $projects = $base->latest('id')->get();

        return view('semua.progresses', compact('projects'));
    }

    /**
     * POST /projects/{project}/progresses
     * Tambah progress baru ke project.
     * route name: projects.progresses.store
     */
    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'            => ['required','string','max:255'],
            'start_date'      => ['required','date'],
            'end_date'        => ['required','date','after_or_equal:start_date'],
            'desired_percent' => ['required','integer','min:0','max:100'],
        ]);

        $data['created_by'] = Auth::id();
        $progress = $project->progresses()->create($data);

        // === NOTIFIKASI ke IT (developer) bahwa DIG membuat progress baru ===
        $actor = $request->user();
        $payload = [
            'type'          => 'progress_created',
            'project_id'    => $project->id,
            'project_name'  => $project->name,
            'progress_id'   => $progress->id,
            'progress_name' => $progress->name,
            'actor_id'      => $actor->id,
            'actor_name'    => $actor->name ?? $actor->username ?? 'User',
            'meta'          => [
                'start_date'      => $progress->start_date,
                'end_date'        => $progress->end_date,
                'desired_percent' => (int)$progress->desired_percent,
            ],
        ];

        foreach (NotifyTargets::itFor($project) as $target) {
            $target->notify(new ProgressChangedNotification($payload));
        }

        // ✅ balik ke halaman yang barusan submit (progress page / detail)
        return back()->with('success','Progress berhasil ditambahkan.');
    }

    /** Alias kompatibilitas. */
    public function storeForProject(Request $request, Project $project)
    {
        return $this->store($request, $project);
    }

    /**
     * PUT /progresses/{progress}
     * Edit metadata progress. Hanya pemilik (created_by) yang boleh.
     * route name: progresses.update
     */
    public function update(Request $request, Progress $progress)
    {
        // ability disamakan dengan yang dipakai di update/confirm di tempat lain
        $this->authorize('createUpdate', $progress);

        $data = $request->validate([
            'name'            => ['required','string','max:255'],
            'start_date'      => ['required','date'],
            'end_date'        => ['required','date','after_or_equal:start_date'],
            'desired_percent' => ['required','integer','min:0','max:100'],
        ]);

        $progress->update($data);

        // ✅ tetap di halaman progress
        return back()->with('success', 'Progress berhasil diperbarui.');
    }

    /**
     * DELETE /progresses/{progress}
     * Hapus progress. Hanya pemilik yang boleh.
     * route name: progresses.destroy
     */
    public function destroy(Progress $progress)
    {
        // (opsional) batasi hanya pembuat yang boleh hapus:
        if ((int)$progress->created_by !== (int)auth()->id()) {
            abort(403, 'Tidak boleh menghapus progress ini.');
        }

        // Hapus dulu relasi terkait (kalau tidak pakai cascade)
        if (method_exists($progress, 'updates')) {
            $progress->updates()->delete();
        }
        if (method_exists($progress, 'notes')) {
            $progress->notes()->delete();
        }

        $progress->delete();

        // ✅ balik ke halaman yang memanggil (progress list / detail)
        return back()->with('success', 'Progress berhasil dihapus.');
    }

    /**
     * POST /progresses/{progress}/updates
     * Simpan update harian (realisasi %). Hanya pemilik progress yang boleh update.
     * route name: progresses.updates.store
     */
    public function updatesStore(Request $request, Progress $progress)
    {
        $this->authorize('createUpdate', $progress);

        $data = $request->validate([
            'update_date' => ['required','date'],
            'percent'     => ['required','integer','min:0','max:100'],
        ]);

        // Cegah update lewat timeline selesai
        if (
            $progress->end_date &&
            now()->startOfDay()->gt(
                \Illuminate\Support\Carbon::parse($progress->end_date)->startOfDay()
            )
        ) {
            return back()->withErrors([
                'update' => 'Tidak bisa update: sudah lewat timeline selesai.',
            ]);
        }

        // ✅ BOLEH update BERKALI-KALI di tanggal yang sama (di-overwrite)
        $progress->updates()->updateOrCreate(
            [
                'update_date' => $data['update_date'],   // per hari
            ],
            [
                'percent'    => $data['percent'],
                'created_by' => Auth::id(),
            ]
        );

        // === NOTIFIKASI ke IT: progress di-update ===
        $project = $progress->project()->with(['developer','digitalBanking'])->first();
        $actor   = $request->user();

        $payload = [
            'type'          => 'progress_updated',
            'project_id'    => $project->id,
            'project_name'  => $project->name,
            'progress_id'   => $progress->id,
            'progress_name' => $progress->name,
            'percent'       => (int)$data['percent'],
            'actor_id'      => $actor->id,
            'actor_name'    => $actor->name ?? $actor->username ?? 'User',
            'meta'          => ['update_date' => $data['update_date']],
        ];

        foreach (NotifyTargets::itFor($project) as $target) {
            $target->notify(new ProgressChangedNotification($payload));
        }

        // ✅ tetap di halaman progress (dig.detail / dig.progresses / kd.progresses)
        return back()->with('success', 'Update progress disimpan.');
    }

    /**
     * POST /progresses/{progress}/notes
     * Simpan catatan.
     * route name: progresses.notes.store (kalau ada)
     */
    public function notesStore(Request $request, Progress $progress)
    {
        $data = $request->validate([
            'body' => ['required','string','max:2000'],
        ]);

        $progress->notes()->create([
            'content' => $data['body'],
            'user_id' => Auth::id(),
            'role'    => Auth::user()->role ?? null,
        ]);

        return back()->with('success', 'Catatan ditambahkan.');
    }

    /**
     * POST /progresses/{progress}/confirm
     * Konfirmasi progress jika realisasi >= target. Hanya pemilik yang boleh.
     * route name: progresses.confirm
     */
    public function confirm(Progress $progress)
    {
        $this->authorize('createUpdate', $progress);

        if ($progress->confirmed_at) {
            return back()->with('success', 'Progress sudah dikonfirmasi sebelumnya.');
        }

        $latestUpdate = $progress->updates()->orderByDesc('update_date')->first();
        $latest = (int) (
            optional($latestUpdate)->percent
            ?? optional($latestUpdate)->progress_percent
            ?? 0
        );

        if ($latest < (int) $progress->desired_percent) {
            return back()->withErrors([
                'confirm' => 'Konfirmasi gagal: realisasi belum mencapai target.',
            ]);
        }

        $progress->forceFill(['confirmed_at' => now()])->save();

        // ===== Notifikasi ke DIG jika IT yang konfirmasi
        $confirmer = Auth::user();
        $project   = $progress->project()->with(['digitalBanking','developer'])->first();

        if (
            optional($project->developer)->role === 'it' &&
            optional($confirmer)->role === 'it' &&
            $project->digitalBanking
        ) {
            $project->digitalBanking->notify(new ProgressConfirmed($progress, $confirmer));
        }

        // ===== NEW: Notifikasi ke IT juga (feed IT melihat konfirmasi progress)
        $payloadIT = [
            'type'          => 'progress_confirmed',
            'project_id'    => $project->id,
            'project_name'  => $project->name,
            'progress_id'   => $progress->id,
            'progress_name' => $progress->name,
            'percent'       => $latest,
            'actor_id'      => $confirmer->id,
            'actor_name'    => $confirmer->name ?? $confirmer->username ?? 'User',
        ];
        foreach (NotifyTargets::itFor($project) as $target) {
            $target->notify(new ProgressChangedNotification($payloadIT));
        }

        // ===== Re-load & logic supervisor (tetap)
        $project = $progress->project()->with([
            'progresses' => function ($q) {
                $q->with([
                    'creator',
                    'updates' => fn($u) => $u->orderByDesc('update_date'),
                ]);
            },
            'digitalBanking',
            'developer',
            'attachments',
        ])->first();

        $allDone = $project->progresses->every(function ($pr) {
            $last = $pr->updates->first();
            $real = (int) (optional($last)->percent ?? optional($last)->progress_percent ?? 0);
            return $pr->confirmed_at && $real >= (int) $pr->desired_percent;
        });

        if ($allDone && is_null($project->finished_at ?? null)) {
            $project->forceFill(['finished_at' => now()])->save();
        }

        $itGroup  = $project->progresses->filter(fn($p) => ($p->creator?->role ?? null) === 'it');
        $digGroup = $project->progresses->filter(fn($p) => ($p->creator?->role ?? null) === 'digital_banking');

        $itDone  = $itGroup->isNotEmpty()  && $itGroup->every(fn($p) => (bool) $p->confirmed_at);
        $digDone = $digGroup->isNotEmpty() && $digGroup->every(fn($p) => (bool) $p->confirmed_at);

        $payloadCommon = [
            'project_id'   => $project->id,
            'project_name' => $project->name,
            'when'         => now()->toISOString(),
        ];

        $supervisors = User::where('role','kepala_divisi')->get();
        $notifySup = function (array $payload) use ($supervisors) {
            foreach ($supervisors as $sup) {
                $sup->notify(new SupervisorStatusNotification($payload));
            }
        };

        if ($itDone && $digDone) {
            $notifySup(array_merge($payloadCommon, ['status' => 'project_done']));
        } elseif ($itDone) {
            $notifySup(array_merge($payloadCommon, ['status' => 'it_done']));
        } elseif ($digDone) {
            $notifySup(array_merge($payloadCommon, ['status' => 'dig_done']));
        }

        return back()->with('success', $allDone ? 'Project selesai dikonfirmasi.' : 'Progress selesai dikonfirmasi.');
    }
}
