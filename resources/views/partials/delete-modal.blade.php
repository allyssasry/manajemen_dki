{{-- Modal Konfirmasi Hapus (Project / Progress) --}}
<div id="confirmDeleteModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40">
    <div class="mx-4 w-full max-w-sm rounded-2xl bg-white shadow-xl border border-red-100 overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-3 bg-[#7A1C1C] text-white">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11 15h2v2h-2zm0-8h2v6h-2z" />
                    <path d="M12 2C6.49 2 2 6.49 2 12s4.49 10 10 10
                             10-4.49 10-10S17.51 2 12 2zm0 18
                             c-4.41 0-8-3.59-8-8s3.59-8 8-8
                             8 3.59 8 8-3.59 8-8 8z" />
                </svg>
            </div>
            <div class="flex-1">
                <div class="text-sm font-semibold">Konfirmasi Hapus</div>
                <div class="text-xs text-white/80">Aksi ini tidak bisa dibatalkan.</div>
            </div>
        </div>
        <div class="px-4 py-4 text-sm text-gray-700" id="confirmDeleteMessage">
            Yakin ingin menghapus data ini?
        </div>
        <div class="flex justify-end gap-2 px-4 py-3 bg-[#FFF7F7]">
            <button type="button" id="cancelDeleteBtn"
                class="inline-flex items-center justify-center rounded-xl border border-red-200 px-4 py-1.5 text-xs font-semibold text-[#7A1C1C] bg-white hover:bg-red-50">
                Batal
            </button>
            <button type="button" id="confirmDeleteBtn"
                class="inline-flex items-center justify-center rounded-xl border border-[#7A1C1C] px-4 py-1.5 text-xs font-semibold text-white bg-[#8D2121] hover:bg-[#741B1B]">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>
