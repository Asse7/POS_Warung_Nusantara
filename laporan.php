<?php
include 'query/boot.php';
include "query/koneksi.php";

date_default_timezone_set('Asia/Jakarta');

$tgl_awal  = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

$where = "";
if ($tgl_awal && $tgl_akhir) {
  $where = "WHERE DATE(tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$sql = "SELECT id_transaksi, tanggal, total_harga, bayar, atas_nama
        FROM transaksi 
        $where 
        ORDER BY tanggal DESC";
$result = mysqli_query($konek, $sql);

$total = 0;
$no = 1;
?>

<div class="card">
  <div class="card-body">
    <div class="container mt-4">

      <!-- Filter tanggal -->
      <form method="get">
        <div class="row g-2 mb-3">
          <div class="col-md-3">
            <input type="date" class="form-control" name="tgl_awal" value="<?= $_GET['tgl_awal'] ?? '' ?>">
            <input type="hidden" name="page" value="laporan">
          </div>
          <div class="col-md-3">
            <input type="date" class="form-control" name="tgl_akhir" value="<?= $_GET['tgl_akhir'] ?? '' ?>">
            <input type="hidden" name="page" value="laporan">
          </div>
          <div class="col-md-2">
            <button class="btn btn-warning w-100" type="submit">Cari</button>
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary w-100" type="button" onclick="printDiv('print')">
              <i class="bi bi-printer"></i> Cetak
            </button>
          </div>
        </div>
      </form>

      <!-- Data laporan -->
      <fieldset id="print">
        <div class="card shadow-lg">
          <div class="card-header bg-success text-white fw-bold text-star">
            Data Laporan
          </div>
          <div class="card-body p-0">
            <table class="table table-bordered mb-0 text-center align-middle">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th>Tanggal Transaksi</th>
                  <th>Atas Nama</th>
                  <th>Subtotal</th>
                  <th>Bayar</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/Y H:i:s', strtotime($row['tanggal'])) ?> WIB</td>
                    <td><?= htmlspecialchars($row['atas_nama']) ?></td>
                    <td>Rp. <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                    <td>Rp. <?= number_format($row['bayar'], 0, ',', '.') ?></td>
                    <td>
                      <a href="?page=struk_laporan&id=<?= $row['id_transaksi'] ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-card-text"></i>
                      </a>
                      <!-- <a href="?page=hapus_transaksi&id=<?= $row['id_transaksi'] ?>" 
                         class="btn btn-sm btn-danger"
                         onclick="return confirm('Yakin ingin menghapus data laporan ini?')">
                        <i class="bi bi-trash"></i>
                      </a> -->
                    </td>
                  </tr>
                  <?php $total += $row['total_harga']; ?>
                <?php endwhile; ?>
              </tbody>
              <tfoot class="fw-bold">
                <tr>
                  <td colspan="3" class="text-end">TOTAL :</td>
                  <td class="text-center">Rp. <?= number_format($total, 0, ',', '.') ?></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </fieldset>

    </div>
  </div>
</div>

<script type="text/javascript">
  function printDiv(el) {
    var a = document.body.innerHTML;
    var b = document.getElementById(el).innerHTML;
    document.body.innerHTML = b;
    window.print();
    document.body.innerHTML = a;
  }
</script>
