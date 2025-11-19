<?php
include 'query/koneksi.php';

$id_barang = $_GET['id_barang'];
$sqlhapus = "UPDATE barang SET is_delete = 1 WHERE id_barang = $id_barang";
$hasil = $konek->query($sqlhapus);

// Jika berhasil
if ($hasil) {
    header('location: ?page=produk&hapus=sukses');
} else {
    header('location: ?page=produk&hapus=gagal');
}
?>
