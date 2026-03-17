<?php

namespace App\Support;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ProgressTargetAllocator
{
    /**
     * Bagi target progress secara merata dengan pembagi tetap 23.
     * Contoh: 8 item = 5%, 15 item = 4% (total 100 jika 23 progress).
     * Divisor selalu 23, tidak bergantung jumlah progress aktual.
     */
    public static function rebalance(Project $project): void
    {
        $ids = $project->progresses()->orderBy('id')->pluck('id')->all();
        $count = count($ids);

        if ($count === 0) {
            return;
        }

        $base = intdiv(100, 23);
        $remainder = 100 - ($base * 23);

        foreach ($ids as $index => $id) {
            $target = $base + ($index < $remainder ? 1 : 0);

            DB::table('progresses')
                ->where('id', $id)
                ->update(['desired_percent' => $target]);
        }
    }
}
