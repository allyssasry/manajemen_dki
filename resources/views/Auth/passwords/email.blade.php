<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Lupa Password • Bank Jakarta</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    html {
      scrollbar-gutter: stable;
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

    <!-- Card Lupa Password - palet sama dengan Login & Register -->
    <div class="bg-[#8D2121] backdrop-blur rounded-2xl shadow-lg p-5 md:p-7">
      <h1 class="text-center text-[#F6E4E4] text-base md:text-lg font-semibold mb-2">
        Lupa Kata Sandi?
      </h1>
      <p class="text-center text-[#F6E4E4]/80 text-xs md:text-sm mb-5">
        Masukkan email terdaftar Anda dan kami akan mengirimkan link untuk mereset kata sandi.
      </p>

      <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email -->
        <div class="bg-[#E9C8C8]/20 rounded-xl border border-[#C89898]/60 focus-within:ring-2 focus-within:ring-[#F6E4E4]/50 transition-all duration-200 @error('email') ring-2 ring-red-400 @enderror">
          <div class="flex items-center px-4 py-3 gap-3 text-[#F6E4E4]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 opacity-90 text-[#F6E4E4]" viewBox="0 0 24 24" fill="currentColor">
              <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            <input
              name="email"
              type="email"
              placeholder="Masukkan Email Anda"
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

        <!-- Tombol Send Reset Link -->
        <button
          type="submit"
          class="w-full rounded-2xl border-2 border-[#F6E4E4]/50 bg-white hover:bg-[#F6E4E4] text-[#8D2121] font-extrabold py-3 transition duration-200"
        >
          Kirim Link Reset
        </button>
      </form>

      <!-- Success Message -->
      @if (session('success'))
        <div class="mt-4 text-sm text-green-200 bg-green-600/20 border border-green-400/30 rounded-lg p-3 space-y-3">
          <div>{{ session('success') }}</div>
          @if (session('reset_url'))
            <div class="pt-2 border-t border-green-400/30">
              <p class="text-xs mb-2">📎 Link Reset Password:</p>
              <a href="{{ session('reset_url') }}" 
                 class="inline-block w-full text-center bg-green-600/40 hover:bg-green-600/60 text-green-100 px-3 py-2 rounded-lg font-semibold transition duration-200 break-words text-xs">
                {{ session('reset_url') }}
              </a>
            </div>
          @endif
        </div>
      @endif

      <!-- Error Messages -->
      @if ($errors->any() && !$errors->has('email'))
        <div class="mt-4 text-sm text-red-300 bg-red-500/20 border border-red-400/30 rounded-lg p-3">
          @foreach ($errors->all() as $e)
            <div>{{ $e }}</div>
          @endforeach
        </div>
      @endif
    </div>

    <!-- Link Kembali ke Login -->
    <p class="text-center mt-4 text-sm text-[#8D2121]/90">
      Ingat kata sandi Anda?
      <a href="{{ route('login') }}" class="font-bold text-m text-[#8D2121] hover:underline">Kembali ke Login</a>
    </p>

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-[#FFF5F5] border border-[#C89898]/40 rounded-xl">
      <p class="text-xs text-[#7A1C1C]/80 leading-relaxed">
        <strong>Catatan:</strong> Saat ini dalam mode development, link reset password akan ditampilkan di halaman ini. Pada production, link akan dikirim melalui email. Jika ada pertanyaan atau masalah, hubungi administrator.
      </p>
    </div>
  </div>

</body>
</html>
