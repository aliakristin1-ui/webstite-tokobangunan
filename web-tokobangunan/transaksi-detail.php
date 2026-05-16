<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();
$id = intval($_GET['id'] ?? 0);
$trx = $db->queryOne("SELECT t.*, k.nama as kasir FROM transaksi t JOIN karyawan k ON t.id_kasir=k.id_karyawan WHERE t.id_transaksi=?", [$id]);
if (!$trx) { header('Location: transaksi.php?error=Transaksi tidak ditemukan'); exit; }
$detail = $db->query("SELECT d.*, p.nama_produk, p.satuan FROM detail_transaksi d JOIN produk p ON d.id_produk=p.id_produk WHERE d.id_transaksi=?", [$id]);
include "header.php";
?>
<main id="main">
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-file-earmark-text me-2"></i>Detail Transaksi #<?php echo $id; ?></h4>
        <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / <a href="transaksi.php">Transaksi</a> / Detail</div>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Cetak</button>
        <a href="transaksi.php" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-body">
        <div class="text-center border-bottom pb-3 mb-3">
            <h5 class="fw-bold text-dark">TOKO BANGUNAN BUKIT TUNGGAL JAYA</h5>
            <small class="text-muted">Nota Penjualan</small>
        </div>
        <div class="row mb-3">
            <div class="col-6"><small class="text-muted">No. Transaksi</small><div class="fw-bold">#<?php echo $trx['id_transaksi']; ?></div></div>
            <div class="col-6 text-end"><small class="text-muted">Tanggal</small><div class="fw-bold"><?php echo tanggal(substr($trx['tanggal']??'',0,10)); ?></div></div>
            <div class="col-6 mt-2"><small class="text-muted">Kasir</small><div class="fw-bold"><?php echo htmlspecialchars($trx['kasir']); ?></div></div>
            <div class="col-6 mt-2 text-end"><small class="text-muted">Metode Bayar</small><div><?php echo badge_status($trx['metode_pembayaran']); ?></div></div>
        </div>
        <table class="table table-sm border">
            <thead class="table-light"><tr><th>Produk</th><th class="text-center">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($detail as $d): ?>
            <tr>
                <td><?php echo htmlspecialchars($d['nama_produk']); ?><br><small class="text-muted"><?php echo $d['satuan']; ?></small></td>
                <td class="text-center"><?php echo $d['jumlah']; ?></td>
                <td class="text-end"><?php echo rupiah($d['harga']); ?></td>
                <td class="text-end fw-semibold"><?php echo rupiah($d['subtotal']); ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="table-success"><td colspan="3" class="text-end fw-bold">TOTAL</td><td class="text-end fw-bold fs-5"><?php echo rupiah($trx['total']); ?></td></tr>
            </tfoot>
        </table>
        <div class="text-center text-muted mt-3" style="font-size:0.8rem;">
            Terima kasih telah berbelanja di Toko Bangunan Bukit Tunggal Jaya!
        </div>
    </div>
</div>
</div>
</div>
</main>
<?php include "footer.php"; ?>