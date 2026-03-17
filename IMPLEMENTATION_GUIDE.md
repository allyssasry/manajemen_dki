# 🎯 PANDUAN IMPLEMENTASI - REFACTOR CLEAN CODE DIG

## 📌 Ringkasan Perubahan

Telah membuat struktur clean code untuk folder `dig/` dengan cara memisahkan:
- ✅ Sidebar (mini & full) → `components/dig/mini-sidebar.blade.php` & `components/dig/sidebar.blade.php`
- ✅ Navbar → `components/dig/navbar.blade.php`
- ✅ Modal logout → `components/dig/logout-modal.blade.php`
- ✅ Layout utama → `layouts/dig.blade.php`
- ✅ Dashboard baru → `dig/dashboard-new.blade.php` (contoh penggunaan)

---

## 🚀 STEP 1: Verify Components

Pastikan file-file berikut sudah ada:

```bash
resources/views/components/dig/
├── mini-sidebar.blade.php      ✅
├── sidebar.blade.php           ✅
├── navbar.blade.php            ✅
└── logout-modal.blade.php      ✅

resources/views/layouts/
└── dig.blade.php               ✅

resources/views/dig/
└── dashboard-new.blade.php     ✅ (contoh)
```

---

## 🔧 STEP 2: Update File-File Existing

### Opsi A: Update `dig/dashboard.blade.php` (Recommended)

**Langkah:**
1. Backup file original
2. Ganti seluruh isi dengan konten dari `dashboard-new.blade.php`
3. Test di browser

### Opsi B: Buat File Baru Dulu

1. Rename `dashboard.blade.php` → `dashboard-old.blade.php`
2. Copy `dashboard-new.blade.php` → `dashboard.blade.php`
3. Test
4. Delete `dashboard-old.blade.php` setelah confirm OK

---

## 📄 STEP 3: Update File Lainnya

### Untuk `dig/notifications.blade.php`

**Before:**
```blade
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Notifikasi DIG</title>
    <!-- 100+ lines of sidebar, navbar, modal code -->
</head>
<body>
    <aside id="miniSidebar">...</aside>
    <aside id="sidebar">...</aside>
    <div id="pageWrapper">
        <header>...</header>
        <!-- YOUR ACTUAL CONTENT -->
    </div>
    <div id="confirmLogoutModal">...</div>
</body>
</html>
```

**After:**
```blade
@extends('layouts.dig')

@section('title', 'Notifikasi DIG')

@section('content')

@php
    $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';
    // Setup data here
@endphp

<section class="relative h-[200px] md:h-[250px] overflow-hidden">
    <img src="..." class="w-full h-full object-cover" alt="Banner" />
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="absolute inset-0 flex items-center justify-center">
        <h1 class="text-white text-2xl md:text-3xl font-bold">Notifikasi</h1>
    </div>
</section>

<div class="{{ $container }} my-6">
    <!-- YOUR CONTENT HERE -->
</div>

@endsection
```

### Untuk `dig/detail.blade.php`

Terapkan pola yang sama seperti `notifications.blade.php`

---

## ✨ STEP 4: Verification Checklist

Setelah update, cek berikut:

- [ ] Sidebar toggle bekerja (klik logo/hamburger menu)
- [ ] Sidebar close saat click link (mobile)
- [ ] Navbar tampil dengan user info
- [ ] Logout click → modal appears
- [ ] Logout button di modal bekerja
- [ ] Responsive di desktop (1200px+)
- [ ] Responsive di tablet (768px - 1199px)
- [ ] Responsive di mobile (< 768px)
- [ ] Tidak ada error di console
- [ ] Tidak ada hardcoded sidebar/navbar di halaman

---

## 🎨 STEP 5: Customization

### Jika ingin mengubah Sidebar

Edit: `resources/views/components/dig/sidebar.blade.php`

Contoh: Menambah menu baru
```blade
<a href="{{ route('new.menu') }}"
    class="flex items-center gap-3 px-5 py-2.5 rounded-xl hover:bg-[#FFF2F2]">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" fill="black" viewBox="0 0 24 24">
        <!-- SVG icon -->
    </svg>
    <span>Menu Baru</span>
</a>
```

**Langsung otomatis muncul di:**
- Desktop mini sidebar (rail icon)
- Mobile full sidebar (drawer)
- Tidak perlu update di setiap file view!

### Jika ingin mengubah Navbar

Edit: `resources/views/components/dig/navbar.blade.php`

Contoh: Menambah search bar
```blade
<div class="flex-1 max-w-md mx-4">
    <input type="text" placeholder="Search..." class="w-full px-4 py-2 rounded-lg border border-white/20 bg-white/10 text-white placeholder-white/50" />
</div>
```

---

## 🐛 TROUBLESHOOTING

### Problem: Sidebar tidak bisa dibuka

**Solution:**
```javascript
// Check localStorage
localStorage.getItem('dig.sidebar.open')
// Should return '0' or '1'
```

### Problem: Navbar tidak tampil

**Solution:**
```blade
<!-- Check layout di head -->
<x-dig.navbar :container="$container">
    Your Title
</x-dig.navbar>
```

### Problem: Modal logout tidak muncul

**Solution:**
```blade
<!-- Check di HTML footer -->
<x-dig.logout-modal />
```

### Problem: Sidebar double-muncul

**Solution:**
- Pastikan hanya di `layouts/dig.blade.php` ada sidebar
- Jangan tambah sidebar di file view individual
- Pastikan menggunakan `@extends('layouts.dig')`

---

## 📊 STRUKTUR FOLDER FINAL

```
resources/views/
├── components/
│   ├── dig/                           # DIG components
│   │   ├── mini-sidebar.blade.php     # Desktop rail
│   │   ├── sidebar.blade.php          # Mobile drawer
│   │   ├── navbar.blade.php           # Header
│   │   └── logout-modal.blade.php     # Confirmation
│   └── ... (other components)
├── layouts/
│   ├── app.blade.php                  # Main layout (existing)
│   ├── dig.blade.php                  # DIG layout (NEW)
│   └── ... (other layouts)
├── dig/
│   ├── dashboard.blade.php            # UPDATED (bersih)
│   ├── dashboard-old.blade.php        # Backup (bisa dihapus)
│   ├── notifications.blade.php        # UPDATED (bersih)
│   └── detail.blade.php               # UPDATED (bersih)
└── ... (other views)
```

---

## 🎁 BONUS: Buat Component Tambahan

Jika ada section yang sering dipakai, buat component:

### Contoh: KPI Cards Component

File: `resources/views/components/dig/kpi-card.blade.php`
```blade
@props(['title', 'value', 'subtitle' => null])

<div class="rounded-2xl card-maroon border p-4 grid min-h-[140px]">
    <div class="font-semibold text-sm mb-3">{{ $title }}</div>
    <div class="text-4xl font-bold place-self-center text-white">{{ $value }}</div>
    @if ($subtitle)
        <div class="text-[11px] text-white/80 mt-2 text-center">{{ $subtitle }}</div>
    @endif
</div>
```

**Penggunaan:**
```blade
<x-dig.kpi-card 
    title="Project Selesai"
    value="15"
    subtitle="Target: 20"
/>
```

---

## ✅ NEXT STEPS

1. ✅ Update `dashboard.blade.php`
2. ✅ Update `notifications.blade.php`
3. ✅ Update `detail.blade.php`
4. 🔄 Test semua halaman
5. 🎨 Customize warna/style sesuai kebutuhan
6. 🚀 Deploy ke production

---

**Status**: Ready to use  
**Last Updated**: 2026-01-15  
**Support**: Check REFACTOR_GUIDE.md untuk detail lebih

