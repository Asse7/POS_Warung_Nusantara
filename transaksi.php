<?php
include 'query/boot.php';
include 'query/koneksi.php';

// Pastikan session hanya start sekali
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan user sudah login
if (!isset($_SESSION['id_admin'])) {
    echo "<script>alert('Silakan login terlebih dahulu!');document.location.href='index.php';</script>";
    exit;
}

// ==================== AMBIL DATA ADMIN (KASIR) ====================
$id_admin = $_SESSION['id_admin'];
$getKasir = $konek->query("SELECT nama FROM admin WHERE id_admin = '$id_admin'");
$kasirData = $getKasir->fetch_assoc();
$nama_kasir = $kasirData['nama'] ?? 'Kasir Tidak Dikenal';

// ==================== PROSES TAMBAH KE KERANJANG ====================
if (isset($_POST['add'])) {
  $id_produk   = $_POST['kode_barang'];
  $id_barang   = $_POST['id_barang'];
  $nama_produk = $_POST['nama_barang'];
  $harga       = $_POST['harga_barang'];
  $qty         = $_POST['qty'];

  // Cek stok di database
  $cekStok = $konek->query("SELECT stok FROM barang WHERE id_barang = '$id_barang'");
  $stokData = $cekStok->fetch_assoc();

  if ($stokData['stok'] <= 0) {
      echo "<script>alert('Stok habis, tidak bisa menambah ke keranjang!');</script>";
  } elseif ($qty > $stokData['stok']) {
      echo "<script>alert('Jumlah melebihi stok yang tersedia!');</script>";
  } else {
      if (!isset($_SESSION['cart'])) {
          $_SESSION['cart'] = [];
      }

      if (isset($_SESSION['cart'][$id_produk])) {
          $_SESSION['cart'][$id_produk]['qty'] += $qty;
      } else {
          $_SESSION['cart'][$id_produk] = [
              'nama'  => $nama_produk,
              'harga' => $harga,
              'qty'   => $qty
          ];
      }
  }
}

// Hapus produk dari keranjang
if (isset($_GET['hapus'])) {
  $id_produk = $_GET['hapus'];
  unset($_SESSION['cart'][$id_produk]);
}
?>

<!-- ===================== HALAMAN TRANSAKSI ===================== -->
<div class="card">
  <div class="row">
  <!-- ========== KOLOM KIRI (DAFTAR PRODUK) ========== -->
<div class="container col-6 mt-5">
  <div class="card">
    <div class="card-body" style="max-height:350px; overflow-y:auto;">
      
      <!-- FORM CARI -->
      <form class="d-flex" role="search" method="post" action="?page=transaksi">
        <input class="form-control me-2" type="search" placeholder="Cari Barang..." aria-label="Search" name="cari" />
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>

      <table class="table mt-3">
        <thead style="background-color: #8B4513; color: white;">
          <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Gambar</th>
            <th>Barang</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
          </tr>
        </thead>

        <tbody>
          <?php
          $cari = isset($_POST['cari']) ? $_POST['cari'] : '';

          $tampil2 = $konek->query("SELECT * FROM barang 
            WHERE is_delete = 0 
            AND (nama_barang LIKE '%$cari%' 
              OR kode_barang LIKE '%$cari%' 
              OR harga_barang LIKE '%$cari%' 
              OR stok LIKE '%$cari%')
          ");

          $no = 1;

          if ($tampil2->num_rows > 0) {
            while ($data = $tampil2->fetch_array()) {
          ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><?= $data['kode_barang']; ?></td>

                <!-- TAMPILAN GAMBAR -->
                 <td> 
                  <img 
                    src="poto/<?= $data['file_gambar']; ?>" 
                    alt="<?= $data['nama_barang']; ?>" 
                    style="width:55px; height:55px; object-fit:cover; border-radius:6px; margin-bottom:4px;">
                 </td>

                <td><?= $data['nama_barang']; ?></td>
                <td><?= number_format((int)($data['harga_barang'] ?: 0), 0, ',', '.'); ?></td>
                <td><?= $data['stok']; ?></td>

                <td>
                  <?php if ($data['stok'] <= 0): ?>
                    <button type="button" class="btn btn-danger btn-sm" disabled>Stok Habis</button>
                  <?php else: ?>

                    <!-- ======= Aksi seperti gambar ======= -->
                    <form method="post" class="d-flex">
                      <input type="hidden" name="kode_barang" value="<?= $data['id_barang'] ?>">
                      <input type="hidden" name="id_barang" value="<?= $data['id_barang'] ?>">
                      <input type="hidden" name="nama_barang" value="<?= $data['nama_barang'] ?>">
                      <input type="hidden" name="harga_barang" value="<?= $data['harga_barang'] ?>">

                      <div class="input-group aksi-box">

                        <!-- INPUT QTY (mirip gambar) -->
                        <input type="number" 
                               name="qty" 
                               value="1" 
                               min="1" 
                               max="<?= $data['stok'] ?>" 
                               class="form-control qty-input">

                        <!-- TOMBOL TAMBAH -->
                        <button type="submit" name="add" class="btn btn-success aksi-btn">
                          <i class="bi bi-cart-plus"></i>
                        </button>

                      </div>
                    </form>
                    <!-- ==================================== -->

                  <?php endif; ?>
                </td>
              </tr>
          <?php
            }
          } else {
            echo '<tr><td colspan="7" class="text-center text-danger">Maaf produk yang dicari tidak ada.</td></tr>';
          }
          ?>
        </tbody>
      </table>

    </div>
  </div>
</div>

<!-- ====== CSS UNTUK AKSI (SAMAKAN DENGAN FOTO) ====== -->
<style>
.aksi-box {
    display: flex;
    border: 2px solid #d5d5d5;
    border-radius: 14px;
    overflow: hidden;
    background: white;
    width: 120px;
}

.qty-input {
    width: 50px;
    border: none;
    text-align: center;
    font-size: 15px;
}

.qty-input:focus {
    outline: none;
    box-shadow: none;
}

.aksi-btn {
    background: #1f8b4c !important;
    border: none;
    width: 55px;
    border-radius: 0;
}

.aksi-btn:hover {
    background: #15713c !important;
}
</style>



    <!-- ========== KOLOM KANAN (KERANJANG BELANJA) ========== -->
    <div class="container col-6 mt-5">
      <div class="card">
        <div class="card-body">
          <h3 class="mt-4">Keranjang</h3>
          <table class="table table-bordered">
            <tr>
              <th>Nama Produk</th>
              <th>Harga</th>
              <th>Jumlah</th>
              <th>Subtotal</th>
              <th>Aksi</th>
            </tr>

            <?php
            $total = 0;
            if (!empty($_SESSION['cart'])) {
              foreach ($_SESSION['cart'] as $id => $item) {
                $subtotal = $item['harga'] * $item['qty'];
                $total += $subtotal;
                echo "
                  <tr>
                    <td>{$item['nama']}</td>
                    <td>Rp " . number_format($item['harga'], 0, ',', '.') . "</td>
                    <td>{$item['qty']}</td>
                    <td>Rp " . number_format($subtotal, 0, ',', '.') . "</td>
                    <td><a href='?page=transaksi&hapus=$id' class='btn btn-danger btn-sm'>Hapus</a></td>
                  </tr>
                ";
              }
              echo "
                <tr>
                  <td colspan='3' align='right'><b>Total</b></td>
                  <td colspan='2'><b>Rp " . number_format($total, 0, ',', '.') . "</b></td>
                </tr>
              ";
            } else {
              echo "<tr><td colspan='5' align='center'>Keranjang kosong</td></tr>";
            }
            ?>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ===================== FORM TRANSAKSI ===================== -->
<div class="card mt-4">
  <div class="card-body">
    <div class="container col-md-6">
      <div class="card shadow-lg">
        <div class="card-body">
          <h5 class="mb-3">Form Transaksi</h5>
          <form method="post" action="?page=struk" id="formTransaksi">
            <?php $tanggal = date('d/m/Y'); ?>

            <!-- Tanggal -->
            <div class="mb-3">
              <label class="form-label">Tgl. Transaksi</label>
              <input type="text" class="form-control" value="<?= $tanggal ?>" readonly>
            </div>


            <!-- Daftar Belanja -->
            <div class="mb-3">
              <label class="form-label">Daftar Belanja</label>
              <ul class="list-group">
                <?php
                $total = 0;
                if (!empty($_SESSION['cart'])) {
                  foreach ($_SESSION['cart'] as $id => $item) {
                    $subtotal = $item['harga'] * $item['qty'];
                    $total += $subtotal;
                    echo "
                      <li class='list-group-item d-flex justify-content-between align-items-center'>
                        {$item['nama']} (x{$item['qty']})
                        <span>Rp " . number_format($subtotal, 0, ',', '.') . "</span>
                      </li>
                    ";
                  }
                  echo "
                    <li class='list-group-item d-flex justify-content-between'>
                      <strong>Total</strong>
                      <strong>Rp " . number_format($total, 0, ',', '.') . "</strong>
                    </li>
                  ";
                } else {
                  echo "<li class='list-group-item text-center'>Keranjang kosong</li>";
                }
                ?>
              </ul>
            </div>

            <!-- Nama Pembeli -->
            <div class="mb-3">
              <label class="form-label">Atas Nama (Pelanggan)</label>
              <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama Pelanggan" required>
            </div>

            <!-- Bayar -->
            <div class="mb-3">
              <label class="form-label">Bayar</label>
              <input type="number" name="bayar" class="form-control" placeholder="Masukkan jumlah bayar" required>
            </div>

            <!-- Kembalian -->
            <div class="mb-3">
              <label class="form-label">Kembalian</label>
              <input type="text" id="kembalian" class="form-control" readonly>
            </div>

            <!-- Tombol -->
            <div class="text-end">
              <button type="submit" class="btn btn-success px-4">Bayar</button>
            </div>

            <!-- Hidden input untuk total -->
            <input type="hidden" name="total" id="total" value="<?= $total ?>">
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ===================== SCRIPT JS ===================== -->
<script>
  const inputBayar = document.querySelector("input[name='bayar']");
  const inputTotal = document.getElementById("total");
  const inputKembali = document.getElementById("kembalian");
  const formTransaksi = document.getElementById("formTransaksi");

  inputBayar.addEventListener("input", function() {
    let bayar = parseInt(this.value) || 0;
    let total = parseInt(inputTotal.value) || 0;
    let kembali = bayar - total;
    inputKembali.value = (kembali >= 0) ? kembali : 0;
  });

  formTransaksi.addEventListener("submit", function(e) {
    let bayar = parseInt(inputBayar.value) || 0;
    let total = parseInt(inputTotal.value) || 0;

    if (bayar < total) {
      e.preventDefault();
      alert("⚠️ Uang bayar kurang dari total belanja!");
    }
  });
</script>
