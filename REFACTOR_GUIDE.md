# Refactor Guide - Clean Code Structure untuk Folder DIG

## 📁 Struktur Baru

```
resources/
├── views/
│   ├── components/
│   │   └── dig/                          # ✨ BARU: Component umum DIG
│   │       ├── mini-sidebar.blade.php    # Mini sidebar untuk desktop
│   │       ├── sidebar.blade.php         # Full sidebar untuk mobile
│   │       ├── navbar.blade.php          # Navbar header
│   │       └── logout-modal.blade.php    # Modal logout confirmation
│   ├── layouts/
│   │   ├── app.blade.php                 # (Existing)
│   │   └── dig.blade.php                 # ✨ BARU: Main layout untuk DIG
│   └── dig/
│       ├── dashboard-new.blade.php       # ✨ BARU: Dashboard yang sudah direfactor
│       ├── detail.blade.php              # (Existing, bisa update menggunakan layout baru)
│       └── notifications.blade.php       # (Existing, bisa update menggunakan layout baru)
```

## 🎯 Keuntungan Refactor

1. **Single Source of Truth**: Sidebar, navbar, dan modal logout hanya ada di 1 tempat
2. **Mudah Maintenance**: Jika ingin mengubah sidebar, cukup edit `components/dig/sidebar.blade.php`
3. **Reusable**: Setiap halaman di folder DIG bisa menggunakan layout yang sama
4. **Clean Code**: Memisahkan logic dari presentasi

## 📝 Cara Menggunakan

### 1. **File Layout Baru** (`resources/views/layouts/dig.blade.php`)
Layout ini berisi:
- Structure HTML dasar (DOCTYPE, head, body)
- Mini sidebar + Full sidebar
- Navbar
- Logout modal
- Script untuk sidebar toggle dan logout confirmation

### 2. **Components yang Bisa Dipakai Ulang**

#### `x-dig.navbar`
```blade
<x-dig.navbar :container="$container">
    Dashboard Title
</x-dig.navbar>
```

#### `x-dig.sidebar` & `x-dig.mini-sidebar`
Sudah otomatis dipanggil di layout

#### `x-dig.logout-modal`
Sudah otomatis dipanggil di layout

### 3. **Menggunakan Layout di Halaman**

```blade
@extends('layouts.dig')
@section('title', 'Dashboard DIG')

@php
    // Data preparation
    $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';
@endphp

<!-- Content -->
```

## 🔄 Migrasi File Existing

### Untuk `notifications.blade.php`
Ganti dari:
```blade
<!doctype html>
<html lang="id">
<head>...</head>
<body>
    <!-- SIDEBAR CODE (100+ lines) -->
    <!-- NAVBAR CODE (50+ lines) -->
    <!-- MODAL CODE (30+ lines) -->
    <!-- YOUR CONTENT -->
</body>
</html>
```

Menjadi:
```blade
@extends('layouts.dig')
@section('title', 'Notifikasi DIG')

<!-- HANYA CONTENT ANDA -->
```

## 📋 Checklist Migrasi

- [ ] Update `detail.blade.php` ke layout baru
- [ ] Update `notifications.blade.php` ke layout baru
- [ ] Test sidebar toggle
- [ ] Test logout confirmation
- [ ] Test responsive di mobile & desktop
- [ ] Delete file lama kalau sudah migrasi semua

## 🛠️ Struktur Folder Components

Jika Anda ingin menambah lebih banyak components:

```
resources/views/components/dig/
├── mini-sidebar.blade.php
├── sidebar.blade.php
├── navbar.blade.php
├── logout-modal.blade.php
├── banner.blade.php              # (Future) Banner reusable
├── kpi-section.blade.php         # (Future) KPI cards
└── project-list.blade.php        # (Future) Project listing
```

## 📌 Notes

1. **Extends vs Include**: Gunakan `@extends('layouts.dig')` untuk layout, bukan `@include()`
2. **Props**: Components bisa menerima data via props: `<x-dig.navbar :container="$container">`
3. **Slots**: Gunakan `{{ $slot }}` untuk content di tengah
4. **Push**: Gunakan `@push('scripts')` untuk JS di bagian bawah layout

## 🚀 Next Steps

Setelah migrasi berhasil:
1. Hapus code sidebar, navbar, modal dari setiap file view
2. Buat components untuk bagian yang sering dipakai (banner, cards, dll)
3. Extract logic kompleks ke Service class
4. Tambahkan unit tests

---

**Status**: Ready untuk digunakan  
**Last Updated**: 2026-01-15
