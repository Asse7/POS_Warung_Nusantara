<?php
include 'query/boot.php';
include 'query/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['id_admin'])) {
    echo "<script>document.location.href='index.php';</script>";
    exit;
}

$id_admin = $_SESSION['id_admin'];

// Ambil data admin saat ini
$query = $konek->query("SELECT * FROM admin WHERE id_admin='$id_admin'");
$data  = $query->fetch_assoc();

// ===== PROSES UPDATE =====
if (isset($_POST['simpan'])) {

    $password_lama = mysqli_real_escape_string($konek, $_POST['password_lama']);
    $password_baru = mysqli_real_escape_string($konek, $_POST['password_baru']);

    // Cek password lama
    $cek_pw = $konek->query("
        SELECT * FROM admin 
        WHERE id_admin='$id_admin' AND password=MD5('$password_lama')
    ");

    if ($cek_pw->num_rows == 0) {
        echo "<div class='alert alert-danger mt-3'>Password lama salah! Perubahan dibatalkan.</div>";
    } else {

        // UPDATE password jika ada perubahan
        if ($password_baru != "") {
            $sql = "UPDATE admin SET password=MD5('$password_baru') WHERE id_admin='$id_admin'";
        } else {
            $sql = "UPDATE admin SET id_admin=id_admin WHERE id_admin='$id_admin'"; // Query dummy
        }

        $update = $konek->query($sql);

        if ($update) {
            echo "<div class='alert alert-success mt-3'>Password berhasil diperbarui!</div>";
            echo "<meta http-equiv='refresh' content='1'>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Terjadi kesalahan: " . $konek->error . "</div>";
        }
    }
}
?>

<!-- Script tampilkan password -->
<script>
function togglePassword(id) {
    let field = document.getElementById(id);
    field.type = field.type === "password" ? "text" : "password";
}
</script>

<!-- VALIDASI FORM -->
<script>
function validateForm() {
    let pwLama   = document.getElementById("pw_lama").value.trim();
    let pwBaru   = document.getElementById("pw_baru").value.trim();

    if (pwLama === "") {
        alert("Password lama wajib diisi!");
        return false;
    }

    if (pwBaru !== "") {
        if (pwBaru.length < 6) {
            alert("Password baru minimal 6 karakter!");
            return false;
        }
        if (pwBaru === pwLama) {
            alert("Password baru tidak boleh sama dengan password lama!");
            return false;
        }
    }

    return true;
}
</script>

<div class="container col-5 mt-4">
    <div class="card shadow rounded-4">
        <div class="card-body">
            <h3 class="mb-4 text-center">Edit Profil</h3>

            <form action="" method="post" onsubmit="return validateForm()">

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" 
                           value="<?php echo $data['nama']; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="text" name="username" class="form-control"
                           value="<?php echo $data['username']; ?>" readonly>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label">Password Lama (wajib)</label>
                    <div class="input-group">
                        <input type="password" name="password_lama" id="pw_lama" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('pw_lama')">
                            Tampilkan
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password_baru" id="pw_baru" class="form-control"
                               placeholder="Kosongkan jika tidak ingin mengubah password">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('pw_baru')">
                            Tampilkan
                        </button>
                    </div>
                </div>

                <div class="text-center">
                    <button name="simpan" class="btn btn-primary"
                            onclick="return confirm('Apakah Anda Yakin Ingin Mengedit Profil Ini?')">
                        Simpan Perubahan
                    </button>
                    <a href="beranda.php" class="btn btn-secondary">Kembali</a>
                </div>

            </form>
        </div>
    </div>
</div>
