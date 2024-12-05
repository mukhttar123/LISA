<?php
session_start();
include 'config.php';

// Cek apakah user sudah login
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
</head>

<body class="bg-body text-white min-h-screen flex flex-col items-center p-8">
    <header class="w-full max-w-6xl flex justify-between items-center mb-6">
        <h1 class="text-3xl mulish font-bold">Daftar Barang</h1>
        <div>
            <a href="input_buku.php" class="px-4 py-2 bg-green-500 text-white rounded-full mulish hover:bg-green-600 transition">Tambah Barang</a>
            <a href="admin.php" class="px-4 py-2 bg-blue-500 text-white rounded-full mulish hover:bg-blue-600 transition">Kembali</a>
            <a href="logout.php" class="px-4 py-2 bg-red-500 text-white rounded-full mulish hover:bg-red-600 transition">Logout</a>
        </div>
    </header>

    <div class="w-full max-w-6xl">
        <table class="table-auto w-full bg-glass rounded-lg overflow-hidden">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Kode Barang</th>
                    <th class="px-4 py-2">Judul Barang</th>
                    <th class="px-4 py-2">Stok</th>
                    <th class="px-4 py-2">Satuan</th>
                    <th class="px-4 py-2">Waktu Ditambahkan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query untuk mengambil data barang
                $query = "SELECT kode_barang, nama_barang, stok, satuan, created_at FROM barang ORDER BY created_at DESC";
                $result = $conn->query($query);
                
                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='odd:bg-gray-700 even:bg-gray-600'>";
                        echo "<td class='px-4 py-2'>" . $no++ . "</td>";
                        echo "<td class='px-4 py-2'>" . htmlspecialchars($row['kode_barang']) . "</td>";
                        echo "<td class='px-4 py-2'>" . htmlspecialchars($row['nama_barang']) . "</td>";
                        echo "<td class='px-4 py-2'>" . htmlspecialchars($row['stok']) . "</td>";
                        echo "<td class='px-4 py-2'>" . htmlspecialchars($row['satuan']) . "</td>";
                        echo "<td class='px-4 py-2'>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center py-4'>Tidak ada data barang.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <style>
        .bg-body {
            background-color: rgba(13, 13, 13, 1);
        }

        .mulish {
            font-family: "Mulish", sans-serif;
        }

        .bg-glass {
            background: rgba(255, 255, 255, 0.24);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</body>

</html>
