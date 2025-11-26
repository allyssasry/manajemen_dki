<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SupervisorNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) abort(401);
        if (!in_array($user->role, ['kepala_divisi', 'supervisor'])) {
            abort(403, 'Khusus Kepala Divisi / Supervisor.');
        }

        $tz = 'Asia/Jakarta';

        // === RANGE HARI INI (UTC versi Jakarta) ===
        $todayStartJak = Carbon::now($tz)->startOfDay();
        $todayEndJak   = Carbon::now($tz)->endOfDay();
        $todayStartUtc = $todayStartJak->clone()->timezone('UTC');
        $todayEndUtc   = $todayEndJak->clone()->timezone('UTC');

        // === TYPES YANG DIBUTUHKAN KEPALA DIVISI ===
        $typesForKD = [
            'dig_project_created',      // DIG membuat project
            'it_project_created',       // IT membuat project
            'dig_completion_decision', // DIG konfirmasi selesai / tidak
            // kalau masih pakai type lama 'kepala_divisi_status' silakan tambahkan:
            // 'kepala_divisi_status',
        ];

        // === BASE QUERY: notifikasi milik user ini,
        //      hanya type yang relevan, optional target_role
        $base = $user->notifications()
            ->whereIn('data->type', $typesForKD);

        // kalau kamu ingin Kepala Divisi cuma baca yang benar-benar ditandai untuknya:
        // ->where(function ($q) {
        //     $q->whereNull('data->target_role')
        //       ->orWhere('data->target_role', 'kepala_divisi')
        //       ->orWhere('data->target_role', 'supervisor');
        // });

        // === HARI INI ===
        $today = (clone $base)
            ->whereBetween('created_at', [$todayStartUtc, $todayEndUtc])
            ->latest()
            ->get();

        // === SEMUA / RIWAYAT (tanpa batas hari, atau bisa kamu batasi 30 hari) ===
        $notifications = (clone $base)
            ->latest()
            ->paginate(50);

        $unreadCount = $user->unreadNotifications()
            ->whereIn('data->type', $typesForKD)
            ->count();

        return view('kd.notifications', [
            'today'         => $today,
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }
}
