<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login • Bank Jakarta</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* --- Hilangkan ikon mata hitam bawaan browser --- */
    /* Edge / IE */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
      display: none !important;
      width: 0 !important;
      height: 0 !important;
    }
    /* WebKit (Safari/Chrome varian) tombol kredensial auto-fill */
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

    <!-- Card -->
    <div class="bg-[#8D2121] backdrop-blur rounded-2xl shadow-lg p-5 md:p-7">
      <h1 class="text-center text-[#F6E4E4] text-base md:text-lg font-semibold mb-5">
        Selamat Datang! Silakan Masuk.
      </h1>

      <form method="POST" action="/login" class="space-y-4">
        @csrf

        <!-- Username -->
        <div class="bg-[#E9C8C8]/20 rounded-xl border border-[#C89898]/60 focus-within:ring-2 focus-within:ring-[#F6E4E4]/50 transition-all duration-200">
          <div class="flex items-center px-4 py-3 gap-3 text-[#F6E4E4]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 opacity-90 text-[#F6E4E4]" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
            </svg>
            <input
              name="username"
              type="text"
              placeholder="Masukkan Username"
              required
              class="w-full bg-transparent outline-none text-[#F6E4E4] placeholder-[#F6E4E4]/60 focus:placeholder-[#F6E4E4]/40"
              autocomplete="username"
            />
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

            <!-- Input Password (appearance-none mencegah ikon bawaan) -->
            <input
              id="password"
              name="password"
              type="password"
              placeholder="Password"
              required
              autocomplete="current-password"
              class="w-full bg-transparent outline-none appearance-none text-[#F6E4E4] placeholder-[#F6E4E4]/60 focus:placeholder-[#F6E4E4]/40"
            />

            <!-- Tombol Mata (ikon milik kita, warna senada) -->
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

        <!-- Link daftar -->
        <p class="text-right text-sm text-[#F6E4E4]/90">
          Jika belum memiliki akun,
          <a href="{{ route('Auth.register') }}" class="font-semibold text-[#FBDCDC] hover:underline">Daftar Sekarang!</a>
        </p>

        <!-- Tombol Login -->
        <button
          type="submit"
          class="w-full rounded-2xl border-2 border-[#F6E4E4]/50 bg-white hover:bg-[#F6E4E4] text-[#8D2121] font-extrabold py-3 transition duration-200"
        >
          Login
        </button>
      </form>

      <!-- Error messages -->
      @if ($errors->any())
        <div class="mt-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3">
          @foreach ($errors->all() as $e)
            <div>{{ $e }}</div>
          @endforeach
        </div>
      @endif

      @if (session('error'))
        <div class="mt-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3">
          {{ session('error') }}
        </div>
      @endif
    </div>
  </div>

  <!-- Toggle Show/Hide Password -->
  <script>
    (function () {
      const input = document.getElementById('password');
      const btn   = document.getElementById('togglePwd');
      const eyeOn = document.getElementById('eyeOnIcon');
      const eyeOff= document.getElementById('eyeOffIcon');

      btn?.addEventListener('click', () => {
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
