{{-- 
    Notification Card Partial
    Variables: $n (notification), $norm (normalizer function)
--}}
@php
    $d         = $n->data ?? [];
    $type      = $norm(data_get($d,'type'));
    $late      = (bool) data_get($d,'late', false);
    $pId       = data_get($d,'project_id');
    $pName     = data_get($d,'project_name', 'Project');
    $progId    = data_get($d,'progress_id');
    $progName  = data_get($d,'progress_name', 'Progress');
    $headline  = data_get($d,'message', '');
    $created   = optional($n->created_at)->timezone('Asia/Jakarta');
    $timeText  = $created ? $created->format('H.i') : '-';
    $dateText  = $created ? $created->translatedFormat('d M Y') : '-';
    $dateTime  = $created ? $created->format('d M Y, H.i') : '-';
    $isUnread  = is_null($n->read_at);
@endphp

@if($type === 'progress_confirmed')
    {{-- KARTU: IT KONFIRMASI PROGRESS --}}
    <div class="rounded-xl px-5 py-4 border {{ $isUnread ? 'border-[#7A1C1C] bg-[#F2DCDC]' : 'border-[#E7C9C9] bg-white' }}">
        <div class="flex items-center justify-between">
            <div class="text-[15px] font-semibold">
                {{ $timeText }}
                <span class="text-[12px] font-normal text-gray-600">(jam dikonfirmasi)</span>
            </div>
            <div class="text-[14px] font-semibold text-right">
                {{ $headline ?: 'IT telah mengonfirmasi' }} {{ $progName }}
            </div>
        </div>

        <div class="mt-3 text-[14px] leading-6">
            <div><span class="font-semibold">Nama Project :</span> {{ $pName }}</div>
            <div>
                @if(function_exists('route') && Route::has('dig.projects.show') && $pId)
                    <a class="underline text-[#0a58ca]" href="{{ route('dig.projects.show', $pId) }}">{{ $progName }}</a>
                @else
                    <span class="underline">{{ $progName }}</span>
                @endif
            </div>
            <div class="mt-1 text-xs text-gray-600">
                {{ $dateText }} • {{ $timeText }} WIB
            </div>
        </div>

        <div class="mt-3 flex items-center justify-between">
            <div>
                @if($late)
                    <span class="inline-flex items-center text-[11px] px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                        IT Tidak Memenuhi Target
                    </span>
                @else
                    <span class="inline-flex items-center text-[11px] px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                        IT Telah Mengonfirmasi
                    </span>
                @endif
            </div>
            <form method="POST" action="{{ route('dig.notifications.read', $n->id) }}">
                @csrf
                <button class="text-xs underline text-[#7A1C1C]">
                    {{ $n->read_at ? 'Terbaca' : 'Tandai terbaca' }}
                </button>
            </form>
        </div>
    </div>

@elseif($type === 'it_project_created')
    {{-- KARTU: IT MEMBUAT PROJECT BARU --}}
    <div class="rounded-xl px-5 py-4 border {{ $isUnread ? 'border-[#7A1C1C] bg-[#F2DCDC]' : 'border-[#E7C9C9] bg-white' }}">
        <div class="flex items-center justify-between">
            <div class="text-[15px] font-semibold">
                {{ $timeText }}
                <span class="text-[12px] font-normal text-gray-600">(jam dibuat)</span>
            </div>
            <div class="text-[14px] font-semibold text-right">
                {{ $headline ?: 'IT membuat project baru' }}
            </div>
        </div>

        <div class="mt-3 text-[14px] leading-6">
            <div><span class="font-semibold">Nama Project :</span> {{ $pName }}</div>
            <div>
                @if(function_exists('route') && Route::has('dig.projects.show') && $pId)
                    <a class="underline text-[#0a58ca]" href="{{ route('dig.projects.show', $pId) }}">Lihat detail project</a>
                @endif
            </div>
            <div class="mt-1 text-xs text-gray-600">
                {{ $dateText }} • {{ $timeText }} WIB
            </div>
        </div>

        <div class="mt-3 flex items-center justify-between">
            <div>
                <span class="inline-flex items-center text-[11px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                    Project baru dari IT
                </span>
            </div>
            <form method="POST" action="{{ route('dig.notifications.read', $n->id) }}">
                @csrf
                <button class="text-xs underline text-[#7A1C1C]">
                    {{ $n->read_at ? 'Terbaca' : 'Tandai terbaca' }}
                </button>
            </form>
        </div>
    </div>

@else
    {{-- FALLBACK UMUM --}}
    <div class="rounded-xl px-5 py-4 border {{ $isUnread ? 'border-[#7A1C1C] bg-[#F2DCDC]' : 'border-[#E7C9C9] bg-white' }}">
        <div class="flex items-center justify-between">
            <div class="text-[13px] font-semibold text-gray-700">
                {{ $dateTime }}
            </div>
            <div class="text-[14px] font-semibold text-right">
                Notifikasi
            </div>
        </div>
        <div class="mt-3 text-[14px] leading-6">
            <div class="text-gray-700">{{ $headline ?: 'Ada pembaruan dari IT.' }}</div>
        </div>
        <div class="mt-3 flex items-center justify-end">
            <form method="POST" action="{{ route('dig.notifications.read', $n->id) }}">
                @csrf
                <button class="text-xs underline text-[#7A1C1C]">
                    {{ $n->read_at ? 'Terbaca' : 'Tandai terbaca' }}
                </button>
            </form>
        </div>
    </div>
@endif
