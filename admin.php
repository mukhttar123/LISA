<?php
session_start();
include 'config.php';

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - PerBarangan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <style>
        .bg-body {
            background-color: rgba(13, 13, 13, 1);
        }

        .mulish {
            font-family: "Mulish", sans-serif;
        }

        .mulish-800 {
            font-family: "Mulish", sans-serif;
            font-weight: 700;
        }

        .bg-glass {
            background: rgba(255, 255, 255, 0.24);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<div class="min-h-screen bg-body text-white flex flex-col items-center p-8">
    <header class="flex justify-between items-center w-full max-w-6xl mb-8">
        <h1 class="text-4xl mulish font-bold">Lisa</h1>
        <div>
            <ul class="text-2xl mulish flex gap-5">
                <li><a href="">History</a></li>
                <li><a href="">Manage</a></li>
                <li><a href="">Stock</a></li>
                <li>
                    <h1 class="text-black bg-white rounded-xl mulish-800 px-2">
                        <?= htmlspecialchars($_SESSION['username']) ?></h1>
                </li>
            </ul>
        </div>
    </header>

    <?php
// Memeriksa apakah session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Memulai session jika belum dimulai
}

if (isset($_GET['message'])) {
    $_SESSION['message'] = htmlspecialchars($_GET['message']);
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    echo "
    <div id='message' class='bg-green-500 text-white p-4 rounded mb-4 relative'>
        <span>$message</span>
        <span id='close' class='absolute top-0 right-0 p-2 cursor-pointer'>&times;</span>
    </div>
    <script>
        var messageDiv = document.getElementById('message');
        var closeButton = document.getElementById('close');

        closeButton.onclick = function() {
            messageDiv.style.display = 'none';
            // Hapus pesan dari session
            window.location.href = window.location.pathname; // Refresh halaman untuk menghapus pesan
        };
    </script>
    ";
    unset($_SESSION['message']); // Hapus pesan dari session setelah ditampilkan
}
?>

    <div class="w-full max-w-6xl my-4">
        <div class="flex justify-between mb-3">
            <h2 class="text-3xl mulish font-bold items-center justify-center">Daftar Barang</h2>
            <ul class="flex gap-4 justify-center items-center">
                <li>
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-full mulish hover:bg-blue-600 transition"
                        onclick="toggleModal()">Tambah Barang Baru</button>
                </li>
                <li>
                    <button class="px-4 py-2 bg-green-500 text-white rounded-full mulish hover:bg-green-600 transition"
                        onclick="toggleStockModal()">Add</button>
                </li>
                <li>
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-full mulish hover:bg-blue-600 transition"
                        onclick="togglePengambilanModal()">Take</button>
                </li>
                <li><a href="history.php"
                        class="px-4 py-2 bg-pink-500 text-white rounded-full mulish hover:bg-pink-600 transition">hist0ry</a>
                </li>
            </ul>
        </div>
        <table class="table-auto w-full bg-glass rounded-lg overflow-hidden">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Nama Barang</th>
                    <th class="px-4 py-2">Stok</th>
                    <th class="px-4 py-2">Satuan</th>
                    <th class="px-4 py-2">Aksi</th> <!-- Kolom untuk tombol Edit -->
                </tr>
            </thead>
            <tbody>
            <?php
                $query = "SELECT id, nama_barang, stok, satuan, created_at, status FROM barang ORDER BY stok ASC"; // Pastikan untuk mengambil ID
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='odd:bg-gray-700 even:bg-gray-600'>";
                        echo "<td class='px-4 py-2 text-center'>" . $no++ . "</td>"; // Memusatkan nomor
                        echo "<td class='px-4 py-2 text-center'>" . htmlspecialchars($row['nama_barang']) . "</td>"; // Memusatkan nama barang
                        echo "<td class='px-4 py-2 text-center'>" . htmlspecialchars($row['stok']) . "</td>"; // Memusatkan stok
                        echo "<td class='px-4 py-2 text-center'>" . htmlspecialchars($row['satuan']) . "</td>"; // Memusatkan satuan
                        echo "<td class='px-4 py-2 text-center'>
                            <button onclick='openEditModal(\"" . htmlspecialchars($row['nama_barang']) . "\", \"" . htmlspecialchars($row['stok']) . "\", \"" . htmlspecialchars($row['satuan']) . "\", \"" . htmlspecialchars($row['id']) . "\")' 
                                    class='px-4 py-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition'>
                                Edit
                            </button>
                            <button onclick='confirmDelete(\"" . htmlspecialchars($row['nama_barang']) . "\")' 
                                    class='px-4 py-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition'>
                                Delete
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center py-4'>Tidak ada data barang.</td></tr>"; // Memusatkan pesan tidak ada data
                }
                ?>
            </tbody>
        </table>
    </div>

            <!-- Modal Tambah Barang -->
        <div id="modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center hidden">
            <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
                <h2 class="text-2xl mb-4 mulish font-bold text-gray-800">Tambah Barang Baru</h2>
                <form method="POST" action="process_input_barang.php" class="text-black">
                    <div class="mb-4">
                        <label for="nama_barang" class="block text-gray-700">Nama Barang:</label>
                        <input type="text" id="nama_barang" name="nama_barang"
                            class="w-full border-gray-300 rounded-md focus:ring focus:ring-blue-200 p-2" required>
                    </div>
                    <div class="mb-4">
                        <label for="satuan" class="block text-gray-700">Satuan:</label>
                        <input type="text" id="satuan" name="satuan"
                            class="w-full border-gray-300 rounded-md focus:ring focus:ring-blue-200 p-2" required>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-full mr-2"
                            onclick="toggleModal()">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-full">Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>

    <!-- Modal Edit Barang -->
    <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl mb-4 mulish font-bold text-gray-800">Edit Barang</h2>
            <form method="POST" action="process_edit_barang.php" class="text-black">
                <input type="hidden" id="edit_id_barang" name="id_barang">
                <div class="mb-4">
                    <label for="edit_nama_barang" class="block text-gray-700">Nama Barang:</label>
                    <input type="text" id="edit_nama_barang" name="nama_barang"
                        class="w-full border-gray-300 rounded-md focus:ring focus:ring-blue-200 p-2" required>
                </div>
                <div class="mb-4">
                    <label for="edit_stok" class="block text-gray-700">Stok:</label>
                    <input type="number" id="edit_stok" name="stok"
                        class="w-full border-gray-300 rounded-md focus:ring focus:ring-blue-200 p-2" required>
                </div>
                <div class="mb-4">
                    <label for="edit_satuan" class="block text-gray-700">Satuan:</label>
                    <input type="text" id="edit_satuan" name="satuan"
                        class="w-full border-gray-300 rounded-md focus:ring focus:ring-blue-200 p-2" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-full mr-2"
                        onclick="toggleEditModal()">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-full">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal Tambah Stok -->
    <div id="stockModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl mb-4 mulish font-bold text-gray-800">Tambah Stok Barang</h2>
            <form method="POST" action="process_tambah_stok.php" class="text-black">
                <div class="mb-4">
                    <label for="nama_barang" class="block text-gray-700">Nama Barang:</label>
                    <input type="text" id="nama_barang" name="nama_barang"
                        class="w-full border-gray-300 rounded-md focus:ring focus:ring-blue-200 p-2" required>
                </div>
                <div class="mb-4">
                    <label for="stok_tambah" class="block text-gray-700">Jumlah Stok Ditambahkan:</label>
                    <input type="number" id="stok_tambah" name="stok_tambah"
                        class="w-full border-gray-300 rounded-md focus:ring focus:ring-blue-200 p-2" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-full mr-2"
                        onclick="toggleStockModal()">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-full">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal Pengambilan Barang -->
    <div id="pengambilanModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl mb-4 mulish font-bold text-gray-800">Ambil Barang</h2>
            <form method="POST" action="process_pengambilan.php" class="text-black">
                <div class="mb-4">
                    <label for="nama_barang_pengambilan" class="block text-gray-700">Nama Barang:</label>
                    <input type="text" id="nama_barang_pengambilan" name="nama_barang"
                        class="w-full border-gray-300 rounded-md focus:ring focus:ring-blue-200 p-2" required>
                </div>
                <div class="mb-4">
                    <label for="jumlah_pengambilan" class="block text-gray-700">Jumlah:</label>
                    <input type="number" id="jumlah_pengambilan" name="jumlah"
                        class="w-full border-gray-300 rounded-md focus:ring focus:ring-blue-200 p-2" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-full mr-2"
                        onclick="togglePengambilanModal()">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-full">Ambil</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function toggleModal() {
        const modal = document.getElementById('modal');
        modal.classList.toggle('hidden');
    }

    function toggleEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.toggle('hidden');
    }

    function openEditModal(nama, stok, satuan, id_barang) {
        document.getElementById('edit_nama_barang').value = nama;
        document.getElementById('edit_stok').value = stok;
        document.getElementById('edit_satuan').value = satuan;
        document.getElementById('edit_id_barang').value = id_barang; // Menambahkan ID barang
        toggleEditModal();
    }

    //tambah stok
    function toggleStockModal() {
        console.log('toggleStockModal dipanggil'); // Debug log
        const stockModal = document.getElementById('stockModal');
        stockModal.classList.toggle('hidden');
    }

    // Tutup modal saat klik di luar modal 
    window.addEventListener('click', (event) => {
        const stockModal = document.getElementById('stockModal');
        if (stockModal && event.target === stockModal) {
            toggleStockModal();
        }
    });

    function togglePengambilanModal() {
        const pengambilanModal = document.getElementById('pengambilanModal');
        pengambilanModal.classList.toggle('hidden');
    }

    // Tutup modal saat klik di luar modal
    window.addEventListener('click', (event) => {
        const pengambilanModal = document.getElementById('pengambilanModal');
        if (pengambilanModal && event.target === pengambilanModal) {
            togglePengambilanModal();
        }
    });

    function confirmDelete(nama_barang) {
        if (confirm("Apakah Anda yakin ingin menghapus barang '" + nama_barang + "'?")) {
            window.location.href = "process_delete_barang.php?nama_barang=" + encodeURIComponent(nama_barang);
        }
    }

</script>

</body>

</html>