<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Reset Password • Bank Jakarta</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    html {
      scrollbar-gutter: stable;
    }
    /* Sembunyikan tombol reveal bawaan Edge/Chromium */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
      display: none !important;
      width: 0;
      height: 0;
    }
    /* Safari/WebKit kredensial button */
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

    <!-- Card Reset Password - palet sama -->
    <div class="bg-[#8D2121] backdrop-blur rounded-2xl shadow-lg p-5 md:p-7">
      <h1 class="text-center text-[#F6E4E4] text-base md:text-lg font-semibold mb-2">
        Reset Kata Sandi
      </h1>
      <p class="text-center text-[#F6E4E4]/80 text-xs md:text-sm mb-5">
        Masukkan email dan kata sandi baru Anda untuk mereset akun.
      </p>

      <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email -->
        <div class="bg-[#E9C8C8]/20 rounded-xl border border-[#C89898]/60 focus-within:ring-2 focus-within:ring-[#F6E4E4]/50 transition-all duration-200">
          <div class="flex items-center px-4 py-3 gap-3 text-[#F6E4E4]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 opacity-90 text-[#F6E4E4]" viewBox="0 0 24 24" fill="currentColor">
              <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            <input
              name="email"
              type="email"
              placeholder="Email Anda"
              required
              class="w-full bg-transparent outline-none text-[#F6E4E4] placeholder-[#F6E4E4]/60 focus:placeholder-[#F6E4E4]/40"
              value="{{ old('email') }}"
              autocomplete="email"
            />
          </div>
        </div>

        @error('email')
          <p class="text-xs text-red-300 bg-red-500/20 border border-red-400/30 rounded-lg p-2">{{ $message }}</p>
        @enderror

        <!-- Kata Sandi Baru -->
        <div class="bg-[#E9C8C8]/20 rounded-xl border border-[#C89898]/60 focus-within:ring-2 focus-within:ring-[#F6E4E4]/50 transition-all duration-200">
          <div class="flex items-center px-4 py-3 gap-3 text-[#F6E4E4]">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                 class="w-5 h-5 opacity-90 text-[#F6E4E4]" aria-hidden="true">
              <path d="M17 8V6a5 5 0 0 0-10 0v2H6a2 2 0 0 0-2 2v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V10a2 2 0 0 0-2-2h-1ZM9 6a3 3 0 0 1 6 0v2H9V6Zm3 7a1.5 1.5 0 0 1 1 2.65V18h-2v-2.35A1.5 1.5 0 0 1 12 13Z"/>
            </svg>

            <input
              id="password"
              name="password"
              type="password"
              placeholder="Kata Sandi Baru (minimal 8 karakter)"
              required
              autocomplete="new-password"
              class="w-full bg-transparent outline-none appearance-none text-[#F6E4E4] placeholder-[#F6E4E4]/60 focus:placeholder-[#F6E4E4]/40"
            />

            <!-- Tombol Mata -->
            <button
              type="button"
              id="togglePwd"
              class="ml-1 p-1 rounded-lg hover:bg-[#E9C8C8]/20 focus:outline-none focus:ring-2 focus:ring-[#F6E4E4]/60"
              aria-label="Tampilkan password"
              aria-pressed="false"
            >
              <!-- Mata Tertutup -->
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

        @error('password')
          <p class="text-xs text-red-300 bg-red-500/20 border border-red-400/30 rounded-lg p-2">{{ $message }}</p>
        @enderror

        <!-- Konfirmasi Kata Sandi Baru -->
        <div class="bg-[#E9C8C8]/20 rounded-xl border border-[#C89898]/60 focus-within:ring-2 focus-within:ring-[#F6E4E4]/50 transition-all duration-200">
          <div class="flex items-center px-4 py-3 gap-3 text-[#F6E4E4]">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                 class="w-5 h-5 opacity-90 text-[#F6E4E4]" aria-hidden="true">
              <path d="M17 8V6a5 5 0 0 0-10 0v2H6a2 2 0 0 0-2 2v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V10a2 2 0 0 0-2-2h-1ZM9 6a3 3 0 0 1 6 0v2H9V6Zm3 7a1.5 1.5 0 0 1 1 2.65V18h-2v-2.35A1.5 1.5 0 0 1 12 13Z"/>
            </svg>

            <input
              id="password_confirmation"
              name="password_confirmation"
              type="password"
              placeholder="Konfirmasi Kata Sandi Baru"
              required
              autocomplete="new-password"
              class="w-full bg-transparent outline-none appearance-none text-[#F6E4E4] placeholder-[#F6E4E4]/60 focus:placeholder-[#F6E4E4]/40"
            />

            <!-- Tombol Mata -->
            <button
              type="button"
              id="togglePwdConfirm"
              class="ml-1 p-1 rounded-lg hover:bg-[#E9C8C8]/20 focus:outline-none focus:ring-2 focus:ring-[#F6E4E4]/60"
              aria-label="Tampilkan password"
              aria-pressed="false"
            >
              <!-- Mata Tertutup -->
              <svg id="eyeOffIconConfirm" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                   class="w-5 h-5 text-[#F6E4E4] opacity-90" aria-hidden="true">
                <path d="M3.53 2.47 2.47 3.53 6 7.06C3.9 8.42 2.3 10.33 1.4 12c1.73 3.18 5.4 7 10.6 7 2.02 0 3.83-.54 5.41-1.39l3.06 3.06 1.06-1.06-18-18ZM12 17c-2.76 0-5-2.24-5-5 0-.53.08-1.04.24-1.52l1.6 1.6A3 3 0 0 0 12 15a3 3 0 0 0 2.92-2.28l1.6 1.6A4.98 4.98 0 0 1 12 17Zm9.6-5c-.73-1.34-1.79-2.71-3.16-3.88C17 6.55 14.68 5.5 12 5.5c-.9 0-1.75.12-2.55.34l1.3 1.3c.4-.09.83-.14 1.25-.14 2.76 0 5 2.24 5 5 0 .42-.05.84-.14 1.24l1.86 1.86c1.04-.8 1.93-1.83 2.54-3.1Z"/>
              </svg>

              <!-- Mata Terbuka -->
              <svg id="eyeOnIconConfirm" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                   class="w-5 h-5 text-[#F6E4E4] opacity-90 hidden" aria-hidden="true">
                <path d="M12 5c5.2 0 8.87 3.82 10.6 7-1.73 3.18-5.4 7-10.6 7S3.13 15.18 1.4 12C3.13 8.82 6.8 5 12 5Zm0 2.5A4.5 4.5 0 1 0 16.5 12 4.5 4.5 0 0 0 12 7.5Zm0 2A2.5 2.5 0 1 1 9.5 12 2.5 2.5 0 0 1 12 9.5Z"/>
              </svg>
            </button>
          </div>
        </div>

        @error('password_confirmation')
          <p class="text-xs text-red-300 bg-red-500/20 border border-red-400/30 rounded-lg p-2">{{ $message }}</p>
        @enderror

        <!-- Tombol Reset -->
        <button
          type="submit"
          class="w-full rounded-2xl border-2 border-[#F6E4E4]/50 bg-white hover:bg-[#F6E4E4] text-[#8D2121] font-extrabold py-3 transition duration-200"
        >
          Reset Kata Sandi
        </button>
      </form>

      <!-- Error Messages -->
      @if ($errors->any())
        <div class="mt-4 text-sm text-red-300 bg-red-500/20 border border-red-400/30 rounded-lg p-3">
          @foreach ($errors->all() as $e)
            @if (!in_array($errors->first(), [$e]))
              <div>{{ $e }}</div>
            @endif
          @endforeach
        </div>
      @endif
    </div>

    <!-- Link Kembali ke Login -->
    <p class="text-center mt-4 text-sm text-[#8D2121]/90">
      <a href="{{ route('login') }}" class="font-bold text-m text-[#8D2121] hover:underline">← Kembali ke Login</a>
    </p>
  </div>

  <!-- Toggle Show/Hide Password -->
  <script>
    (function () {
      // Password baru
      const pwd1 = document.getElementById('password');
      const btn1  = document.getElementById('togglePwd');
      const eyeOn1 = document.getElementById('eyeOnIcon');
      const eyeOff1 = document.getElementById('eyeOffIcon');

      btn1?.addEventListener('click', () => {
        const isVisible = pwd1.type === 'text';
        pwd1.type = isVisible ? 'password' : 'text';
        eyeOn1.classList.toggle('hidden', isVisible);
        eyeOff1.classList.toggle('hidden', !isVisible);
        btn1.setAttribute('aria-pressed', String(!isVisible));
      });

      // Konfirmasi password
      const pwd2 = document.getElementById('password_confirmation');
      const btn2  = document.getElementById('togglePwdConfirm');
      const eyeOn2 = document.getElementById('eyeOnIconConfirm');
      const eyeOff2 = document.getElementById('eyeOffIconConfirm');

      btn2?.addEventListener('click', () => {
        const isVisible = pwd2.type === 'text';
        pwd2.type = isVisible ? 'password' : 'text';
        eyeOn2.classList.toggle('hidden', isVisible);
        eyeOff2.classList.toggle('hidden', !isVisible);
        btn2.setAttribute('aria-pressed', String(!isVisible));
      });
    })();
  </script>

</body>
</html>
