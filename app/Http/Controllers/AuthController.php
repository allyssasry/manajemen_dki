<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function registerForm() { return view('Auth.register'); }
    public function loginForm() { return view('Auth.login'); }

    public function register(Request $request) {
        $request->validate([
            'name'=>'required|string|max:255',
            'username'=>'required|unique:users|max:100',
            'email'=>'required|email|unique:users|max:255',
            'password'=>'required|min:8|confirmed',
            'role'=>'required|in:digital_banking,it,kepala_divisi',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
            'role.required' => 'Peran/Role wajib dipilih.',
        ]);

        User::create([
            'name'=>$request->name,
            'username'=>$request->username,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>$request->role,
        ]);

        return redirect('/login')->with('success','Register berhasil! Silakan login dengan akun Anda.');
    }

    public function login(Request $request) {
        $request->validate([
            'username' => 'required',
            'password' => 'required|min:8',
        ], [
            'username.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        // Cek apakah input adalah email atau username
        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->username,
            'password' => $request->password
        ];

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            if($user->role == 'digital_banking') return redirect('/dig/dashboard');
            if($user->role == 'it') return redirect('/it/dashboard');
            if($user->role == 'kepala_divisi') return redirect('/kd/dashboard');
        }
        return back()
            ->withInput($request->only('username'))
            ->with('error','Login gagal! Username/Email atau password tidak sesuai.');
    }

    public function logout() {
        Auth::logout();
        return redirect('/login');
    }
}
