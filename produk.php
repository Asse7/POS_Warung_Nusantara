<?php
include 'query/boot.php';
include 'query/koneksi.php';
?>



<div class="card">
  <!-- Body Card -->
  <div class="card-body">
    <div class="row">
      <?php

      // Ambil data barang dari tabel 'barang'
      $tampil = $konek->query("SELECT * FROM barang where is_delete = 0 ORDER BY kode_barang ASC");

      // Looping data barang
      foreach ($tampil as $data) {
      ?>
        <!-- Kolom card tiap produk -->
        <div class="col-md-2 mb-3">
          <div class="card h-100 shadow-sm" style="max-width:180px; font-size:13px;">

            <!-- Gambar Produk -->
            <img src="poto/<?= $data['file_gambar'] ?>"
              class="card-img-top"
              alt="<?= $data['nama_barang'] ?>"
              style="height:120px; object-fit:cover;">

            <!-- Isi Card -->
            <div class="card-body p-2">
              <!-- Nama Barang -->
              <h6 class="card-title mb-1" style="font-size:14px;">
                <?= $data['nama_barang']; ?>
              </h6>

              <!-- Detail Barang -->
              <p class="card-text mb-1" style="font-size:12px;">
                <b>Kode:</b> <?= $data['kode_barang']; ?><br>
                <b>Harga:</b> Rp <?= number_format((int)($data['harga_barang'] ?: 0), 0, ',', '.'); ?><br>
                <b>Stok:</b> <?= number_format((int)($data['stok'] ?: 0), 0, ',', '.'); ?><br>
              </p>

              <!-- Tombol Aksi -->
              <div class="d-flex justify-content-between">
                <!-- Hapus -->
                <a href="?page=hapus_data&id_barang=<?= $data['id_barang'] ?>"
                  onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Menu Ini?')"
                  class="btn btn-danger btn-sm">
                  <i class="bi bi-trash"></i>
                </a>


                <!-- Edit -->
                <a href="?page=edit_produk&id=<?= $data['id_barang'] ?>"
                  class="btn btn-warning btn-sm">
                  <i class="bi bi-pencil-square"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>