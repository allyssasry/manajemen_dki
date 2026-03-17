{{-- EXAMPLE: Cara mengupdate notifications.blade.php ke struktur baru --}}

@extends('layouts.dig')

@section('title', 'Notifikasi DIG')

@php
    $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';
    
    // Prepare your data here
    // $notifications = ...
@endphp

<!-- BANNER (optional) -->
<section class="relative h-[200px] md:h-[250px] overflow-hidden">
    <img src="https://i.pinimg.com/736x/c5/43/71/c543719c97d9efa97da926387fa79d1f.jpg"
        class="w-full h-full object-cover" alt="Banner" />
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="absolute inset-0 flex items-center justify-center">
        <h1 class="text-white text-2xl md:text-3xl font-bold">Notifikasi</h1>
    </div>
</section>

<!-- YOUR CONTENT -->
<div class="{{ $container }} my-6">
    @if (session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- Your notifications listing here -->
</div>

@push('styles')
    <!-- Additional styles if needed -->
@endpush

@push('scripts')
    <!-- Additional scripts if needed -->
@endpush
