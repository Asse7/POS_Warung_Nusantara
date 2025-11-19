<div class="container col-4 mt-5 ">

    <?php
    include 'query/boot.php';
    include 'query/koneksi.php';
    session_start();

    if (isset($_POST['login'])) {
        // Escape input untuk keamanan
        $input = mysqli_real_escape_string($konek, $_POST['email']);
        $pass  = mysqli_real_escape_string($konek, $_POST['pass']);

        // Jalankan query login (bisa pakai username ATAU nama)
        $log = $konek->query("
            SELECT * FROM admin 
            WHERE (username='$input' OR nama='$input') 
            AND password=MD5('$pass')
        ");

        $cek = $log->num_rows;

        if ($cek > 0) {
            $data = $log->fetch_assoc(); // Ambil data admin

            // Simpan ke session
            $_SESSION['user'] = $data['username'];
            $_SESSION['id_admin'] = $data['id_admin'];
            $_SESSION['nama_admin'] = $data['nama'];

            // Arahkan ke beranda
            ?>
            <script>
                document.location.href = 'beranda.php';
            </script>
            <?php
        } else {
            echo "<div class='alert alert-danger mt-3'>
                    Mohon maaf, login gagal. Pastikan email dan password Anda benar!
                  </div>";
        }
    }
    ?>

    <!-- script untuk menampilkan password -->
    <script>
        function togglePassword() {
            let pwField = document.getElementById("exampleInputPassword1");
            pwField.type = pwField.type === "password" ? "text" : "password";
        }
    </script>

    <div class="card rounded-4 shadow">
        <div class="card-body rounded-4 text-white" style="background-color:  #4D2804;">
            <h3 class="text-center mb-3">Login Kasir</h3>
            <p class="text-center mb-4">Silakan masukkan email dan password Anda untuk masuk.</p>
            <form action="" method="post">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email</label>
                    <input name="email" type="text" class="form-control" id="exampleInputEmail1" placeholder="Masukkan email ">
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input name="pass" type="password" class="form-control" id="exampleInputPassword1" placeholder="Masukkan password">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1" onclick="togglePassword()">
                    <label class="form-check-label" for="exampleCheck1">Tampilkan Password</label>
                </div>
                <div class="text-center">
                    <button name="login" type="submit" class="btn btn-success">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>
