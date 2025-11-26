<?php

namespace App\Support;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;

class NotifyTargets
{
    /**
     * Target utama IT untuk sebuah project:
     * - Prioritas ke developer (kolom developer_id)
     * - Optional: broadcast ke semua user role 'it' (includeAllIT = true)
     */
    public static function itFor(Project $project, bool $includeAllIT = false): Collection
    {
        $targets = collect();

        if ($project->developer) {
            $targets->push($project->developer);
        } elseif ($project->developer_id) {
            if ($u = User::find($project->developer_id)) {
                $targets->push($u);
            }
        }

        if ($includeAllIT) {
            $more = User::where('role', 'it')->get();
            $targets = $targets->concat($more);
        }

        return $targets->unique('id')->values();
    }
}
