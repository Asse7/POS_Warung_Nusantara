<div class="container col-6 mt-3">
<?php
include 'query/boot.php';
include 'query/koneksi.php';

// --- Generate kode otomatis ---
$result = $konek->query("SELECT kode_barang FROM barang ORDER BY kode_barang DESC LIMIT 1");
$row = $result->fetch_assoc();

if ($row) {
    $lastKode = $row['kode_barang'];
    $lastNumber = (int) substr($lastKode, 2);
    $newNumber = $lastNumber + 1;
    $kode_barang_baru = "Mn" . str_pad($newNumber, 3, "0", STR_PAD_LEFT);
} else {
    $kode_barang_baru = "Mn001";
}

// --- Proses Simpan ---
if (isset($_POST['kirim'])) {
    $kode_barang  = $_POST['kode_barang'];
    $nama_barang  = trim($_POST['nama_barang']);
    $harga_barang = (int) $_POST['harga_barang'];
    $stok         = (int) $_POST['stok'];

    // 🚫 Validasi harga dan stok tidak boleh negatif
    if ($harga_barang < 0 || $stok < 0) {
        echo '<div class="alert alert-danger text-center mb-3">
                ❌ Harga dan Stok tidak boleh bernilai negatif!
              </div>';
    } else {
        $nama_file = $_FILES['gambar']['name'];
        $temp_file = $_FILES['gambar']['tmp_name'];
        $upload_dir = "poto/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // 🔍 Cek apakah nama_barang sudah ada
        $cek = $konek->query("SELECT * FROM barang WHERE nama_barang = '$nama_barang' AND is_delete = 0");
        if ($cek->num_rows > 0) {
            echo '<div class="alert alert-warning text-center mb-3">
                    ⚠️ Produk "<b>' . htmlspecialchars($nama_barang) . '</b>" sudah ada dalam Data Menu!
                  </div>';
        } else {
            // Lanjut upload jika belum ada
            if (move_uploaded_file($temp_file, $upload_dir . $nama_file)) {
                $kirim = $konek->query("INSERT INTO barang 
                    (kode_barang, nama_barang, harga_barang, stok, file_gambar, is_delete) 
                    VALUES 
                    ('$kode_barang', '$nama_barang', '$harga_barang', '$stok', '$nama_file', 0)");

                if ($kirim) {
                    echo '<div class="alert alert-success text-center mb-3">
                            ✅ Data berhasil ditambahkan!
                          </div>';
                } else {
                    echo "<div class='alert alert-danger text-center mb-3'>
                            ❌ Maaf, Data gagal disimpan: " . $konek->error . "
                          </div>";
                }
            } else {
                echo '<div class="alert alert-danger text-center mb-3">
                        Gagal upload gambar!
                      </div>';
            }
        }
    }
}
?>

<div class="card shadow-sm" style="background-color: #4d2804ff; color: white; font-size: 14px;">
    <div class="card-body">
        <div class="text-center mb-3">
            <label><b>INPUT DATA MENU</b></label>
        </div>
        <form action="" method="post" enctype="multipart/form-data" class="container-sm">
            <div class="mb-2">
                <label class="form-label">Kode</label>
                <input type="text" class="form-control form-control-sm" 
                       name="kode_barang" value="<?= $kode_barang_baru ?>" readonly>
            </div>
            <div class="mb-2">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control form-control-sm" name="nama_barang" placeholder="Masukan Nama..." required>
            </div>
            <div class="mb-2">
                <label class="form-label">Harga</label>
                <input type="number" class="form-control form-control-sm" name="harga_barang" placeholder="Masukan Harga..." min="0" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Stok</label>
                <input type="number" class="form-control form-control-sm" name="stok" placeholder="Masukan Stok..." min="0" required>
            </div>
            <div class="mb-2">
                <label for="formFile" class="form-label">Masukan Gambar</label>
                <input class="form-control form-control-sm" type="file" id="formFile" name="gambar" required>
            </div>
            <div class="text-end">
                <button name="kirim" style="margin-top: 10px;" class="btn btn-success btn-sm">Tambahkan</button>
            </div>
        </form>
    </div>
</div>
</div>
