<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();

// Stats
$tot_produk    = $db->queryOne("SELECT COUNT(*) as c FROM produk")['c'] ?? 0;
$tot_pelanggan = $db->queryOne("SELECT COUNT(*) as c FROM pelanggan")['c'] ?? 0;
$tot_karyawan  = $db->queryOne("SELECT COUNT(*) as c FROM karyawan")['c'] ?? 0;
$tot_truk      = $db->queryOne("SELECT COUNT(*) as c FROM armada_truk")['c'] ?? 0;
$penjualan_hari= $db->queryOne("SELECT COALESCE(SUM(d.subtotal),0) as total FROM transaksi t JOIN detail_transaksi d ON t.id_transaksi=d.id_transaksi WHERE DATE(t.tanggal)=CURDATE()")['total'] ?? 0;
$penjualan_bln = $db->queryOne("SELECT COALESCE(SUM(d.subtotal),0) as total FROM transaksi t JOIN detail_transaksi d ON t.id_transaksi=d.id_transaksi WHERE MONTH(t.tanggal)=MONTH(CURDATE()) AND YEAR(t.tanggal)=YEAR(CURDATE())")['total'] ?? 0;
$pesanan_pending= $db->queryOne("SELECT COUNT(*) as c FROM pesanan_online WHERE status='pending'")['c'] ?? 0;
$pengiriman_proses=$db->queryOne("SELECT COUNT(*) as c FROM pengiriman WHERE status='Dalam pengiriman'")['c'] ?? 0;
$laba_rugi     = $db->queryOne("SELECT * FROM view_laba_rugi") ?? ['total_pemasukan'=>0,'total_pengeluaran'=>0,'laba_bersih'=>0];

// Chart: penjualan 7 hari terakhir
$chart_data = $db->query("SELECT DATE(t.tanggal) as tgl, COALESCE(SUM(d.subtotal),0) as total FROM transaksi t JOIN detail_transaksi d ON t.id_transaksi=d.id_transaksi WHERE t.tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(t.tanggal) ORDER BY tgl ASC");
$chart_labels = json_encode(array_column($chart_data, 'tgl'));
$chart_values = json_encode(array_map('floatval', array_column($chart_data, 'total')));

// Produk terlaris
$terlaris = $db->query("SELECT * FROM view_produk_terlaris LIMIT 5");

// Transaksi terbaru
$transaksi_baru = $db->query("SELECT t.id_transaksi, t.tanggal, k.nama as kasir, SUM(d.subtotal) as total FROM transaksi t JOIN karyawan k ON t.id_kasir=k.id_karyawan JOIN detail_transaksi d ON t.id_transaksi=d.id_transaksi GROUP BY t.id_transaksi ORDER BY t.tanggal DESC LIMIT 5");

// Pesanan online terbaru
$pesanan_baru = $db->query("SELECT * FROM view_pesanan_online ORDER BY tanggal DESC LIMIT 5");

// Stok menipis
$stok_tipis = $db->query("SELECT * FROM view_stok_produk WHERE stok_akkhir <= 10 ORDER BY stok_akkhir ASC LIMIT 5");

include "header.php";
?>
<main id="main">
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
        <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / Dashboard</div>
    </div>
    <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?php echo tanggal(date('Y-m-d')); ?></div>
</div>

<!-- STAT CARDS ROW 1 -->
<div class="row g-3 mb-3">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card bg-btj-green">
            <div class="stat-label">Penjualan Hari Ini</div>
            <div class="stat-val" style="font-size:1.1rem;"><?php echo rupiah($penjualan_hari); ?></div>
            <i class="bi bi-cash-stack stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card bg-btj-blue">
            <div class="stat-label">Penjualan Bulan Ini</div>
            <div class="stat-val" style="font-size:1.1rem;"><?php echo rupiah($penjualan_bln); ?></div>
            <i class="bi bi-graph-up stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card bg-btj-orange">
            <div class="stat-label">Pesanan Pending</div>
            <div class="stat-val"><?php echo $pesanan_pending; ?></div>
            <i class="bi bi-bag-check stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card bg-btj-teal">
            <div class="stat-label">Dalam Pengiriman</div>
            <div class="stat-val"><?php echo $pengiriman_proses; ?></div>
            <i class="bi bi-truck stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card bg-btj-purple">
            <div class="stat-label">Total Produk</div>
            <div class="stat-val"><?php echo $tot_produk; ?></div>
            <i class="bi bi-box-seam stat-icon"></i>
        </div>
    </div>

<!-- LABA RUGI SUMMARY -->
<?php if (strtolower($_SESSION['nama_role']) == 'owner'): ?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100 border-start border-success border-3">
            <div class="card-body">
                <div class="text-muted small fw-semibold mb-1"><i class="bi bi-arrow-down-circle-fill text-success me-1"></i>Total Pemasukan</div>
                <div class="fw-bold fs-5 text-success"><?php echo rupiah($laba_rugi['total_pemasukan'] ?? 0); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-start border-danger border-3">
            <div class="card-body">
                <div class="text-muted small fw-semibold mb-1"><i class="bi bi-arrow-up-circle-fill text-danger me-1"></i>Total Pengeluaran</div>
                <div class="fw-bold fs-5 text-danger"><?php echo rupiah($laba_rugi['total_pengeluaran'] ?? 0); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <?php $lb = floatval($laba_rugi['laba_bersih'] ?? 0); ?>
        <div class="card h-100 border-start border-<?php echo $lb>=0?'primary':'warning'; ?> border-3">
            <div class="card-body">
                <div class="text-muted small fw-semibold mb-1"><i class="bi bi-wallet-fill text-primary me-1"></i>Laba Bersih</div>
                <div class="fw-bold fs-5 text-<?php echo $lb>=0?'primary':'danger'; ?>"><?php echo rupiah($lb); ?></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- CHART + PRODUK TERLARIS -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold text-dark"><i class="bi bi-bar-chart-line-fill text-success me-2"></i>Penjualan 7 Hari Terakhir</span>
            </div>
            <div class="card-body">
                <div id="chartPenjualan"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark"><i class="bi bi-trophy-fill text-warning me-2"></i>Produk Terlaris</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($terlaris)): ?>
                <div class="text-center text-muted p-4">Belum ada data penjualan</div>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($terlaris as $i => $p): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <span class="badge bg-<?php echo ['warning','secondary','danger','info','light text-dark'][$i]??'secondary'; ?> me-2"><?php echo $i+1; ?></span>
                            <?php echo htmlspecialchars($p['nama_produk']); ?>
                        </span>
                        <span class="badge bg-success"><?php echo number_format($p['total_terjual']); ?> unit</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- TRANSAKSI TERBARU + PESANAN + STOK TIPIS -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold text-dark"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Transaksi Terbaru</span>
                <a href="transaksi.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr class="text-center"><th>#</th><th>Kasir</th><th>Tanggal</th><th>Total</th></tr></thead>
                        <tbody>
                        <?php if (empty($transaksi_baru)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada transaksi</td></tr>
                        <?php else: foreach ($transaksi_baru as $t): ?>
                            <tr class="text-center">
                                <td><small class="text-muted">#<?php echo $t['id_transaksi']; ?></small></td>
                                <td><?php echo htmlspecialchars($t['kasir']); ?></td>
                                <td><small><?php echo tanggal(substr($t['tanggal'],0,10)); ?></small></td>
                                <td class="text-success fw-semibold"><?php echo rupiah($t['total']); ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold text-dark"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Stok Menipis (&le;10)</span>
                <a href="stok.php" class="btn btn-sm btn-outline-danger">Kelola Stok</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr class="text-center"><th>Produk</th><th>Kategori</th><th>Stok</th></tr></thead>
                        <tbody>
                        <?php if (empty($stok_tipis)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-3"><i class="bi bi-check-circle text-success me-1"></i>Semua stok aman</td></tr>
                        <?php else: foreach ($stok_tipis as $s): ?>
                            <tr class="text-center">
                                <td class="text-start"><?php echo htmlspecialchars($s['nama_produk']); ?></td>
                                <td><small class="badge bg-light text-dark"><?php echo $s['kategori']; ?></small></td>
                                <td><span class="badge bg-<?php echo $s['stok_akkhir']<=0?'danger':($s['stok_akkhir']<=5?'warning':'info'); ?>"><?php echo $s['stok_akkhir']; ?> <?php echo $s['satuan']; ?></span></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PESANAN ONLINE TERBARU -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-bold text-dark"><i class="bi bi-bag-check-fill text-info me-2"></i>Pesanan Online Terbaru</span>
        <a href="pesanan-online.php" class="btn btn-sm btn-outline-info">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr class="text-center"><th>#</th><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                <?php if (empty($pesanan_baru)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada pesanan online</td></tr>
                <?php else: foreach ($pesanan_baru as $p): ?>
                    <tr class="text-center">
                        <td>#<?php echo $p['id_pesanan']; ?></td>
                        <td><?php echo htmlspecialchars($p['pelanggan']); ?></td>
                        <td><small><?php echo tanggal(substr($p['tanggal']??'',0,10)); ?></small></td>
                        <td class="text-success fw-semibold"><?php echo rupiah($p['total']); ?></td>
                        <td><?php echo badge_status($p['status']); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</main>
<?php include "footer.php"; ?>
<script>
var options = {
    chart: { type: 'area', height: 250, toolbar: { show: false }, fontFamily: 'Nunito, sans-serif' },
    series: [{ name: 'Penjualan (Rp)', data: <?php echo $chart_values ?: '[0]'; ?> }],
    xaxis: { categories: <?php echo $chart_labels ?: '["Hari Ini"]'; ?>, labels: { style: { fontSize: '11px' } } },
    yaxis: { labels: { formatter: v => 'Rp '+v.toLocaleString('id-ID') } },
    colors: ['#1a6b3c'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05 } },
    stroke: { curve: 'smooth', width: 3 },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f0f0f0' },
    tooltip: { y: { formatter: v => 'Rp '+v.toLocaleString('id-ID') } }
};
new ApexCharts(document.querySelector("#chartPenjualan"), options).render();
</script>