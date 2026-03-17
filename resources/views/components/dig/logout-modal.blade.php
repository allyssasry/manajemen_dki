@props(['id' => 'confirmLogoutModal'])

{{-- MODAL KONFIRMASI LOGOUT --}}
<div id="{{ $id }}" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40">
    <div class="w-full max-w-xs rounded-lg bg-white shadow-lg">
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Logout</h3>
            <p class="mt-2 text-sm text-gray-600">Apakah Anda yakin ingin keluar dari akun ini?</p>
        </div>
        <div class="flex gap-3 border-t bg-gray-50 px-4 py-3">
            <button type="button" onclick="document.getElementById('{{ $id }}').classList.add('hidden')"
                class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                Batal
            </button>
            <a href="/logout"
                class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-red-700">
                Logout
            </a>
        </div>
    </div>
</div>
