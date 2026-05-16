<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();
$transaksi = $db->query("SELECT t.id_transaksi, t.tanggal, k.nama as kasir, t.metode_pembayaran, SUM(d.subtotal) as total FROM transaksi t JOIN karyawan k ON t.id_kasir=k.id_karyawan JOIN detail_transaksi d ON t.id_transaksi=d.id_transaksi GROUP BY t.id_transaksi ORDER BY t.tanggal DESC");
include "header.php";
?>
<main id="main">
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-receipt me-2"></i>Transaksi POS (Point of Sale)</h4>
        <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / Transaksi</div>
    </div>
    <?php if (!isReadOnly()): ?>
<a href="transaksi-tambah.php" class="btn btn-btj">
    <i class="bi bi-plus-circle me-1"></i>Transaksi Baru
</a>
<?php endif; ?>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead><tr class="text-center">
                    <th>#ID</th><th>Tanggal</th><th>Kasir</th><th>Metode Bayar</th><th>Total</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                <?php if (empty($transaksi)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada transaksi</td></tr>
                <?php else: foreach ($transaksi as $t): ?>
                <tr class="text-center">
                    <td><span class="badge bg-primary">#<?php echo $t['id_transaksi']; ?></span></td>
                    <td><?php echo tanggal(substr($t['tanggal']??'',0,10)); ?><br><small class="text-muted"><?php echo substr($t['tanggal']??'',11,5); ?></small></td>
                    <td><?php echo htmlspecialchars($t['kasir']); ?></td>
                    <td><span class="badge bg-<?php echo $t['metode_pembayaran']=='Tunai'?'success':($t['metode_pembayaran']=='Transfer'?'info':'warning'); ?>"><?php echo $t['metode_pembayaran']; ?></span></td>
                    <td class="text-success fw-bold"><?php echo rupiah($t['total']); ?></td>
                    <td>
                        <a href="transaksi-detail.php?id=<?php echo $t['id_transaksi']; ?>" class="btn btn-sm btn-outline-primary" title="Detail">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>
<?php include "footer.php"; ?>