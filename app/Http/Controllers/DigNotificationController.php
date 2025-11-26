<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Project;
use App\Models\User;
use App\Notifications\DigMarkedReadNotification;

class DigNotificationController extends Controller
{
    /**
     * ===================== DIG (Digital Banking) =====================
     * Halaman notifikasi untuk user role DIG.
     *
     * Jenis notifikasi yang ditampilkan ke DIG:
     * - it_project_created    → IT membuat project untuk DIG
     * - progress_confirmed    → IT mengonfirmasi progress (on time / terlambat)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) abort(401);
        if ($user->role !== 'digital_banking') {
            abort(403, 'Khusus pengguna DIG.');
        }

        // Hitung rentang hari ini di Jakarta + versi UTC
        [$todayStartUtc, $todayEndUtc, $todayStartJak, $todayEndJak] = $this->todayRangeUtcWithLocal();

        // Range 7 hari terakhir (termasuk hari ini)
        $rangeEndJak   = $todayEndJak->copy();
        $rangeStartJak = $todayEndJak->copy()->subDays(6)->startOfDay();
        $rangeStartUtc = $rangeStartJak->copy()->timezone('UTC');
        $rangeEndUtc   = $rangeEndJak->copy()->timezone('UTC');

        // Jenis notif yang mau ditampilkan ke DIG
        $typesForDig = ['it_project_created', 'progress_confirmed'];

        // Notifikasi "Hari Ini"
        $todayAll = $user->notifications()
            ->whereIn('data->type', $typesForDig)
            ->whereBetween('created_at', [$todayStartUtc, $todayEndUtc])
            ->latest()
            ->get();

        // Riwayat (max 7 hari terakhir, pakai paginator)
        $allPaginated = $user->notifications()
            ->whereIn('data->type', $typesForDig)
            ->whereBetween('created_at', [$rangeStartUtc, $rangeEndUtc])
            ->latest()
            ->paginate(50);

        // Badge unread (khusus jenis notif di atas)
        $unreadCount = $user->unreadNotifications()
            ->whereIn('data->type', $typesForDig)
            ->count();

        return view('dig.notifications', [
            'unreadCount'   => $unreadCount,
            'today'         => $todayAll,
            'notifications' => $allPaginated,
        ]);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        if (!$user) abort(401);
        if ($user->role !== 'digital_banking') {
            abort(403, 'Khusus pengguna DIG.');
        }

        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function markRead(Request $request, string $id)
    {
        $user = $request->user();
        if (!$user) abort(401);
        if ($user->role !== 'digital_banking') {
            abort(403, 'Khusus pengguna DIG.');
        }

        // Pastikan notifikasi milik user yang login
        $n = $user->notifications()->where('id', $id)->firstOrFail();
        $n->markAsRead();

        /**
         * === Opsional: Kirim notifikasi ke IT saat DIG menandai baca ===
         * Ambil info developer/IT dari payload notifikasi (jika ada),
         * jika tidak ada, fallback lewat Project.
         */
        $data        = $n->data ?? [];
        $projectId   = data_get($data, 'project_id');
        $projectName = data_get($data, 'project_name');
        $developerId = data_get($data, 'developer_id');

        // Fallback cari developer_id dari Project
        if (!$developerId && $projectId) {
            $developerId = Project::whereKey($projectId)->value('developer_id');
        }

        // Fallback cari nama project
        if ($projectId && !$projectName) {
            $projectName = optional(Project::find($projectId))->name;
        }

        // Kirim notif ke user IT yang bersangkutan (jika class notifikasi tersedia)
        if ($developerId) {
            $it = User::find($developerId);
            if (
                $it &&
                $it->id !== $user->id &&
                $it->role === 'it' &&
                class_exists(DigMarkedReadNotification::class)
            ) {
                $it->notify(new DigMarkedReadNotification([
                    'project_id'  => $projectId,
                    'project_name'=> $projectName,
                    'by_user_id'  => $user->id,
                    'by_name'     => $user->name,
                    'by_role'     => 'digital_banking',
                    'message'     => 'Digital Banking telah menandai notifikasi sebagai terbaca.',
                    'source_notification_id' => $n->id,
                ]));
            }
        }

        return back()->with('success', 'Notifikasi ditandai terbaca.');
    }

    /**
     * ===================== IT (Developer) =====================
     * Halaman notifikasi untuk user role IT.
     *
     * Syarat yang masuk ke IT:
     * - data->type in ["dig_project_created", "dig_marked_read", "dig_completion_decision"]
     *   (target_role kita abaikan karena notifikasi sudah scoped ke user IT ini)
     */
    public function itIndex(Request $request)
    {
        $user = $request->user();
        if (!$user) abort(401);
        if ($user->role !== 'it') {
            abort(403, 'Khusus pengguna IT.');
        }

        [$todayStartUtc, $todayEndUtc, $todayStartJak, $todayEndJak] = $this->todayRangeUtcWithLocal();

        // Range 7 hari terakhir untuk IT
        $rangeEndJak   = $todayEndJak->copy();
        $rangeStartJak = $todayEndJak->copy()->subDays(6)->startOfDay();
        $rangeStartUtc = $rangeStartJak->copy()->timezone('UTC');
        $rangeEndUtc   = $rangeEndJak->copy()->timezone('UTC');

        $typesForIt = ['dig_project_created', 'dig_marked_read', 'dig_completion_decision'];

        $base = $user->notifications()
            ->whereIn('data->type', $typesForIt);

        // Hari ini (IT)
        $today = (clone $base)
            ->whereBetween('created_at', [$todayStartUtc, $todayEndUtc])
            ->latest()
            ->get();

        // Semua (IT) 7 hari terakhir
        $allPaginated = (clone $base)
            ->whereBetween('created_at', [$rangeStartUtc, $rangeEndUtc])
            ->latest()
            ->paginate(50);

        $unreadCount = $user->unreadNotifications()
            ->whereIn('data->type', $typesForIt)
            ->count();

        return view('it.notifications', [
            'unreadCount'          => $unreadCount,
            'today'                => $today,
            'notifications'        => $allPaginated,
            'today_start_jakarta'  => $todayStartJak,
            'today_end_jakarta'    => $todayEndJak,
        ]);
    }

    public function itMarkAllRead(Request $request)
    {
        $user = $request->user();
        if (!$user) abort(401);
        if ($user->role !== 'it') {
            abort(403, 'Khusus pengguna IT.');
        }

        $typesForIt = ['dig_project_created', 'dig_marked_read', 'dig_completion_decision'];

        $user->unreadNotifications()
            ->whereIn('data->type', $typesForIt)
            ->get()
            ->markAsRead();

        return back()->with('success', 'Semua notifikasi IT ditandai sudah dibaca.');
    }

    public function itMarkRead(Request $request, string $id)
    {
        $user = $request->user();
        if (!$user) abort(401);
        if ($user->role !== 'it') {
            abort(403, 'Khusus pengguna IT.');
        }

        $typesForIt = ['dig_project_created', 'dig_marked_read', 'dig_completion_decision'];

        $n = $user->notifications()
            ->where('id', $id)
            ->whereIn('data->type', $typesForIt)
            ->firstOrFail();

        $n->markAsRead();

        return back();
    }

    /**
     * Helper: hitung rentang "hari ini" di Asia/Jakarta, kembalikan juga versi UTC.
     *
     * @return array{0:\DateTimeInterface,1:\DateTimeInterface,2:Carbon,3:Carbon}
     */
    private function todayRangeUtcWithLocal(): array
    {
        $tzJakarta = 'Asia/Jakarta';
        $startJak  = Carbon::now($tzJakarta)->startOfDay();
        $endJak    = Carbon::now($tzJakarta)->endOfDay();

        $startUtc  = $startJak->clone()->timezone('UTC');
        $endUtc    = $endJak->clone()->timezone('UTC');

        return [$startUtc, $endUtc, $startJak, $endJak];
    }
}
