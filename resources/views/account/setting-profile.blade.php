@extends('layouts.dashboard')

@section('title', 'Pengaturan Profil')
@section('pageTitle', 'Pengaturan Profil')

@section('headStyles')
    <style>
        body {
            background: #F8ECEC;
        }

        .readonly .editable-field {
            background: #F5EAEA;
            cursor: not-allowed;
        }

        .readonly #avatarLabel {
            pointer-events: none;
            opacity: .6;
        }
    </style>
@endsection

@section('content')
    @php
        $me = auth()->user();

        $initial = urlencode(mb_substr($me?->name ?? ($me?->username ?? 'U'), 0, 1));
        $fallbackSvg =
            'data:image/svg+xml;utf8,' .
            rawurlencode(
                '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">' .
                    '<rect width="100%" height="100%" rx="12" ry="12" fill="#7A1C1C"/>' .
                    '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="28" fill="#fff">' .
                    $initial .
                    '</text>' .
                    '</svg>',
            );
        $rawUrl = $me?->avatar_url_public;
        $extraKey = $me?->avatar_cache_key ?? ($me?->updated_at?->timestamp ?? time());
        $avatarUrl = $rawUrl ? $rawUrl . (str_contains($rawUrl, '?') ? '&' : '?') . 'ck=' . $extraKey : $fallbackSvg;
    @endphp

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

        <div id="accountCard" class="readonly rounded-2xl border border-[#C89898] bg-[#FFF5F5] p-5">
            <div class="flex items-start justify-between mb-4">
                <h2 class="text-base font-semibold">Informasi Personal</h2>
                <button type="button" id="toggleEditBtn"
                    class="px-3 py-1.5 rounded-full bg-emerald-600 text-white text-xs font-semibold hover:opacity-90">
                    Edit
                </button>
            </div>

            <form id="accountForm" action="{{ route('account.update') }}" method="POST"
                enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-6">
                @csrf @method('PUT')

                <div class="md:col-span-7 space-y-4">
                    @php
                        $fg = $me?->first_name ?: explode(' ', $me?->name ?? '')[0] ?? '';
                        $lg =
                            $me?->last_name ?:
                            (trim(implode(' ', array_slice(explode(' ', $me?->name ?? ''), 1))) ?:
                            '');
                    @endphp
                    @foreach ([['label' => 'Nama Awalan', 'name' => 'first_name', 'value' => $fg], ['label' => 'Nama Terakhir', 'name' => 'last_name', 'value' => $lg], ['label' => 'Username', 'name' => 'username', 'value' => $me?->username], ['label' => 'Email', 'name' => 'email', 'value' => $me?->email], ['label' => 'Alamat', 'name' => 'address', 'value' => $me?->address], ['label' => 'No. Telepon', 'name' => 'phone', 'value' => $me?->phone]] as $f)
                        <div>
                            <label class="block text-xs mb-1">{{ $f['label'] }}:</label>
                            <input type="text" name="{{ $f['name'] }}"
                                value="{{ old($f['name'], $f['value']) }}"
                                class="editable-field w-full rounded-xl bg-white border border-[#C89898] px-4 py-2.5 outline-none focus:ring-2 focus:ring-[#7A1C1C]/30">
                        </div>
                    @endforeach

                    <div id="formActions" class="pt-2 flex gap-3 hidden">
                        <button type="submit"
                            class="inline-flex justify-center min-w-[160px] h-10 items-center rounded-full border-2 border-[#7A1C1C] bg-[#E2B9B9] hover:bg-[#D9AFAF] text-black text-sm font-semibold">
                            Simpan Perubahan
                        </button>
                        <button type="button" id="cancelEditBtn"
                            class="inline-flex justify-center min-w-[160px] h-10 items-center rounded-full border-2 border-[#7A1C1C] bg-white text-[#7A1C1C] text-sm font-semibold">
                            Batalkan Perubahan
                        </button>
                    </div>
                </div>

                <div class="md:col-span-5">
                    <div class="rounded-2xl overflow-hidden bg-white shadow-sm border border-[#C89898]">
                        <img id="avatarPreview" src="{{ $avatarUrl }}" alt="Foto Profil"
                            class="w-full aspect-[4/3] object-cover"
                            onerror="this.onerror=null;this.src='{{ $fallbackSvg }}';">
                    </div>

                    <label id="avatarLabel" class="mt-3 block">
                        <input id="avatarInput" type="file" name="avatar" accept="image/*"
                            class="hidden editable-field-file">
                        <span
                            class="inline-flex items-center justify-center w-full rounded-full bg-[#7A1C1C] text-white px-4 py-2 text-sm font-semibold shadow hover:opacity-95 cursor-pointer">
                            Ubah Foto Profil
                        </span>
                    </label>
                    <p class="text-[11px] text-gray-600 mt-2 text-center">*Upload PNG, JPG, JPEG. Maks 2MB</p>
                </div>
            </form>
        </div>
    </main>
@endsection

@section('scripts')
    @php
        $me = auth()->user();
        $initial = urlencode(mb_substr($me?->name ?? ($me?->username ?? 'U'), 0, 1));
        $fallbackSvg =
            'data:image/svg+xml;utf8,' .
            rawurlencode(
                '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">' .
                    '<rect width="100%" height="100%" rx="12" ry="12" fill="#7A1C1C"/>' .
                    '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="28" fill="#fff">' .
                    $initial .
                    '</text>' .
                    '</svg>',
            );
        $rawUrl = $me?->avatar_url_public;
        $extraKey = $me?->avatar_cache_key ?? ($me?->updated_at?->timestamp ?? time());
        $avatarUrl = $rawUrl ? $rawUrl . (str_contains($rawUrl, '?') ? '&' : '?') . 'ck=' . $extraKey : $fallbackSvg;
    @endphp

    <script>
        const card = document.getElementById('accountCard');
        const form = document.getElementById('accountForm');
        const editBtn = document.getElementById('toggleEditBtn');
        const cancelBtn = document.getElementById('cancelEditBtn');
        const actions = document.getElementById('formActions');
        const fields = form.querySelectorAll('.editable-field');
        const fileInput = form.querySelector('.editable-field-file');
        const avatar = document.getElementById('avatarPreview');
        let editing = false;

        function setEditing(state) {
            editing = state;
            fields.forEach(f => f.disabled = !state);
            if (fileInput) fileInput.disabled = !state;
            card.classList.toggle('readonly', !state);
            actions.classList.toggle('hidden', !state);
            if (editBtn) editBtn.style.display = state ? 'none' : 'inline-block';
        }

        setEditing(false);

        editBtn?.addEventListener('click', () => setEditing(true));
        cancelBtn?.addEventListener('click', () => {
            form.reset();
            avatar.src = @json($avatarUrl);
            setEditing(false);
        });

        const input = document.getElementById('avatarInput');
        input?.addEventListener('change', e => {
            if (!editing) return;
            const file = e.target.files?.[0];
            if (file) avatar.src = URL.createObjectURL(file);
        });
    </script>
@endsection
