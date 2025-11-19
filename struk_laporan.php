<?php
include 'query/boot.php';
include "query/koneksi.php";

date_default_timezone_set('Asia/Jakarta');

// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Ambil data admin (kasir yang login)
$id_admin = $_SESSION['id_admin'] ?? 0;
$kasir = 'Kasir Tidak Dikenal';

if ($id_admin > 0) {
  $q_admin = mysqli_query($konek, "SELECT nama FROM admin WHERE id_admin='$id_admin'");
  if ($row_admin = mysqli_fetch_assoc($q_admin)) {
    $kasir = $row_admin['nama'];
  }
}

// Ambil id transaksi dari URL
$id_transaksi = $_GET['id'] ?? 0;

// Ambil data transaksi
$q_transaksi = mysqli_query($konek, "SELECT * FROM transaksi WHERE id_transaksi='$id_transaksi'");
$transaksi   = mysqli_fetch_assoc($q_transaksi);

// Ambil detail transaksi
$q_detail = mysqli_query($konek, "
    SELECT td.*, b.nama_barang, b.harga_barang 
    FROM transaksi_detail td
    JOIN barang b ON td.id_barang = b.id_barang
    WHERE td.id_transaksi='{$transaksi['id_transaksi']}'
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

<div id="print" class="container col-md-6 mt-4">
  <div class="card border-dark shadow-lg">
    <div class="card-body">
      <!-- Header -->
      <div class="text-center">
        <h6 class="fw-bold">Warung Makanan Tradisional / 0831-4376-0186</h6>
        <p class="mb-0">Kampung Cimeta RT.001 / RW.014, Desa Bojong</p>
        <p class="mb-0">Kecamatan Rongga, Kabupaten Bandung Barat, 40565</p>
        <p class="fw-bold">Terima kasih telah berbelanja di Warung kami!</p>
      </div>

      <!-- Kasir & Tgl -->
      <div class="row border-top border-bottom py-2">
        <div class="col-6">Kasir : <?= htmlspecialchars($kasir) ?></div>
        <div class="col-6 text-end">
          <?= date('d/m/Y H:i', strtotime($transaksi['tanggal'])) ?>
        </div>
      </div>

      <!-- Tabel belanja -->
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
            while ($row = mysqli_fetch_assoc($q_detail)):
              $total_item += $row['jumlah_beli'];
            ?>
              <tr>
                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td class="text-end"><?= $row['jumlah_beli'] ?></td>
                <td class="text-end"><?= number_format($row['harga_barang'], 0, ',', '.') ?></td>
                <td class="text-end"><?= number_format($row['jumlah_total'], 0, ',', '.') ?></td>
              </tr>
            <?php endwhile; ?>
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
          <span>Total Belanja :</span> <span>Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span>Bayar :</span> <span>Rp <?= number_format($transaksi['bayar'], 0, ',', '.') ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span>Kembalian :</span> <span>Rp <?= number_format($transaksi['bayar'] - $transaksi['total_harga'], 0, ',', '.') ?></span>
        </div>
      </div>

      <!-- Footer -->
      <div class="border-top mt-3 text-center pt-2 small">
        <p class="mb-0">Kritik & Saran : <b>Asep134@gmail.com</b></p>
        <p class="mb-0">SMS/WA : <b>0857-9744-3884</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Tombol -->
<div class="mt-3 d-flex justify-content-center gap-2">
  <a href="?page=laporan" class="btn btn-secondary">Selesai</a>
  <button class="btn btn-primary" onclick="window.print()">
    <i class="bi bi-printer"></i> Cetak
  </button>
</div>
