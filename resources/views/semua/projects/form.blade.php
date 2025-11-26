{{-- resources/views/semua/projects/form.blade.php --}}
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">

    @php
        use Illuminate\Support\Collection;

        // mode default: create
        $mode = $mode ?? 'create';
        $isEdit = $mode === 'edit';

        // user login
        $meUser = auth()->user();

        // safety: kalau controller belum ngirimin, ambil sendiri
        if (!isset($digitalUsers) || !($digitalUsers instanceof Collection)) {
            try {
                $digitalUsers = \App\Models\User::where('role', 'digital_banking')
                    ->orderByRaw('COALESCE(NULLIF(name, \'\'), username)')
                    ->get(['id', 'name', 'username']);
            } catch (\Throwable $e) {
                $digitalUsers = collect();
            }
        }

        if (!isset($itUsers) || !($itUsers instanceof Collection)) {
            try {
                $itUsers = \App\Models\User::where('role', 'it')
                    ->orderByRaw('COALESCE(NULLIF(name, \'\'), username)')
                    ->get(['id', 'name', 'username']);
            } catch (\Throwable $e) {
                $itUsers = collect();
            }
        }
    @endphp

    <title>{{ $mode === 'edit' ? 'Edit Project' : 'Tambah Project' }} | Project</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Poppins", sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-[#FFFAFA] text-gray-900">
    @php
        $container = 'max-w-5xl mx-auto w-full px-5 md:px-8';
    @endphp

    <header class="w-full bg-[#8D2121] text-white">
        <div class="{{ $container }} flex items-center justify-between py-3">
            <span class="font-semibold text-sm md:text-base tracking-wide">
                Bank Jakarta — Satuan Kerja Digital Banking
            </span>

            <div class="flex items-center gap-2">
                <div class="text-right text-xs leading-tight hidden sm:block">
                    <div class="font-semibold">{{ $meUser->name ?? 'User' }}</div>
                    <div class="text-white/80 text-[11px]">{{ strtoupper($meUser->role ?? '-') }}</div>
                </div>
                <div
                    class="w-8 h-8 rounded-full bg-white/20 border border-white/40 overflow-hidden flex items-center justify-center text-xs">
                    {{ mb_substr($meUser->name ?? ($meUser->username ?? 'U'), 0, 1) }}
                </div>
            </div>
        </div>
    </header>

    <main class="py-7">
        <div class="{{ $container }}">

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-lg md:text-xl font-semibold text-[#8D2121]">
                    {{ $isEdit ? 'Edit Project' : 'Tambah Project' }}
                </h1>
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center justify-center rounded-full px-5 py-2 text-xs md:text-sm font-semibold bg-[#8D2121] text-white hover:bg-[#6d1a1a]">
                    Kembali
                </a>

            </div>

            @if (session('success'))
                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-xs text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-800">
                    <div class="font-semibold mb-1">Terjadi kesalahan:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                action="{{ $isEdit ? route('semua.projects.update', $project->id) : route('semua.projects.store') }}"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                {{-- NAMA PROJECT --}}
                <section>
                    <label class="block text-sm font-semibold mb-1">Nama Project</label>
                    <input type="text" name="name" required
                        value="{{ old('name', $isEdit ? $project->name : '') }}"
                        class="w-full rounded-xl border border-[#C89898] px-4 py-3 outline-none bg-white focus:ring-1 focus:ring-[#8D2121]"
                        placeholder="Tulis nama project..." />
                </section>

                {{-- PENANGGUNG JAWAB --}}
                <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Penanggung Jawab (DIG)</label>
                        <select name="digital_banking_id" required
                            class="w-full rounded-xl border border-[#C89898] px-4 py-3 outline-none bg-white cursor-pointer focus:ring-1 focus:ring-[#8D2121]">
                            <option value="">Pilih Nama</option>
                            <optgroup label="Semua User Digital Banking">
                                @forelse(($digitalUsers ?? collect()) as $u)
                                    <option value="{{ $u->id }}" @selected((string) old('digital_banking_id', $isEdit ? $project->digital_banking_id : '') === (string) $u->id)>
                                        {{ $u->name }} {{ $u->username ? '(' . $u->username . ')' : '' }}
                                    </option>
                                @empty
                                    <option value="" disabled>Belum ada user role Digital Banking</option>
                                @endforelse
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Penanggung Jawab (IT / Developer)</label>
                        <select name="developer_id" required
                            class="w-full rounded-xl border border-[#C89898] px-4 py-3 outline-none bg-white cursor-pointer focus:ring-1 focus:ring-[#8D2121]">
                            <option value="">Pilih Nama</option>
                            <optgroup label="Semua User IT">
                                @forelse(($itUsers ?? collect()) as $u)
                                    @php
                                        $defaultDev =
                                            !$isEdit && $meUser && $meUser->role === 'it'
                                                ? $meUser->id
                                                : ($isEdit
                                                    ? $project->developer_id
                                                    : null);
                                    @endphp
                                    <option value="{{ $u->id }}" @selected((string) old('developer_id', $defaultDev) === (string) $u->id)>
                                        {{ $u->name }} {{ $u->username ? '(' . $u->username . ')' : '' }}
                                    </option>
                                @empty
                                    <option value="" disabled>Belum ada user role IT</option>
                                @endforelse
                            </optgroup>
                        </select>
                    </div>
                </section>

                {{-- DESKRIPSI --}}
                <section>
                    <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                    <textarea name="description" rows="4"
                        class="w-full rounded-xl border border-[#C89898] px-4 py-3 outline-none bg-white focus:ring-1 focus:ring-[#8D2121]"
                        placeholder="Tuliskan deskripsi project...">{{ old('description', $isEdit ? $project->description : '') }}</textarea>
                </section>

                {{-- ========== DAFTAR PROGRESS (HANYA CREATE) ========== --}}
                @unless ($isEdit)
                    <section class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm md:text-base font-semibold text-[#8D2121]">Daftar Progress</h2>
                            <button type="button" id="addProgressBtn"
                                class="inline-flex items-center gap-2 rounded-full border-2 border-[#8D2121] bg-white hover:bg-[#FFF2F2] text-[#8D2121] font-semibold h-[36px] px-3 text-xs md:text-sm">
                                <span
                                    class="grid place-items-center w-6 h-6 rounded-full bg-[#8D2121] text-white text-base leading-none">+</span>
                                Tambah Progress
                            </button>
                        </div>

                        <div id="progressList" class="space-y-4">
                            <div class="progress-row rounded-xl bg-[#E2B9B9]/50 border border-[#C89898] p-4" data-index="0">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="font-semibold text-sm">Progress <span class="progress-number">1</span></div>
                                    <button type="button"
                                        class="removeProgressBtn text-xs px-2 py-1 rounded-lg border border-red-300 text-red-700 hover:bg-red-50"
                                        disabled>Hapus</button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-semibold mb-1 block">Nama Progress</label>
                                        <input name="progresses[0][name]" required value="{{ old('progresses.0.name') }}"
                                            class="w-full rounded-xl bg-white border border-[#C89898] px-4 py-2.5 outline-none"
                                            placeholder="Nama Progress" />
                                    </div>
                                    <div>
                                        <label class="text-sm font-semibold mb-1 block">Timeline</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="date" name="progresses[0][start_date]" required
                                                value="{{ old('progresses.0.start_date') }}"
                                                class="rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
                                            <input type="date" name="progresses[0][end_date]" required
                                                value="{{ old('progresses.0.end_date') }}"
                                                class="rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
                                        </div>
                                        <label class="block text-sm font-semibold mt-3 mb-1">Target (%)</label>
                                        {{-- GANTI: dari select ke input number bebas --}}
                                        <input type="number"
                                            name="progresses[0][desired_percent]"
                                            min="0" max="100" step="1"
                                            required
                                            value="{{ old('progresses.0.desired_percent', 75) }}"
                                            placeholder="Misal 25"
                                            class="w-full rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none cursor-pointer" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <template id="progressRowTemplate">
                            <div class="progress-row rounded-xl bg-[#E2B9B9]/50 border border-[#C89898] p-4"
                                data-index="__INDEX__">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="font-semibold text-sm">Progress <span class="progress-number"></span></div>
                                    <button type="button"
                                        class="removeProgressBtn text-xs px-2 py-1 rounded-lg border border-red-300 text-red-700 hover:bg-red-50">
                                        Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-semibold mb-1 block">Nama Progress</label>
                                        <input name="progresses[__INDEX__][name]" required
                                            class="w-full rounded-xl bg-white border border-[#C89898] px-4 py-2.5 outline-none"
                                            placeholder="Nama Progress" />
                                    </div>
                                    <div>
                                        <label class="text-sm font-semibold mb-1 block">Timeline</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="date" name="progresses[__INDEX__][start_date]" required
                                                class="rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
                                            <input type="date" name="progresses[__INDEX__][end_date]" required
                                                class="rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none">
                                        </div>
                                        <label class="block text-sm font-semibold mt-3 mb-1">Target (%)</label>
                                        {{-- GANTI: dari select ke input number --}}
                                        <input type="number"
                                            name="progresses[__INDEX__][desired_percent]"
                                            min="0" max="100" step="1"
                                            required
                                            placeholder="Misal 25"
                                            class="w-full rounded-xl bg-white border border-[#C89898] px-3 py-2 outline-none cursor-pointer" />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </section>
                @endunless

                {{-- ========== LAMPIRAN PROJECT (CREATE & EDIT) ========== --}}
                <section>
                    <label class="block text-sm font-semibold mb-1">Lampiran Project (Opsional)</label>
                    <p class="text-[11px] text-gray-600 mb-2">
                        Upload file pendukung seperti BRD, desain, atau dokumen lain (PDF / gambar).
                        Kamu bisa klik tombol <b>Tambah File</b> berkali-kali, semua file akan ikut tersimpan.
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" id="{{ $isEdit ? 'editAttachmentBtn' : 'createAttachmentBtn' }}"
                            class="px-4 py-2 rounded-lg bg-[#7A1C1C] text-white text-xs md:text-sm font-semibold">
                            + Tambah File
                        </button>
                        <span class="text-[11px] text-gray-600">Maksimal 5MB per file.</span>
                    </div>

                    {{-- wrapper tempat semua <input type="file" ...> dinamis --}}
                    <div id="{{ $isEdit ? 'editAttachmentInputs' : 'createAttachmentInputs' }}"></div>

                    {{-- list preview file baru --}}
                    <ul id="{{ $isEdit ? 'editAttachmentList' : 'createAttachmentList' }}"
                        class="mt-3 space-y-2 text-xs md:text-sm text-gray-800"></ul>

                    {{-- LAMPIRAN LAMA (HANYA EDIT) --}}
                    @if ($isEdit)
                        <div class="mt-4">
                            <h3 class="text-xs font-semibold mb-1">Lampiran yang sudah tersimpan</h3>
                            @if ($project->attachments && $project->attachments->isNotEmpty())
                                <p class="text-[11px] text-gray-600 mb-1">
                                    Centang <b>Hapus</b> jika ingin menghapus file tersebut.
                                </p>
                                <ul class="space-y-2 text-xs md:text-sm">
                                    @foreach ($project->attachments as $att)
                                        @php
                                            $isPdf =
                                                str_contains(strtolower($att->mime_type ?? ''), 'pdf') ||
                                                \Illuminate\Support\Str::endsWith(
                                                    strtolower($att->original_name),
                                                    '.pdf',
                                                );
                                            $url = route('attachments.show', $att->id);
                                        @endphp
                                        <li
                                            class="flex items-center justify-between gap-3 rounded-lg border border-[#E7C9C9] bg-[#FFF7F7] px-3 py-2">
                                            <a href="{{ $url }}" target="_blank"
                                                class="flex items-center gap-2 min-w-0">
                                                @if ($isPdf)
                                                    <span
                                                        class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-red-600 text-white text-[10px]">
                                                        PDF
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-amber-500 text-white text-[10px]">
                                                        IMG
                                                    </span>
                                                @endif
                                                <span class="truncate max-w-[190px]"
                                                    title="{{ $att->original_name }}">
                                                    {{ $att->original_name }}
                                                </span>
                                            </a>
                                            <label class="inline-flex items-center gap-1 text-[11px] text-red-700">
                                                <input type="checkbox" name="delete_attachments[]"
                                                    value="{{ $att->id }}"
                                                    class="rounded border-red-300 text-red-600 focus:ring-red-400">
                                                <span>Hapus</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-[11px] text-gray-500">Belum ada lampiran untuk project ini.</p>
                            @endif
                        </div>
                    @endif
                </section>

                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ route('semua.progresses') }}"
                        class="w-[130px] h-[40px] inline-flex items-center justify-center rounded-full border-2 border-[#7A1C1C] bg-white text-[#7A1C1C] text-sm font-semibold">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-[140px] h-[40px] inline-flex items-center justify-center rounded-full border-2 border-[#7A1C1C] bg-[#E2B9B9] hover:bg-[#D9AFAF] text-sm font-semibold text-black">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // ====== LAMPIRAN DINAMIS (CREATE & EDIT) ======
        (function() {
            function setupDynamicAttachment(prefix) {
                const wrapper = document.getElementById(prefix + 'AttachmentInputs');
                const btn = document.getElementById(prefix + 'AttachmentBtn');
                const list = document.getElementById(prefix + 'AttachmentList');

                if (!wrapper || !btn || !list) return;

                function makeInput() {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.name = 'attachments[]';
                    input.accept = '.pdf,image/*';
                    input.multiple = true; // user boleh select beberapa file sekaligus
                    input.className = 'hidden attachment-input';
                    wrapper.appendChild(input);

                    input.addEventListener('change', () => {
                        if (!input.files || !input.files.length) return;

                        Array.from(input.files).forEach(file => {
                            const li = document.createElement('li');
                            const sizeKB = Math.round(file.size / 1024);
                            li.className =
                                'flex items-center justify-between rounded-lg border border-[#E7C9C9] bg-[#FFF7F7] px-3 py-2';
                            li.innerHTML = `
                            <span class="truncate max-w-[220px]">${file.name}</span>
                            <span class="text-[11px] text-gray-500">${sizeKB} KB</span>
                        `;
                            list.appendChild(li);
                        });

                        // setelah user pilih file di input ini,
                        // langsung siapkan input baru yang kosong untuk klik berikutnya
                        makeInput();
                    });

                    return input;
                }

                // buat satu input awal
                makeInput();

                btn.addEventListener('click', () => {
                    const inputs = wrapper.querySelectorAll('.attachment-input');
                    const last = inputs[inputs.length - 1];
                    if (last) last.click();
                });
            }

            setupDynamicAttachment('create');
            setupDynamicAttachment('edit');
        })();

        // ====== BUILDER PROGRESS (CREATE SAJA) ======
        (function() {
            const listEl = document.getElementById('progressList');
            const addBtn = document.getElementById('addProgressBtn');
            const template = document.getElementById('progressRowTemplate');

            if (!listEl || !addBtn || !template) return;

            function renumber() {
                const rows = listEl.querySelectorAll('.progress-row');
                rows.forEach((row, idx) => {
                    row.querySelector('.progress-number').textContent = idx + 1;
                    const removeBtn = row.querySelector('.removeProgressBtn');
                    if (idx === 0) {
                        removeBtn.disabled = true;
                        removeBtn.classList.add('opacity-60', 'cursor-not-allowed');
                    } else {
                        removeBtn.disabled = false;
                        removeBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                });
            }

            function attachRemove(row) {
                const btn = row.querySelector('.removeProgressBtn');
                btn.addEventListener('click', () => {
                    row.remove();
                    renumber();
                });
            }

            addBtn.addEventListener('click', () => {
                const currentIndex = listEl.querySelectorAll('.progress-row').length;
                const html = template.innerHTML.replace(/__INDEX__/g, String(currentIndex));
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html.trim();
                const row = wrapper.firstElementChild;
                listEl.appendChild(row);
                attachRemove(row);
                renumber();
            });

            listEl.querySelectorAll('.progress-row').forEach(attachRemove);
            renumber();
        })();
    </script>
</body>

</html>
