<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    /**
     * Siapa saja boleh lihat daftar project, kalau sudah login.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['it','digital_banking','supervisor']);
    }

    /**
     * Lihat project tertentu.
     */
    public function view(User $user, Project $project): bool
    {
        // Boleh lihat kalau dia terlibat di project ini atau supervisor
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
     * Bikin project baru.
     * (sesuaikan kalau kamu mau hanya role tertentu)
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['it','digital_banking']);
    }

    /**
     * EDIT PROJECT
     *
     * - Anak IT hanya boleh edit project yang developer_id = user IT tersebut
     * - Anak DIG hanya boleh edit project yang digital_banking_id = user DIG tersebut
     * - Supervisor (kalau mau) boleh edit semua
     */
      public function update(User $user, Project $project): bool
    {
        // Boleh edit kalau:
        // - role supervisor / kd
        // - ATAU role digital_banking
        // - ATAU role it
        // - ATAU dia pembuat project
        return in_array($user->role, ['supervisor', 'digital_banking', 'it'], true)
            || (int) $project->created_by === (int) $user->id;
    }

    /**
     * Hapus project (opsional, sama logika dengan update).
     */
    public function delete(User $user, Project $project): bool
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
     * FINALIZE (set meets_requirement) – misalnya hanya DIG atau supervisor.
     */
    public function finalize(User $user, Project $project): bool
    {
        if ($user->role === 'supervisor') {
            return true;
        }

        if ($user->role === 'digital_banking') {
            return (int) $project->digital_banking_id === (int) $user->id;
        }

        return false;
    }
}
