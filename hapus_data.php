<?php
include 'query/koneksi.php';

$id_barang = $_GET['id_barang'];
$sqlhapus = "UPDATE barang SET is_delete = 1 WHERE id_barang = $id_barang";
$hasil = $konek->query($sqlhapus);

header('location: ?page=produk');

?>
