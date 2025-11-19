<?php
include "query/koneksi.php"; 
session_start();

// Pastikan ada id
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // amankan input

    // Query hapus
    $sql = "DELETE FROM transaksi WHERE id_transaksi = $id";
    $hapus = mysqli_query($konek, $sql);

    if ($hapus) {
        // Jika berhasil
        $_SESSION['pesan'] = "Data transaksi berhasil dihapus!";
    } else {
        // Jika gagal
        $_SESSION['pesan'] = "Data transaksi gagal dihapus: " . mysqli_error($konek);
    }

    header("Location: ?page=laporan"); // kembali ke halaman laporan
    exit;
} else {
    header("Location: ?page=laporan");
    exit;
}
?>
