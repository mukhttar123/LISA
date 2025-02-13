<?php
session_start();
include 'config.php';

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

function timeAgo($datetime, $full = false) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Menghitung jumlah minggu dari jumlah hari
    $weeks = floor($diff->d / 7);
    $days = $diff->d % 7; // Sisa hari setelah minggu

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    // Menghitung string waktu
    $timeStrings = array();
    if ($diff->y) {
        $timeStrings[] = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');}
    if ($diff->m) {
        $timeStrings[] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');}
    if ($weeks) {
        $timeStrings[] = $weeks . ' week' . ($weeks > 1 ? 's' : '');}
    if ($days) {
        $timeStrings[] = $days . ' day' . ($days > 1 ? 's' : '');}
    if ($diff->h) {
        $timeStrings[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');}
    if ($diff->i) {
        $timeStrings[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');}
    if ($diff->s) {
        $timeStrings[] = $diff->s . ' second' . ($diff->s > 1 ? 's' : '');}
    if (!$full) {
        $timeStrings = array_slice($timeStrings, 0, 1);}

    return $timeStrings ? implode(', ', $timeStrings) . ' ago' : 'just now';
}

// Inisialisasi variabel untuk bulan, tahun, dan nama barang
$selected_month = isset($_POST['month']) ? $_POST['month'] : date('m');
$selected_year = isset($_POST['year']) ? $_POST['year'] : date('Y');
$search_name = isset($_POST['search_name']) ? $_POST['search_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang - PerBarangan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .bg-body {
            background-color: rgba(13, 13, 13, 1);
        }
        .text-white {
            color: white;
        }
    </style>
</head>
    <body class="bg-body text-white">
        <div class="min-h-screen p-8">
            <header class="mb-8">
                <h1 class="text-4xl font-bold">Laporan Barang</h1>
            </header>

            <!-- Tombol Kembali -->
            <div class="mb-4">
                <a href="admin.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Kembali ke Admin
                </a>
            </div>

            <!-- Form Pencarian -->
            <form method="POST" class="mb-8">
                <div class="flex space-x-4">
                    <select name="month" class="bg-gray-800 text-white rounded px-4 py-2">
                        <?php
                        for ($m = 1; $m <= 12; $m++) {
                            $selected = ($m == $selected_month) ? 'selected' : '';
                            echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
                        }
                        ?>
                    </select>
                    <select name="year" class="bg-gray-800 text-white rounded px-4 py-2">
                        <?php
                        for ($y = date('Y'); $y >= 2000; $y--) {
                            $selected = ($y == $selected_year) ? 'selected' : '';
                            echo "<option value='$y' $selected>$y</option>";
                        }
                        ?>
                    </select>
                    <input type="text" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>" placeholder="Nama Barang" class="bg-gray-800 text-white rounded px-4 py-2">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Cari
                    </button>
                </div>
            </form>

            <h2 class="text-3xl mb-4">Barang Masuk</h2>
            <table class="table-auto w-full bg-gray-800 rounded-lg overflow-hidden mb-8">
                <thead>
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Nama Barang</th>
                        <th class="px-4 py-2">Stok</th>
                        <th class="px-4 py-2">Satuan</th>
                        <th class="px-4 py-2">Waktu Ditambahkan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Mengambil data barang masuk dari tabel history_barang berdasarkan bulan, tahun, dan nama barang
                        $query_masuk = "SELECT b.nama_barang, h.stok, h.satuan, h.created_at 
                                        FROM history_barang h 
                                        JOIN barang b ON h.id_barang = b.id 
                                        WHERE h.status = 'masuk' 
                                        AND MONTH(h.created_at) = $selected_month 
                                        AND YEAR(h.created_at) = $selected_year 
                                        AND b.nama_barang LIKE '%$search_name%' 
                                        ORDER BY h.created_at DESC";                
                        $result_masuk = $conn->query($query_masuk);
                        if ($result_masuk->num_rows > 0) {
                            $no = 1;
                            while ($row = $result_masuk->fetch_assoc()) {
                                echo "<tr class='odd:bg-gray-700 even:bg-gray-600'>";
                                echo "<td class='px-4 py-2'>" . $no++ . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['nama_barang']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['stok']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['satuan']) . "</td>";
                                echo "<td class='px-4 py-2'>" . timeAgo(htmlspecialchars($row['created_at'])) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-4'>Tidak ada data barang masuk.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>

            <h2 class="text-3xl mb-4">Barang Keluar</h2>
            <table class="table-auto w-full bg-gray-800 rounded-lg overflow-hidden">
                <thead>
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Nama Barang</th>
                        <th class="px-4 py-2">Jumlah Diambil</th>
                        <th class="px-4 py-2">Tanggal Ambil</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Mengambil data barang keluar dari tabel history_barang berdasarkan bulan, tahun, dan nama barang
                        $query_keluar = "SELECT b.nama_barang, h.stok, p.tgl_ambil 
                                        FROM pengambilan p 
                                        JOIN barang b ON p.id_barang = b.id 
                                        JOIN history_barang h ON h.id_barang = b.id 
                                        WHERE h.status = 'keluar' 
                                        AND MONTH(p.tgl_ambil) = $selected_month 
                                        AND YEAR(p.tgl_ambil) = $selected_year 
                                        AND b.nama_barang LIKE '%$search_name%' 
                                        ORDER BY p.tgl_ambil DESC";                
                        $result_keluar = $conn->query($query_keluar);
                        if ($result_keluar->num_rows > 0) {
                            $no = 1;
                            while ($row = $result_keluar->fetch_assoc()) {
                                echo "<tr class='odd:bg-gray-700 even:bg-gray-600'>";
                                echo "<td class ='px-4 py-2'>" . $no++ . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['nama_barang']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['stok']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['tgl_ambil']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center py-4'>Tidak ada data barang keluar.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
            

            <!-- Tabel Barang yang Dihapus -->
            <h2 class="text-3xl mb-4">Barang Dihapus</h2>
            <table class="table-auto w-full bg-gray-800 rounded-lg overflow-hidden">
                <thead>
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Nama Barang</th>
                        <th class="px-4 py-2">Stok</th>
                        <th class="px-4 py-2">Satuan</th>
                        <th class="px-4 py-2">Waktu Dihapus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_hapus = "SELECT b.nama_barang, h.stok, h.satuan, h.deleted_at 
                            FROM history_barang h 
                            JOIN barang b ON h.id_barang = b.id 
                            WHERE h.status = 'hapus' 
                            ORDER BY h.deleted_at DESC";                
                    $result_hapus = $conn->query($query_hapus);
                    if ($result_hapus->num_rows > 0) {
                        $no = 1;
                        while ($row = $result_hapus->fetch_assoc()) {
                            echo "<tr class='odd:bg-gray-700 even:bg-gray-600'>";
                            echo "<td class='px-4 py-2'>" . $no++ . "</td>";
                            echo "<td class='px-4 py-2'>" . htmlspecialchars($row['nama_barang']) . "</td>";
                            echo "<td class='px-4 py-2'>" . htmlspecialchars($row['stok']) . "</td>";
                            echo "<td class='px-4 py-2'>" . htmlspecialchars($row['satuan']) . "</td>";
                            echo "<td class='px-4 py-2'>" . timeAgo(htmlspecialchars($row['deleted_at'])) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4'>Tidak ada data barang yang dihapus.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>