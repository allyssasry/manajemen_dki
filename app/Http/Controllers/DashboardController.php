<?php
   // app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function digDashboard()
    {
        $projects = Project::query()
            ->visibleTo(Auth::user())
            ->with(['creator', 'progresses.updates'])
            ->orderByDesc('id')
            ->get();

        // dropdown penanggung jawab saat bikin project
        $digitalUsers = User::where('role','digital_banking')->orderBy('name')->get();
        $itUsers      = User::where('role','it')->orderBy('name')->get();

        return view('dig.dashboard', compact('projects','digitalUsers','itUsers'));
    }

    public function itDashboard()
    {
        $projects = Project::query()
            ->visibleTo(Auth::user())
            ->with(['digitalBanking', 'developer', 'progresses.updates'])
            ->orderByDesc('id')
            ->get();

        return view('it.dashboard', compact('projects'));
    }
}

