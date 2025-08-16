<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pengukuran</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 w-full bg-white shadow-md px-4 py-3 flex justify-between items-center z-50">
        <!-- Judul -->
        <h1 class="text-lg font-bold" style="color: #FF9B00">Input Pengukuran</h1>


        <!-- Logout -->
        @auth
            <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                @csrf
                <button type="submit"
                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                    Logout
                </button>
            </form>
        @endauth
    </nav>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search_input');
            const rows = document.querySelectorAll('.search-row');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                rows.forEach(row => {
                    const no_reg = row.children[0].textContent.toLowerCase();
                    const nama = row.children[1].textContent.toLowerCase();

                    if (no_reg.includes(searchTerm) || nama.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        function showInputForm(id, nama) {
            const modal = document.getElementById('inputModal');
            const form = document.getElementById('measurementForm');
            const title = document.getElementById('modalTitle');

            title.textContent = `Input Pengukuran - ${nama}`;
            form.action = `/formDewasa/${id}/isi-bb`;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('inputModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('inputModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>

    <!-- Success Alert -->
    @if (session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
    @endif

</body>

</html>