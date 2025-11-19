<?php
include 'query/boot.php';
session_start();

$user = $_SESSION['user'];
if (!isset($user)) {
?>
    <script>
        document.location.href = 'index.php';
    </script>
<?php
} else {
    echo "";
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>

<body>
    <nav class=" navbar navbar-expand-lg ">
        <div class="container-fluid" style="background-color: #4d2804ff; color: white; height: 55px; ">

            <ul class="navbar-nav text-star">
                <li class="nav-item dropdown">
                    <a class="navbar-brand  text-white" href="?page=produk" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <b>POS Warung Nusantara</b>
                    </a>
                </li>
            </ul>


            <form class="d-flex" role="search" method="post" action="?page=cari">
                <input class="form-control me-3" type="search" placeholder="Search" aria-label="Search" name="cari" />
                <button class="btn" type="submit" style="background-color: #997950; color: #fff;">Search</button>
            </form>
        </div>
    </nav>

    <div class="row">
        <div class="col col-2">
            <ul class="list-group">
                <a href="?page=produk" class="list-group-item active"
                    style="background-color: #4d2804ff; color: white;"><b>Dashboard</b></a>
                <a href="?page=input_produk" class="list-group-item"><b>Input Data</b></a>
                <a href="?page=transaksi" class="list-group-item"><b>Transaksi</b></a>
                <a href="?page=laporan" class="list-group-item"><b>Laporan</b></a>
                <a href="?page=edit_profil" class="list-group-item"><b>Edit Profil</b></a>
                <a href="logout.php" class="list-group-item list-group-item-danger" onclick="return confirm('Apakah Anda yakin untuk log out?')"><b> Logout</b></a>
            </ul>
        </div>
        <div class="col">
            <?php

            $page = $_GET['page'] ?? 'home';
            $file = "$page.php";

            if (file_exists($file)) {
                include $file;
            } else {
                include "produk.php";
            }
            ?>
        </div>
    </div>
</body>