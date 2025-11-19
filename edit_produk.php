<?php
include 'query/boot.php';
include 'query/koneksi.php';

$id_barang = $_GET['id'];
$tampil = $konek->query("SELECT * FROM barang WHERE id_barang='$id_barang'");
$data = $tampil->fetch_array();
?>

<div class="container d-flex justify-content-center mt-3">
    <div class="col-md-5">
        <?php
        if (isset($_POST['update'])) {
            $nama_barang = $_POST['nama_barang'];
            $kode_barang = $_POST['kode_barang'];
            $harga_barang = $_POST['harga_barang'];
            $stok = $_POST['stok'];

            // 🔍 Validasi agar tidak boleh nilai minus
            if ($harga_barang < 0 || $stok < 0) {
                echo "<div class='alert alert-warning mt-3 text-center'>
                        ⚠️ Harga dan stok tidak boleh bernilai negatif!
                      </div>";
            } else {
                $nama_file = $_FILES['gambar']['name'];
                $temp_file = $_FILES['gambar']['tmp_name'];
                $upload_dir = 'poto/';

                // Default pakai gambar lama
                $file_gambar = $data['file_gambar'];

                // Jika upload gambar baru
                if (!empty($nama_file)) {
                    if (move_uploaded_file($temp_file, $upload_dir . $nama_file)) {
                        $file_gambar = $nama_file; // pakai gambar baru
                    }
                }

                $update = $konek->query("UPDATE barang 
                    SET nama_barang='$nama_barang', kode_barang='$kode_barang', 
                        harga_barang='$harga_barang', stok='$stok', file_gambar='$file_gambar' 
                    WHERE id_barang='$id_barang'");

                if (!$update) {
                    echo "<div class='alert alert-danger mt-3'>❌ Maaf, data gagal diupdate.</div>";
                } else {
                    echo '<div class="alert alert-success mt-3 text-center">✅ Data berhasil diupdate!</div>';
                }
            }
        }
        ?>
        
        <div class="card shadow-sm" style="background-color: #4d2804ff; color: white; border-radius:10px; max-width: 450px; margin:auto;">
            <div class="card-body p-3">
                <div class="text-center mb-3">
                    <label><b>UPDATE DATA MENU</b></label>
                </div>
                <form action="" method="post" enctype="multipart/form-data" class="container-sm">
                    <div class="mb-2">
                        <label class="form-label">Kode</label>
                        <input type="text" class="form-control form-control-sm" name="kode_barang" 
                               value="<?= $data['kode_barang'] ?>" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control form-control-sm" name="nama_barang" 
                               value="<?= $data['nama_barang'] ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control form-control-sm" name="harga_barang" 
                               min="0" value="<?= $data['harga_barang'] ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Stok</label>
                        <input type="number" class="form-control form-control-sm" name="stok" 
                               min="0" value="<?= $data['stok'] ?>" required>
                    </div>
                    <div class="mb-2">
                        <label for="formFile" class="form-label">Masukan Gambar</label>
                        <input class="form-control form-control-sm" type="file" id="formFile" name="gambar">
                    </div>

                    <div class="text-end">
                        <button name="update" class="btn btn-warning btn-sm px-4">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
