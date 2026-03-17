@extends('layouts.dashboard')

@section('title', 'Pengaturan')
@section('pageTitle', 'Pengaturan')

@section('headStyles')
    <style>
        body {
            background: #F8ECEC;
        }
    </style>
@endsection

@section('content')
    <main class="max-w-6xl mx-auto px-5 py-6">
        <div class="rounded-2xl border border-[#C89898] bg-[#FFF5F5] overflow-hidden">
            <div class="px-5 py-4 border-b border-[#E5CFCF]">
                <h2 class="text-xl font-semibold text-[#3A1B1B]">Pengaturan Umum</h2>
                <p class="text-sm text-gray-600 mt-1">Pilih menu pengaturan yang ingin Anda kelola.</p>
            </div>

            <div class="divide-y divide-[#EEDADA]">
                <a href="{{ route('account.setting.profile') }}"
                    class="flex items-center justify-between gap-3 px-5 py-4 hover:bg-[#FBECEC] transition">
                    <div class="flex items-center gap-3">
                        <span class="w-11 h-11 rounded-xl bg-[#F0E6E6] inline-flex items-center justify-center text-[#7A1C1C]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                            </svg>
                        </span>
                        <div>
                            <div class="text-base font-semibold text-[#0F172A]">Pengaturan Profil</div>
                            <div class="text-sm text-gray-600">Ubah informasi profil, foto, dan data pribadi.</div>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 1 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0Z"
                            clip-rule="evenodd" />
                    </svg>
                </a>

                <a href="{{ route('account.setting.password') }}"
                    class="flex items-center justify-between gap-3 px-5 py-4 hover:bg-[#FBECEC] transition">
                    <div class="flex items-center gap-3">
                        <span class="w-11 h-11 rounded-xl bg-[#F0E6E6] inline-flex items-center justify-center text-[#7A1C1C]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-1.5 0h12a1.5 1.5 0 0 1 1.5 1.5v7.5a1.5 1.5 0 0 1-1.5 1.5h-12A1.5 1.5 0 0 1 4.5 19.5V12a1.5 1.5 0 0 1 1.5-1.5Z" />
                            </svg>
                        </span>
                        <div>
                            <div class="text-base font-semibold text-[#0F172A]">Pengaturan Password</div>
                            <div class="text-sm text-gray-600">Ubah kata sandi akun Anda.</div>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 1 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0Z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>
    </main>
@endsection
