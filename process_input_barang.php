<?php
session_start();
include 'config.php';

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $satuan = $_POST['satuan'];

    // Menambahkan barang ke tabel barang dengan status 'masuk' dan stok diatur ke 0
    $query_barang = "INSERT INTO barang (nama_barang, stok, satuan_id, status) VALUES (?, 0, ?, 'masuk')";
    $stmt_barang = $conn->prepare($query_barang);
    $stmt_barang->bind_param("ss", $nama_barang, $satuan);
    $stmt_barang->execute();

    // Mendapatkan ID barang yang baru ditambahkan
    $id_barang = $stmt_barang->insert_id;

    // Menambahkan riwayat ke tabel history_barang
    // Jika Anda ingin menyimpan riwayat tanpa stok, Anda bisa mengatur stok ke 0 atau menghapusnya dari riwayat
    $query_history = "INSERT INTO history_barang (id_barang, stok, satuan, status) VALUES (?, 0, ?, 'masuk')";
    $stmt_history = $conn->prepare($query_history);
    $stmt_history->bind_param("is", $id_barang, $satuan);
    $stmt_history->execute();

    // Redirect atau tampilkan pesan sukses
    header('Location: admin.php?messageCreate=Success');
    exit;
}
?>