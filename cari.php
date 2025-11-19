<?php
include 'query/boot.php';
?>

<div class="card">
  <div class="card-body">
    <div class="row">
      <?php
      include "query/koneksi.php";
      $cari = $_POST['cari'];

      $tampil2 = $konek->query("SELECT * FROM barang WHERE is_delete = 0 
              AND (nama_barang LIKE '%$cari%' 
                OR kode_barang LIKE '%$cari%' 
                OR harga_barang LIKE '%$cari%' 
                OR stok LIKE '%$cari%')");

      // Cek apakah hasilnya ada
      if ($tampil2->num_rows > 0) {
        while ($data = $tampil2->fetch_array()) {
      ?>
          <div class="col-md-2 mb-3">
            <div class="card h-100 shadow-sm" style="max-width:180px; font-size:13px;">
              <img src="poto/<?= $data['file_gambar'] ?>"
                class="card-img-top"
                alt="<?= $data['nama_barang'] ?>"
                style="height:120px; object-fit:cover;">
              <div class="card-body p-2">
                <h6 class="card-title mb-1" style="font-size:14px;"><?= $data['nama_barang']; ?></h6>

                <p class="card-text mb-1" style="font-size:12px;">
                  <b>Kode:</b> <?= $data['kode_barang']; ?><br>
                  <b>Harga:</b> Rp <?= number_format((int)($data['harga_barang'] ?: 0), 0, ',', '.'); ?><br>
                  <b>Stok:</b> <?= number_format((int)($data['stok'] ?: 0), 0, ',', '.'); ?><br>
                </p>

                <div class="d-flex justify-content-between">
                  <!-- Tombol Hapus -->
                  <a href="?page=hapus_data&id_barang=<?= $data['id_barang'] ?>" 
                     onclick="return confirm('Apakah Anda Yakin?')" 
                     class="btn btn-danger btn-sm">
                     <i class="bi bi-trash"></i>
                  </a>

                  <!-- Tombol Edit -->
                  <a href="?page=edit_produk&id=<?= $data['id_barang'] ?>" 
                     class="btn btn-warning btn-sm">
                     <i class="bi bi-pencil-square"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
      <?php 
        }
      } else {
        echo "<div class='text-center  mt-3 text-danger'> 
                <h6>Maaf produk yang Anda cari tidak ada.</h6>
              </div>";
      }
      ?>
    </div>
  </div>
</div>
