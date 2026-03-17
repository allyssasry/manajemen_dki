@extends('layouts.dashboard')

@section('title', 'Pengaturan Password')
@section('pageTitle', 'Pengaturan Password')

@section('headStyles')
    <style>
        body {
            background: #F8ECEC;
        }
    </style>
@endsection

@section('content')
    <main class="max-w-6xl mx-auto px-5 py-6">
        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-red-300 bg-red-50 p-4">
                <div class="flex gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none text-red-600 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4"/>
                    </svg>
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-red-900 mb-2">Terjadi kesalahan:</div>
                        <ul class="list-disc list-inside text-xs text-red-800 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-green-300 bg-green-50 p-4">
                <div class="flex gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none text-green-600 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-green-900">{{ session('success') }}</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="rounded-2xl border border-[#C89898] bg-[#FFF5F5] p-5">
            <div class="mb-4">
                <h2 class="text-base font-semibold">Ganti Kata Sandi</h2>
                <p class="text-xs text-gray-600 mt-1">Ubah kata sandi akun Anda untuk keamanan yang lebih baik.</p>
            </div>

            <form id="changePasswordForm" action="{{ route('account.change-password') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs mb-1">Kata Sandi Saat Ini <span class="text-red-600">*</span>:</label>
                    <input type="password" name="current_password"
                        class="w-full rounded-xl bg-white border border-[#C89898] px-4 py-2.5 outline-none focus:ring-2 focus:ring-[#7A1C1C]/30"
                        placeholder="Masukkan kata sandi saat ini"
                        value="{{ old('current_password') }}">
                    @error('current_password')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs mb-1">Kata Sandi Baru <span class="text-red-600">*</span>:</label>
                    <input type="password" name="password"
                        class="w-full rounded-xl bg-white border border-[#C89898] px-4 py-2.5 outline-none focus:ring-2 focus:ring-[#7A1C1C]/30"
                        placeholder="Masukkan kata sandi baru (minimal 8 karakter)"
                        value="{{ old('password') }}">
                    @error('password')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-gray-600 mt-1">*Minimal 8 karakter, hindari yang terlalu mudah ditebak</p>
                </div>

                <div>
                    <label class="block text-xs mb-1">Konfirmasi Kata Sandi Baru <span class="text-red-600">*</span>:</label>
                    <input type="password" name="password_confirmation"
                        class="w-full rounded-xl bg-white border border-[#C89898] px-4 py-2.5 outline-none focus:ring-2 focus:ring-[#7A1C1C]/30"
                        placeholder="Ketik ulang kata sandi baru"
                        value="{{ old('password_confirmation') }}">
                    @error('password_confirmation')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit"
                        class="inline-flex justify-center min-w-[160px] h-10 items-center rounded-full border-2 border-[#7A1C1C] bg-[#E2B9B9] hover:bg-[#D9AFAF] text-black text-sm font-semibold">
                        Ubah Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection
