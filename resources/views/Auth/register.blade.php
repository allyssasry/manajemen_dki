<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Sign Up • Bank Jakarta</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Sembunyikan tombol reveal bawaan Edge/Chromium agar tidak muncul ikon mata hitam */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
      display: none !important;
      width: 0;
      height: 0;
    }
    /* Safari/WebKit kredensial button (jika muncul) */
    input::-webkit-credentials-auto-fill-button {
      visibility: hidden !important;
      display: none !important;
      pointer-events: none !important;
    }
  </style>
</head>
<body class="min-h-screen bg-white text-gray-800 flex items-start md:items-center justify-center p-6">

  <div class="w-full max-w-xl">
    <!-- Logo -->
    <div class="text-center mb-6">
      <img src="https://website-api.bankdki.co.id/integrations/storage/page-meta-data/007UlZbO3Oe6PivLltdFiQax6QH5kWDvb0cKPdn4.png"
           alt="Bank DKI" class="mx-auto h-14 md:h-16">
    </div>

    <!-- Card Register – palet sama dengan Login -->
    <div class="bg-[#8D2121] backdrop-blur rounded-2xl shadow-lg p-5 md:p-7">
      <h1 class="text-center text-[#F6E4E4] text-base md:text-l font-semibold mb-5">
        Silakan daftarkan akun terlebih dahulu, jika belum memiliki akun.
      </h1>

      <form method="POST" action="/register" class="space-y-4">
        @csrf

        <!-- Username -->
        <div class="bg-[#E9C8C8]/20 rounded-xl border border-[#C89898]/60 focus-within:ring-2 focus-within:ring-[#F6E4E4]/50 transition-all duration-200">
          <div class="flex items-center px-4 py-3 gap-3 text-[#F6E4E4]">
            <!-- user icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 opacity-90 text-[#F6E4E4]" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
            </svg>
            <input
              name="username"
              value="{{ old('username') }}"
              type="text"
              placeholder="Username"
              required
              class="w-full bg-transparent outline-none text-[#F6E4E4] placeholder-[#F6E4E4]/60 focus:placeholder-[#F6E4E4]/40"
              autocomplete="username"
            />
          </div>
        </div>

        <!-- Name -->
        <div class="bg-[#E9C8C8]/20 rounded-xl border border-[#C89898]/60 focus-within:ring-2 focus-within:ring-[#F6E4E4]/50 transition-all duration-200">
          <div class="flex items-center px-4 py-3 gap-3 text-[#F6E4E4]">
            <!-- pencil/person icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 opacity-90 text-[#F6E4E4]" viewBox="0 0 24 24" fill="currentColor">
              <path d="m3 17.25 6.52-1.4L19.44 5.9a1.75 1.75 0 0 0-2.47-2.47L7.05 13.38 5.6 19.9 3 17.25zM20 21H4a1 1 0 1 1 0-2h16a1 1 0 1 1 0 2z"/>
            </svg>
            <input
              name="name"
              value="{{ old('name') }}"
              type="text"
              placeholder="Nama Lengkap"
              required
              class="w-full bg-transparent outline-none text-[#F6E4E4] placeholder-[#F6E4E4]/60 focus:placeholder-[#F6E4E4]/40"
              autocomplete="name"
            />
          </div>
        </div>

        <!-- Role -->
        <div class="bg-[#E9C8C8]/30 rounded-2xl border border-[#F6E4E4]/40 focus-within:ring-2 focus-within:ring-[#F6E4E4]/60 shadow-sm transition-all duration-200">
          <div class="flex items-center px-4 py-3 gap-3 text-[#F6E4E4]">
            <!-- Bag Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 opacity-90 text-[#F6E4E4]" viewBox="0 0 24 24" fill="currentColor">
              <path d="M7 7V6a5 5 0 1 1 10 0v1h2a2 2 0 0 1 2 2v10a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V9a2 2 0 0 1 2-2h2Zm2 0h6V6a3 3 0 0 0-6 0v1Z"/>
            </svg>

            <!-- Select Box -->
            <div class="relative w-full">
              <select
                name="role"
                required
                class="w-full bg-transparent text-[#F6E4E4] placeholder-[#F6E4E4]/70 outline-none appearance-none cursor-pointer px-1 py-1 rounded-xl focus:text-white focus:placeholder-white"
              >
                <option value="" disabled {{ old('role') ? '' : 'selected' }}>
                  Pilih Role
                </option>
                <option value="it" class="bg-[#8D2121] text-[#F6E4E4]" {{ old('role')=='it' ? 'selected' : '' }}>IT</option>
                <option value="digital_banking" class="bg-[#8D2121] text-[#F6E4E4]" {{ old('role')=='digital_banking' ? 'selected' : '' }}>Digital Banking</option>
                <option value="kepala_divisi" class="bg-[#8D2121] text-[#F6E4E4]" {{ old('role')=='kepala_divisi' ? 'selected' : '' }}>Kepala Divisi</option>
              </select>

              <!-- Chevron -->
              <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-1.5 top-1/2 -translate-y-1/2 w-5 h-5 opacity-80 text-[#F6E4E4] pointer-events-none" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8.12 9.29 12 13.17l3.88-3.88 1.41 1.41L12 16l-5.29-5.29 1.41-1.41z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Password -->
        <div class="bg-[#E9C8C8]/20 rounded-xl border border-[#C89898]/60 focus-within:ring-2 focus-within:ring-[#F6E4E4]/50 transition-all duration-200">
          <div class="flex items-center px-4 py-3 gap-3 text-[#F6E4E4]">
            <!-- Ikon Password (kunci modern) -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                 class="w-5 h-5 opacity-90 text-[#F6E4E4]" aria-hidden="true">
              <path d="M17 8V6a5 5 0 0 0-10 0v2H6a2 2 0 0 0-2 2v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V10a2 2 0 0 0-2-2h-1ZM9 6a3 3 0 0 1 6 0v2H9V6Zm3 7a1.5 1.5 0 0 1 1 2.65V18h-2v-2.35A1.5 1.5 0 0 1 12 13Z"/>
            </svg>

            <!-- Input (appearance-none untuk cegah ikon bawaan) -->
            <input
              id="password"
              name="password"
              type="password"
              placeholder="Password"
              required
              autocomplete="new-password"
              class="w-full bg-transparent outline-none appearance-none text-[#F6E4E4] placeholder-[#F6E4E4]/60 focus:placeholder-[#F6E4E4]/40"
            />

            <!-- Tombol Mata (warna disamakan, tidak ada ikon hitam) -->
            <button
              type="button"
              id="togglePwd"
              class="ml-1 p-1 rounded-lg hover:bg-[#E9C8C8]/20 focus:outline-none focus:ring-2 focus:ring-[#F6E4E4]/60"
              aria-label="Tampilkan password"
              aria-pressed="false"
            >
              <!-- Mata Tertutup (default) -->
              <svg id="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                   class="w-5 h-5 text-[#F6E4E4] opacity-90" aria-hidden="true">
                <path d="M3.53 2.47 2.47 3.53 6 7.06C3.9 8.42 2.3 10.33 1.4 12c1.73 3.18 5.4 7 10.6 7 2.02 0 3.83-.54 5.41-1.39l3.06 3.06 1.06-1.06-18-18ZM12 17c-2.76 0-5-2.24-5-5 0-.53.08-1.04.24-1.52l1.6 1.6A3 3 0 0 0 12 15a3 3 0 0 0 2.92-2.28l1.6 1.6A4.98 4.98 0 0 1 12 17Zm9.6-5c-.73-1.34-1.79-2.71-3.16-3.88C17 6.55 14.68 5.5 12 5.5c-.9 0-1.75.12-2.55.34l1.3 1.3c.4-.09.83-.14 1.25-.14 2.76 0 5 2.24 5 5 0 .42-.05.84-.14 1.24l1.86 1.86c1.04-.8 1.93-1.83 2.54-3.1Z"/>
              </svg>

              <!-- Mata Terbuka -->
              <svg id="eyeOnIcon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                   class="w-5 h-5 text-[#F6E4E4] opacity-90 hidden" aria-hidden="true">
                <path d="M12 5c5.2 0 8.87 3.82 10.6 7-1.73 3.18-5.4 7-10.6 7S3.13 15.18 1.4 12C3.13 8.82 6.8 5 12 5Zm0 2.5A4.5 4.5 0 1 0 16.5 12 4.5 4.5 0 0 0 12 7.5Zm0 2A2.5 2.5 0 1 1 9.5 12 2.5 2.5 0 0 1 12 9.5Z"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Tombol -->
        <button
          type="submit"
          class="w-full rounded-2xl border-2 border-[#F6E4E4]/70 bg-[#F6E4E4]/90 hover:bg-[#F6E4E4] text-[#8D2121] font-extrabold py-3 transition duration-200"
        >
          Sign Up
        </button>
      </form>

      <!-- Error handling -->
      @if ($errors->any())
        <div class="mt-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3">
          @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
      @endif
    </div>

    <p class="text-center mt-4 text-sm text-[#8D2121]/90">
      Sudah punya akun?
      <a href="{{ route('login') }}" class="font-bold text-m text-[#8D2121] hover:underline">Login</a>
    </p>
  </div>

  <!-- Script: Toggle Show/Hide Password -->
  <script>
    (function () {
      const input = document.getElementById('password');
      const btn   = document.getElementById('togglePwd');
      const eyeOn = document.getElementById('eyeOnIcon');
      const eyeOff= document.getElementById('eyeOffIcon');

      btn.addEventListener('click', () => {
        const isVisible = input.type === 'text';
        input.type = isVisible ? 'password' : 'text';

        eyeOn.classList.toggle('hidden', isVisible);
        eyeOff.classList.toggle('hidden', !isVisible);

        btn.setAttribute('aria-pressed', String(!isVisible));
        btn.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
      });
    })();
  </script>
</body>
</html>
