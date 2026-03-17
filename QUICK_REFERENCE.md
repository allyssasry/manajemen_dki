# ⚡ QUICK REFERENCE - DIG Clean Code

## 🚀 30-Second Setup

```bash
# 1. Verify files exist
ls resources/views/components/dig/
ls resources/views/layouts/dig.blade.php

# 2. Update dashboard.blade.php dengan isi dari dashboard-new.blade.php

# 3. Test di browser
http://localhost:8000/dig/dashboard

# Done! ✨
```

---

## 📝 Template untuk Halaman Baru

Untuk setiap halaman baru di folder `dig/`:

```blade
@extends('layouts.dig')

@section('title', 'Page Title')

@section('content')

@php
    $container = 'max-w-6xl mx-auto w-full px-5 md:px-6 lg:px-8';
    // Setup your data here
@endphp

<!-- Your content here -->

@endsection
```

---

## 🎨 Common Patterns

### Pattern 1: Banner Section
```blade
<section class="relative h-[260px] md:h-[320px] overflow-hidden">
    <img src="https://..." class="w-full h-full object-cover" alt="Banner" />
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="absolute inset-0 flex items-center justify-center">
        <h1 class="text-white text-2xl md:text-3xl font-bold">Title</h1>
    </div>
</section>
```

### Pattern 2: Card Container
```blade
<div class="rounded-2xl border border-red-200 bg-white p-4">
    <div class="font-semibold text-lg mb-4">Card Title</div>
    <div class="grid md:grid-cols-2 gap-4">
        <!-- Content -->
    </div>
</div>
```

### Pattern 3: KPI Card (Maroon)
```blade
<div class="rounded-2xl card-maroon border p-4 grid min-h-[140px]">
    <div class="font-semibold text-sm">Title</div>
    <div class="text-4xl font-bold place-self-center text-white">42</div>
    <div class="text-[11px] text-white/80 mt-2 text-center">Subtitle</div>
</div>
```

### Pattern 4: Add Custom Styles
```blade
@push('styles')
    <style>
        .custom-class {
            background: red;
        }
    </style>
@endpush
```

### Pattern 5: Add Custom Scripts
```blade
@push('scripts')
    <script>
        console.log('Page loaded!');
    </script>
@endpush
```

---

## 🛠️ File Locations Cheat Sheet

| What | Location |
|------|----------|
| Sidebar changes | `resources/views/components/dig/sidebar.blade.php` |
| Mini sidebar | `resources/views/components/dig/mini-sidebar.blade.php` |
| Navbar changes | `resources/views/components/dig/navbar.blade.php` |
| Global layout | `resources/views/layouts/dig.blade.php` |
| New page | `resources/views/dig/your-page.blade.php` |
| New component | `resources/views/components/dig/your-component.blade.php` |

---

## 🧪 Testing Checklist

After any change:
- [ ] Sidebar toggle works (mobile)
- [ ] Sidebar close on link click
- [ ] Navbar displays user info
- [ ] Logout modal appears
- [ ] Responsive at all breakpoints
- [ ] No console errors
- [ ] No network errors

---

## ❌ Common Mistakes

### ❌ WRONG
```blade
<!doctype html>
<html>
<head>...</head>
<body>
    <aside><!-- sidebar code --></aside>
    <!-- content -->
</body>
</html>
```

### ✅ RIGHT
```blade
@extends('layouts.dig')
@section('content')
    <!-- content only -->
@endsection
```

---

### ❌ WRONG
```blade
<!-- Copy sidebar code from another file -->
<aside id="sidebar">...</aside>
```

### ✅ RIGHT
```blade
<!-- Use layout, sidebar already included -->
<!-- No need to add sidebar! -->
```

---

### ❌ WRONG
```blade
<x-dig.navbar>Dashboard</x-dig.navbar>
<x-dig.navbar>Notifikasi</x-dig.navbar>
<x-dig.navbar>Detail</x-dig.navbar>
<!-- 3 navbar instances -->
```

### ✅ RIGHT
```blade
<!-- Navbar already in layout -->
<!-- Just use @extends('layouts.dig') -->
```

---

## 🎯 Quick Customizations

### Change Navbar Title
```blade
@extends('layouts.dig')
@section('title', 'My Custom Title') <!-- ← Changes both title tag & browser tab -->
```

### Add Custom Colors
```blade
@push('styles')
    <style>
        .brand-primary { color: #8D2121; }
        .brand-dark { color: #7A1C1C; }
    </style>
@endpush
```

### Add JavaScript
```blade
@push('scripts')
    <script>
        document.querySelectorAll('.my-element').forEach(el => {
            el.addEventListener('click', () => {
                console.log('Clicked!');
            });
        });
    </script>
@endpush
```

---

## 🔗 Component Usage Examples

### Navbar with Custom Title
```blade
<x-dig.navbar :container="$container">
    Dashboard
</x-dig.navbar>
```

### Logout Modal
```blade
<x-dig.logout-modal id="myLogoutModal" />
```

---

## 🐛 Quick Troubleshoot

**Sidebar not showing?**
```blade
<!-- Check: Are you using @extends('layouts.dig')? -->
<!-- Sidebar is auto-included in layout -->
```

**Navbar not showing?**
```blade
<!-- Check: Using correct layout? -->
@extends('layouts.dig')
```

**Modal not showing?**
```html
<!-- Check browser console for JS errors -->
<!-- Modal is auto-included in layout -->
```

**Styles not applying?**
```blade
@push('styles')
    <style>
        .my-class { color: red !important; }
    </style>
@endpush
```

---

## 📚 Full Documentation

- **Setup** → `REFACTOR_GUIDE.md`
- **Step-by-step** → `IMPLEMENTATION_GUIDE.md`
- **Examples** → `EXAMPLE_NOTIFICATIONS.md`
- **Before/After** → `BEFORE_AFTER_COMPARISON.md`
- **File List** → `FILE_SUMMARY.md`
- **This file** → `QUICK_REFERENCE.md`

---

## 🎁 Pro Tips

**Tip 1**: Use CSS classes consistently
```blade
<!-- Use existing classes -->
.card-maroon (dark red card)
.chip-soft (light red chip)
.scroll-thin (custom scrollbar)
```

**Tip 2**: Keep content minimal in views
```blade
<!-- Put logic in controller -->
<!-- Put data prep in @php blade section -->
<!-- Views only for presentation -->
```

**Tip 3**: Use @push for addon scripts
```blade
<!-- Don't modify layout.blade.php -->
<!-- Use @push('scripts') to add scripts -->
```

**Tip 4**: Extract repeated sections to components
```blade
<!-- If you repeat same HTML 3x -->
<!-- Make it a component -->
<!-- Reuse with <x-my-component /> -->
```

---

**Remember**: When in doubt, check the example in `dashboard-new.blade.php`! 😊

