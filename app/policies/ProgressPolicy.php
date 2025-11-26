<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Progress;

class ProgressPolicy
{
    /**
     * Lihat daftar progress di dalam project (kamu pakai via relasi project).
     */
    public function view(User $user, Progress $progress): bool
    {
        // Boleh lihat kalau user terlibat di project atau supervisor
        $project = $progress->project;

        if ($user->role === 'supervisor') {
            return true;
        }

        if ($user->role === 'it') {
            return (int) $project->developer_id === (int) $user->id;
        }

        if ($user->role === 'digital_banking') {
            return (int) $project->digital_banking_id === (int) $user->id;
        }

        return false;
    }

    /**
     * Bikin progress baru.
     * Biasanya boleh untuk user yang terlibat di project.
     */
    public function create(User $user, \App\Models\Project $project): bool
    {
        if ($user->role === 'it') {
            return (int) $project->developer_id === (int) $user->id;
        }

        if ($user->role === 'digital_banking') {
            return (int) $project->digital_banking_id === (int) $user->id;
        }

        return false;
    }

    /**
     * EDIT PROGRESS
     *
     * HANYA boleh kalau user adalah pembuat progress tersebut (created_by).
     * Jadi progress yang dibuat DIG nggak bisa diedit IT, dan sebaliknya.
     */
    public function update(User $user, Progress $progress): bool
    {
        return (int) ($progress->created_by ?? 0) === (int) $user->id;
    }

    /**
     * Hapus progress – aturan sama dengan update.
     */
    public function delete(User $user, Progress $progress): bool
    {
        return (int) ($progress->created_by ?? 0) === (int) $user->id;
    }

    /**
     * Bikin UPDATE LOG progress (route progresses.updates.store)
     * – juga cuma boleh owner progress.
     */
    public function createUpdate(User $user, Progress $progress): bool
    {
        return (int) ($progress->created_by ?? 0) === (int) $user->id;
    }

    /**
     * Konfirmasi progress (progresses.confirm)
     * – juga cuma boleh owner progress.
     */
    public function confirm(User $user, Progress $progress): bool
    {
        return (int) ($progress->created_by ?? 0) === (int) $user->id;
    }
}
