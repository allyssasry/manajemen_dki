<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\DatabaseNotification;

class SupervisorNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * HALAMAN LIST NOTIF KEPALA DIVISI / SUPERVISOR
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if (!in_array($user->role, ['kepala_divisi', 'supervisor'])) {
            abort(403, 'Khusus Kepala Divisi / Supervisor.');
        }

        $tz = 'Asia/Jakarta';

        // === RANGE HARI INI (UTC versi Jakarta) ===
        $todayStartJak = Carbon::now($tz)->startOfDay();
        $todayEndJak   = Carbon::now($tz)->endOfDay();
        $todayStartUtc = $todayStartJak->clone()->timezone('UTC');
        $todayEndUtc   = $todayEndJak->clone()->timezone('UTC');

        // === TIPE NOTIF UNTUK KD / SUPERVISOR ===
        $typesForKD = [
            'dig_project_created',       // DIG membuat project
            'it_project_created',        // IT membuat project
            'dig_completion_decision',   // DIG konfirmasi selesai / tidak
            // 'kepala_divisi_status',    // kalau masih ada type lama, boleh di-uncomment
        ];

        // BASE QUERY: notifikasi milik user ini, hanya type yang relevan
        $base = $user->notifications()
            ->whereIn('data->type', $typesForKD);

        // === HARI INI ===
        $today = (clone $base)
            ->whereBetween('created_at', [$todayStartUtc, $todayEndUtc])
            ->latest()
            ->get();

        // === RIWAYAT (misal 50 terakhir) ===
        $notifications = (clone $base)
            ->latest()
            ->paginate(50);

        // === JUMLAH BELUM TERBACA (untuk badge) ===
        $unreadCount = $user->unreadNotifications()
            ->whereIn('data->type', $typesForKD)
            ->count();

        return view('kd.notifications', [
            'today'         => $today,
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }

    /**
     * TANDAI SEMUA NOTIF KD SEBAGAI TERBACA
     * POST /kd/notifications/read-all
     */
    public function readAll(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if (!in_array($user->role, ['kepala_divisi', 'supervisor'])) {
            abort(403, 'Khusus Kepala Divisi / Supervisor.');
        }

        $typesForKD = [
            'dig_project_created',
            'it_project_created',
            'dig_completion_decision',
            // 'kepala_divisi_status',
        ];

        $user->unreadNotifications()
            ->whereIn('data->type', $typesForKD)
            ->update(['read_at' => now()]);

        return back();
    }

    /**
     * TANDAI SATU NOTIF SEBAGAI TERBACA
     * POST /kd/notifications/{notification}/read
     */
    public function read(Request $request, DatabaseNotification $notification)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if (!in_array($user->role, ['kepala_divisi', 'supervisor'])) {
            abort(403, 'Khusus Kepala Divisi / Supervisor.');
        }

        // pastikan notif ini memang milik user yang login
        if ((int)$notification->notifiable_id !== (int)$user->id) {
            abort(403);
        }

        if (is_null($notification->read_at)) {
            $notification->forceFill(['read_at' => now()])->save();
        }

        return back();
    }
}
