<?php
include 'query/boot.php';
include "query/koneksi.php";

date_default_timezone_set('Asia/Jakarta');

// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Ambil username dari session (pastikan sama seperti di index.php)
$username = $_SESSION['user'] ?? '';

// Ambil data admin (id_admin dan nama)
$id_admin = 0;
$kasir = 'Tidak Dikenal';
if ($username) {
  $q_admin = mysqli_query($konek, "SELECT id_admin, nama FROM admin WHERE username='$username'");
  if ($q_admin && mysqli_num_rows($q_admin) > 0) { // ✅ perbaikan: harus > 0, bukan > 1
    $row_admin = mysqli_fetch_assoc($q_admin);
    $id_admin = $row_admin['id_admin'];
    $kasir = $row_admin['nama'];
  }
}

// Ambil tanggal dan data post
$tanggal = date('d/m/Y H:i');
$nama    = $_POST['nama'] ?? '';
$total   = $_POST['total'] ?? 0;
$bayar   = $_POST['bayar'] ?? 0;
$kembali = $bayar - $total;

// Jika data dikirim dari form transaksi
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $tanggal_sql = date('Y-m-d H:i'); // format SQL
  $total_harga = $_POST['total'];
  $bayar       = $_POST['bayar'];

  // 1. Simpan ke tabel transaksi (tambahkan id_admin)
  $sql = "INSERT INTO transaksi (id_admin, tanggal, total_harga, bayar, atas_nama) 
          VALUES ('$id_admin', '$tanggal_sql', '$total_harga', '$bayar', '$nama')";
  if (mysqli_query($konek, $sql)) {
    // Ambil id_transaksi terakhir
    $id_transaksi = mysqli_insert_id($konek);

    // 2. Simpan detail transaksi
    if (!empty($_SESSION['cart'])) {
      foreach ($_SESSION['cart'] as $id_barang => $item) {
        $jumlah_beli  = $item['qty'];
        $jumlah_total = $item['harga'] * $item['qty'];

        $sql_detail = "INSERT INTO transaksi_detail 
                       (id_transaksi, id_barang, jumlah_beli, jumlah_total) 
                       VALUES 
                       ('$id_transaksi', '$id_barang', '$jumlah_beli', '$jumlah_total')";
        mysqli_query($konek, $sql_detail);

        // Kurangi stok barang
        $sql_update = "UPDATE barang 
                       SET stok = stok - $jumlah_beli 
                       WHERE id_barang = '$id_barang'";
        mysqli_query($konek, $sql_update);
      }
    }

    // 3. Kosongkan keranjang
    unset($_SESSION['cart']);

    // 4. Redirect ke halaman struk
    echo "<script>location.href='?page=struk&id=$id_transaksi';</script>";
    exit;
  } else {
    echo "Gagal menyimpan transaksi: " . mysqli_error($konek);
  }
}

// --- Ambil data dari database untuk cetak struk ---
$id_transaksi = $_GET['id'] ?? 0;
$q_transaksi = mysqli_query($konek, "
  SELECT t.*, a.nama AS nama_admin
  FROM transaksi t
  LEFT JOIN admin a ON t.id_admin = a.id_admin
  WHERE t.id_transaksi='$id_transaksi'
");
$transaksi = mysqli_fetch_assoc($q_transaksi);

$q_detail = mysqli_query($konek, "
  SELECT td.*, b.nama_barang, b.harga_barang 
  FROM transaksi_detail td
  JOIN barang b ON td.id_barang = b.id_barang
  WHERE td.id_transaksi='$id_transaksi'
");
?>

<style>
  @media print {
    body * {
      visibility: hidden;
    }
    #print, #print * {
      visibility: visible;
    }
    #print {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
    }
    .btn {
      display: none !important;
    }
  }
</style>

<!-- ===================== CETAK STRUK ===================== -->
<div id="print" class="container col-md-6 mt-4">
  <div class="card border-dark shadow-lg">
    <div class="card-body">
      <!-- Header -->
      <div class="text-center">
        <h6 class="fw-bold">Warung Makanan Tradisional / 0831-4376-0186</h6>
        <p class="mb-0">Kp. Cimeta RT 001 / RW 014, Desa Bojong</p>
        <p class="mb-0">Kecamatan Rongga, Kab. Bandung Barat, 40565</p>
        <p class="fw-bold">Terima kasih telah berbelanja di warung kami!</p>
      </div>

      <!-- Kasir & Tanggal -->
      <div class="row border-top border-bottom py-2">
        <div class="col-6">Kasir : <?= htmlspecialchars($transaksi['nama_admin'] ?? $kasir) ?></div>
        <div class="col-6 text-end"><?= date('d/m/Y H:i', strtotime($transaksi['tanggal'] ?? $tanggal)) ?></div>
      </div>

      <!-- Tabel Belanja -->
      <div class="my-3" style="min-height:100px;">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Barang</th>
              <th class="text-end">Qty</th>
              <th class="text-end">Harga</th>
              <th class="text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $total_item = 0;
            if ($q_detail && mysqli_num_rows($q_detail) > 0):
              while ($row = mysqli_fetch_assoc($q_detail)):
                $total_item += $row['jumlah_beli'];
            ?>
              <tr>
                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td class="text-end"><?= $row['jumlah_beli'] ?></td>
                <td class="text-end"><?= number_format($row['harga_barang'], 0, ',', '.') ?></td>
                <td class="text-end"><?= number_format($row['jumlah_total'], 0, ',', '.') ?></td>
              </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="4" class="text-center">Tidak ada data barang</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Total -->
      <div class="border-top pt-2">
        <div class="d-flex justify-content-between">
          <span>Atas Nama :</span>
          <span><?= htmlspecialchars($transaksi['atas_nama'] ?? '-') ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span>Total Item :</span> <span><?= $total_item ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span>Total Belanja :</span> 
          <span>Rp <?= number_format($transaksi['total_harga'] ?? 0, 0, ',', '.') ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span>Bayar :</span> 
          <span>Rp <?= number_format($transaksi['bayar'] ?? 0, 0, ',', '.') ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span>Kembalian :</span> 
          <span>Rp <?= number_format(($transaksi['bayar'] ?? 0) - ($transaksi['total_harga'] ?? 0), 0, ',', '.') ?></span>
        </div>
      </div>

      <!-- Footer -->
      <div class="border-top mt-3 text-center pt-2 small">
        <p class="mb-0">Kritik & Saran: <b>Asep134@gmail.com</b></p>
        <p class="mb-0">SMS/WA: <b>0857-9744-3884</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Tombol -->
<div class="mt-3 d-flex justify-content-center gap-2">
  <a href="?page=transaksi" class="btn btn-success">Selesai</a>
  <button class="btn btn-primary" onclick="window.print()">
    <i class="bi bi-printer"></i> Cetak
  </button>
</div>
