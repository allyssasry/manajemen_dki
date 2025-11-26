<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ItNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }
        if ($user->role !== 'it') {
            abort(403, 'Khusus pengguna IT.');
        }

        // Jenis notifikasi IT yang ditampilkan
        $types = [
            'dig_project_created',      // ketika DIG bikin project baru (kamu sudah pakai)
            'dig_completion_decision', // ketika DIG memutuskan Memenuhi / Tidak Memenuhi
        ];

        $tz = 'Asia/Jakarta';

        // === RANGE "HARI INI" (untuk section Hari Ini) ===
        $todayStartJak = Carbon::now($tz)->startOfDay();
        $todayEndJak   = Carbon::now($tz)->endOfDay();
        $todayStartUtc = $todayStartJak->clone()->timezone('UTC');
        $todayEndUtc   = $todayEndJak->clone()->timezone('UTC');

        // === RANGE 7 HARI TERAKHIR (untuk riwayat / grouped by tanggal) ===
        // contoh: hari ini + 6 hari ke belakang = 7 hari
        $rangeEndJak   = $todayEndJak;
        $rangeStartJak = $todayEndJak->clone()->subDays(6)->startOfDay();
        $rangeStartUtc = $rangeStartJak->clone()->timezone('UTC');
        $rangeEndUtc   = $rangeEndJak->clone()->timezone('UTC');

        // --- HARI INI ---
        $today = $user->notifications()
            ->whereIn('data->type', $types)
            ->whereBetween('created_at', [$todayStartUtc, $todayEndUtc])
            ->latest()
            ->get();

        // --- SEMUA (UNTUK RIWAYAT, MAKS 7 HARI TERAKHIR) ---
        $notifications = $user->notifications()
            ->whereIn('data->type', $types)
            ->whereBetween('created_at', [$rangeStartUtc, $rangeEndUtc])
            ->latest()
            ->paginate(50);

        // --- BADGE UNREAD IT (TANPA BATAS 7 HARI, BIAR JUMLAHNYA REAL) ---
        $unreadCount = $user->unreadNotifications()
            ->whereIn('data->type', $types)
            ->count();

        return view('it.notifications', compact('today', 'notifications', 'unreadCount'));
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }
        if ($user->role !== 'it') {
            abort(403);
        }

        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi IT telah ditandai terbaca.');
    }

    public function markRead(Request $request, string $id)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }
        if ($user->role !== 'it') {
            abort(403);
        }

        $n = $user->notifications()->where('id', $id)->firstOrFail();
        $n->markAsRead();

        return back();
    }
}
