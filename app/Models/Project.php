<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'title',
        'digital_banking_id',
        'developer_id',
        'completed_at',
        'meets_requirement',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'meets_requirement' => 'boolean',
    ];

    protected $appends = [
        'status_text',
        'status_color',
        'can_decide_completion',
        'ready_because_overdue',
    ];

    /* ================== RELATIONSHIPS ================== */

    public function progresses(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    // Jika ingin akses langsung ke semua update project (opsional)
    public function updates(): HasMany
    {
        return $this->hasMany(ProgressUpdate::class);
    }

    public function digitalBanking(): BelongsTo
    {
        return $this->belongsTo(User::class, 'digital_banking_id');
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ================== COMPUTED / HELPERS ================== */

    public function getDesiredAveragePercentAttribute(): int
    {
        if ($this->relationLoaded('progresses')) {
            $avg = (float) ($this->progresses->avg('desired_percent') ?? 0);
            return (int) round($avg);
        }
        return (int) round($this->progresses()->avg('desired_percent') ?? 0);
    }

    public function getIsFinishedAttribute(): bool
    {
        // Sudah difinalisasi? langsung true
        if (!is_null($this->meets_requirement) || !is_null($this->completed_at)) {
            return true;
        }

        // Cek kelengkapan progress (semua confirmed & >= target)
        $this->loadMissing(['progresses.updates' => fn($q) => $q->orderByDesc('update_date')]);
        if ($this->progresses->isEmpty())
            return false;

        foreach ($this->progresses as $pr) {
            $latest = $pr->updates->first();
            $real = (int) ($latest->progress_percent ?? $latest->percent ?? 0);
            if (is_null($pr->confirmed_at) || $real < (int) $pr->desired_percent) {
                return false;
            }
        }
        return true;
    }

    public function getFinishedAtCalcAttribute(): ?Carbon
    {
        $this->loadMissing('progresses');
        $dates = $this->progresses->pluck('confirmed_at')->filter();
        return $dates->isNotEmpty() ? Carbon::parse($dates->max()) : null;
    }

    /** ====== STATUS UNTUK UI ====== */
    public function getStatusTextAttribute(): string
    {
        if (!$this->completed_at || is_null($this->meets_requirement)) {
            return $this->is_finished ? 'Project Selesai' : 'Dalam Proses';
        }

        return $this->meets_requirement
            ? 'Project Selesai, Memenuhi'
            : 'Project Selesai, Tidak Memenuhi';
    }

    public function getStatusColorAttribute(): string
    {
        if (!$this->completed_at || is_null($this->meets_requirement)) {
            return '#7A1C1C';
        }
        return $this->meets_requirement ? '#166534' : '#7A1C1C';
    }

    /**
     * Siap difinalisasi karena ada progress yang lewat timeline,
     * belum dikonfirmasi, dan realisasinya < target — sementara
     * progress lain sudah terkonfirmasi.
     */
    public function readyBecauseOverdue(): bool
    {
        $this->loadMissing(['progresses.updates' => fn($q) => $q->orderByDesc('update_date')]);

        $hasOverdueUnmet = false;

        foreach ($this->progresses as $pr) {
            $latest = $pr->updates->first();
            $real = $latest ? (int) ($latest->progress_percent ?? $latest->percent ?? 0) : 0;

            $end = $pr->end_date ? Carbon::parse($pr->end_date)->startOfDay() : null;
            $od = $end ? $end->lt(now()->startOfDay()) : false;
            $unmet = $od && is_null($pr->confirmed_at) && ($real < (int) $pr->desired_percent);

            if ($unmet) {
                $hasOverdueUnmet = true;
                break;
            }
        }

        if (!$hasOverdueUnmet)
            return false;

        $othersAllConfirmed = $this->progresses->every(function ($pr) {
            $latest = $pr->updates->first();
            $real = $latest ? (int) ($latest->progress_percent ?? $latest->percent ?? 0) : 0;

            $end = $pr->end_date ? Carbon::parse($pr->end_date)->startOfDay() : null;
            $od = $end ? $end->lt(now()->startOfDay()) : false;
            $unmet = $od && is_null($pr->confirmed_at) && ($real < (int) $pr->desired_percent);

            return $unmet ? true : !is_null($pr->confirmed_at);
        });

        return $othersAllConfirmed && is_null($this->meets_requirement);
    }

    public function getReadyBecauseOverdueAttribute(): bool
    {
        return $this->readyBecauseOverdue();
    }

    public function getCanDecideCompletionAttribute(): bool
    {
        $this->loadMissing(['progresses.updates' => fn($q) => $q->orderByDesc('update_date')]);
        if ($this->progresses->isEmpty())
            return false;

        $allMetAndConfirmed = $this->progresses->every(function ($pr) {
            $latest = $pr->updates->first();
            $real = (int) ($latest->progress_percent ?? $latest->percent ?? 0);
            return $real >= (int) $pr->desired_percent && !is_null($pr->confirmed_at);
        });

        $readyBecauseOverdue = $this->readyBecauseOverdue();

        return ($allMetAndConfirmed || $readyBecauseOverdue) && is_null($this->meets_requirement);
    }

    /* ================== SCOPES ================== */

    public function scopeCompleted($q)
    {
        return $q->whereNotNull('completed_at');
    }

    public function scopeNotCompleted($q)
    {
        return $q->whereNull('completed_at');
    }

    public function scopeMeets($q)
    {
        return $q->where('meets_requirement', true);
    }

    public function scopeNotMeets($q)
    {
        return $q->where('meets_requirement', false);
    }

    /**
     * FINALIZED (longgar): completed_at IS NOT NULL OR meets_requirement IS NOT NULL.
     */
    public function scopeFinalized($q)
    {
        return $q->where(function ($x) {
            $x->whereNotNull('completed_at')
                ->orWhereNotNull('meets_requirement');
        });
    }

    /**
     * ARCHIVED (ketat/STRICT): mengikuti halaman Arsip-mu sekarang,
     * yaitu HANYA project yang memiliki completed_at **dan** meets_requirement.
     * -> inilah yang akan dipakai Arsip & Dashboard KPI agar konsisten.
     */
    public function scopeArchivedStrict($q)
    {
        return $q->whereNotNull('completed_at')
            ->whereNotNull('meets_requirement');
    }

    /** akses cepat sebagai properti */
    public function getIsFinalizedAttribute(): bool
    {
        return !is_null($this->completed_at) || !is_null($this->meets_requirement);
    }

    public function attachments()
    {
        return $this->hasMany(\App\Models\ProjectAttachment::class, 'project_id');
    }

    /**
     * Batasi project yang terlihat sesuai role penanggung jawab.
     * - DIG: hanya project dengan digital_banking_id = user login
     * - IT:  hanya project dengan developer_id = user login
     * - Kepala divisi/supervisor: tidak dibatasi
     */
    public function scopeVisibleTo($query, ?User $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if (in_array($user->role, ['kepala_divisi', 'kd', 'supervisor'], true)) {
            return $query;
        }

        if ($user->role === 'digital_banking') {
            return $query->where('digital_banking_id', $user->id);
        }

        if ($user->role === 'it') {
            return $query->where('developer_id', $user->id);
        }

        return $query->whereRaw('1 = 0');
    }


}
