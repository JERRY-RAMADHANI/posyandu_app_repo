<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Pendaftaran</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen p-4 pt-20"> <!-- pt-20 = biar ga ketiban navbar -->

  <!-- Navbar dengan Logout -->
  <nav class="fixed top-0 left-0 w-full bg-white shadow-md px-4 py-3 flex justify-between items-center z-50">
    <h1 class="text-lg font-bold text-gray-800">Form Pendaftaran</h1>

    @auth
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button
        type="submit"
        class="text-sm bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition"
      >
        Logout
      </button>
    </form>
    @endauth
  </nav>

  <!-- Form Pendaftaran -->
  <div class="w-full max-w-md mx-auto bg-white rounded-lg shadow-md p-6 mt-6">
    <h2 class="text-2xl font-bold mb-6 text-center">Form Pendaftaran</h2>

    <form class="space-y-5">
      <!-- Nama -->
      <div>
        <label for="name" class="block mb-2 text-sm font-medium text-gray-700">Nama</label>
        <input
          type="text"
          id="name"
          name="name"
          required
          class="form-input w-full rounded-lg border border-gray-300 p-2.5"
          placeholder="Nama lengkap"
        >
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          required
          class="form-input w-full rounded-lg border border-gray-300 p-2.5"
          placeholder="email@example.com"
        >
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          required
          class="form-input w-full rounded-lg border border-gray-300 p-2.5"
          placeholder="••••••••"
        >
      </div>

      <!-- Checkbox -->
      <div class="flex items-center">
        <input
          id="terms"
          type="checkbox"
          required
          class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded"
        >
        <label for="terms" class="ml-2 text-sm text-gray-700">
          Saya setuju dengan <a href="#" class="text-blue-600 hover:underline">syarat & ketentuan</a>
        </label>
      </div>

      <!-- Tombol Submit -->
      <button
        type="submit"
        class="w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5"
      >
        Daftar
      </button>
    </form>
  </div>

</body>
</html>

