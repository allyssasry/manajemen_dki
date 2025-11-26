<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        // filter dari form
        $q     = (string) $request->query('q', '');
        $from  = $request->query('from'); // YYYY-MM-DD
        $to    = $request->query('to');   // YYYY-MM-DD
        $sort  = $request->query('sort', 'finished_desc');

        // === BASE QUERY: gunakan scope ArchivedStrict (konsisten dgn KPI) ===
        $projects = Project::archivedStrict()
            ->with([
                'progresses.updates',
                'digitalBanking:id,name',
                'developer:id,name',
            ]);

        // === FILTER keyword ===
        if ($q !== '') {
            $projects->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // === FILTER rentang tanggal selesai (completed_at) ===
        if ($from) $projects->whereDate('completed_at', '>=', $from);
        if ($to)   $projects->whereDate('completed_at', '<=', $to);

        // === SORTING ===
        switch ($sort) {
            case 'finished_asc':
                $projects->orderBy('completed_at', 'asc'); break;
            case 'name_asc':
                $projects->orderBy('name', 'asc'); break;
            case 'name_desc':
                $projects->orderBy('name', 'desc'); break;
            case 'finished_desc':
            default:
                $projects->orderBy('completed_at', 'desc'); break;
        }

        // === PAGINATION ===
        $projects = $projects->paginate(12)->withQueryString();

        // NB: ganti view sesuai yang kamu gunakan (arsip.arsip atau semua.arsip)
        return view('semua.arsip', compact('projects'));
    }
}
