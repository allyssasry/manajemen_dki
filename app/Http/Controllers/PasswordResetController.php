<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    /**
     * Tampilkan form untuk request password reset
     */
    public function showRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Handle permintaan reset password (kirim email)
     */
    public function sendResetLink(Request $request)
    {
        // Trim dan lowercase email
        $email = trim(strtolower($request->email));

        // Cek manual apakah email ada di database
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email tidak ditemukan dalam sistem kami.']);
        }

        // Generate token reset password
        $token = Str::random(60);

        // Simpan token ke database (table password_resets)
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Buat link reset
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);

        // Untuk development: tampilkan link langsung (tanpa kirim email)
        // Pada production: uncomment Mail::send() dan comment session()
        
        return back()
            ->with('success', 'Link reset password telah dibuat! Silakan klik link di bawah:')
            ->with('reset_url', $resetUrl);
    }

    /**
     * Tampilkan form reset password
     */
    public function showResetForm($token = null)
    {
        if (!$token) {
            abort(400, 'Token tidak valid');
        }

        return view('auth.passwords.reset', ['token' => $token]);
    }

    /**
     * Handle reset password
     */
    public function reset(Request $request)
    {
        $email = trim(strtolower($request->email));

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
            'token.required' => 'Token tidak valid.',
        ]);

        // Cek token di database
        $reset = DB::table('password_resets')
                    ->where('email', $email)
                    ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah expired.']);
        }

        if (!Hash::check($request->token, $reset->token)) {
            return back()->withErrors(['email' => 'Token reset password tidak valid.']);
        }

        // Update password user
        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User tidak ditemukan.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Hapus token dari database
        DB::table('password_resets')->where('email', $email)->delete();

        return redirect('/login')->with('success', 'Kata sandi berhasil direset. Silakan login dengan kata sandi baru Anda.');
    }
}
