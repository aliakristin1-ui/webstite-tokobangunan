<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();
$stok = $db->query("SELECT * FROM view_stok_produk ORDER BY nama_produk ASC");
$mutasi = $db->query("SELECT sm.*, p.nama_produk FROM stok_mutasi sm JOIN produk p ON sm.id_produk=p.id_produk ORDER BY sm.tanggal DESC LIMIT 50");
include "header.php";
?>
<main id="main">
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-arrow-left-right me-2"></i>Mutasi Stok</h4>
        <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / Stok</div>
    </div>
<?php if (!isReadOnly()): ?>
<a href="stok-tambah.php" class="btn btn-btj">
    <i class="bi bi-plus-circle me-1"></i>Input Mutasi
</a>
<?php endif; ?>
</div>

<div class="card mb-4">
    <div class="card-header bg-white py-3"><span class="fw-bold text-dark"><i class="bi bi-boxes me-2 text-primary"></i>Stok Produk Saat Ini</span></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead><tr class="text-center">
                    <th>ID</th><th class="text-start">Nama Produk</th><th>Kategori</th><th>Satuan</th><th>Stok Akhir</th>
                </tr></thead>
                <tbody>
                <?php foreach ($stok as $s): ?>
                <tr class="text-center">
                    <td><?php echo $s['id_produk']; ?></td>
                    <td class="text-start"><?php echo htmlspecialchars($s['nama_produk']); ?></td>
                    <td><?php echo $s['kategori']; ?></td>
                    <td><?php echo $s['satuan']; ?></td>
                    <td>
                        <?php $stk = intval($s['stok_akkhir']); ?>
                        <span class="badge bg-<?php echo $stk<=0?'danger':($stk<=10?'warning':'success'); ?> fs-6">
                            <?php echo $stk; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Riwayat mutasi -->
<div class="card">
    <div class="card-header bg-white py-3"><span class="fw-bold text-dark"><i class="bi bi-clock-history me-2 text-warning"></i>Riwayat Mutasi (50 Terakhir)</span></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead><tr class="text-center">
                    <th>Tanggal</th><th class="text-start">Produk</th><th>Jenis</th><th>Jumlah</th><th>Referensi</th><th>Keterangan</th>
                </tr></thead>
                <tbody>
                <?php foreach ($mutasi as $m): ?>
                <tr class="text-center">
                    <td><?php echo tanggal(substr($m['tanggal']??'',0,10)); ?></td>
                    <td class="text-start"><?php echo htmlspecialchars($m['nama_produk']); ?></td>
                    <td><span class="badge bg-<?php echo $m['jenis']=='masuk'?'success':'danger'; ?>"><?php echo ucfirst($m['jenis']); ?></span></td>
                    <td class="fw-bold"><?php echo $m['jumlah']; ?></td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($m['referensi']); ?></small></td>
                    <td><small><?php echo htmlspecialchars($m['keterangan']); ?></small></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>
<?php include "footer.php"; ?>