{{-- Modal Konfirmasi Logout --}}
<div id="confirmLogoutModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40">
    <div class="mx-4 w-full max-w-sm rounded-2xl bg-white shadow-xl border border-red-100 overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-3 bg-[#8D2121] text-white">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 3h10a1 1 0 0 1 1 1v5h-2V5H5v14h7v-4h2v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/>
                    <path d="M14 12l5-5v3h4v4h-4v3l-5-5z"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="text-sm font-semibold">Konfirmasi Logout</div>
                <div class="text-xs text-white/80">Anda akan keluar dari akun ini.</div>
            </div>
            <button id="cancelLogout" type="button" aria-label="Tutup"
                class="text-white/70 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-4 py-4 text-sm text-gray-700">
            Yakin ingin logout dari akun ini?
        </div>
        <div class="flex justify-end gap-2 px-4 py-3 bg-[#FFF7F7]">
            <button type="button" id="cancelLogoutBtn"
                class="inline-flex items-center justify-center rounded-xl border border-red-200 px-4 py-1.5 text-xs font-semibold text-[#7A1C1C] bg-white hover:bg-red-50">
                Batal
            </button>
            <a id="confirmLogoutBtn" href="/logout"
                class="inline-flex items-center justify-center rounded-xl border border-[#7A1C1C] px-4 py-1.5 text-xs font-semibold text-white bg-[#8D2121] hover:bg-[#741B1B]">
                Ya, Logout
            </a>
        </div>
    </div>
</div>
