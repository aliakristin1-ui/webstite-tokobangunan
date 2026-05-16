<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();

$pesanan = $db->query("SELECT * FROM view_pesanan_online ORDER BY tanggal DESC");
include "header.php";
?>
<main id="main">
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-bag-check-fill me-2"></i>Pesanan Online</h4>
        <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / Pesanan Online</div>
    </div>

</div>

<!-- Filter status -->
<div class="d-flex gap-2 mb-3 flex-wrap">

    <?php $sf = $_GET['sf'] ?? ''; ?>

    <a href="pesanan-online.php"
       class="btn btn-sm <?php echo !$sf ? 'btn-btj' : 'btn-outline-secondary'; ?>">
       Semua
    </a>

    <a href="?sf=pending"
       class="btn btn-sm <?php echo $sf=='pending' ? 'btn-warning' : 'btn-outline-warning'; ?>">
       Pending
    </a>

    <a href="?sf=diproses"
       class="btn btn-sm <?php echo $sf=='diproses' ? 'btn-info' : 'btn-outline-info'; ?>">
       Diproses
    </a>

    <a href="?sf=dikirim"
       class="btn btn-sm <?php echo $sf=='dikirim' ? 'btn-primary' : 'btn-outline-primary'; ?>">
       Dikirim
    </a>

    <a href="?sf=selesai"
       class="btn btn-sm <?php echo $sf=='selesai' ? 'btn-success' : 'btn-outline-success'; ?>">
       Selesai
    </a>

    <a href="?sf=dibatalkan"
       class="btn btn-sm <?php echo $sf=='dibatalkan' ? 'btn-danger' : 'btn-outline-danger'; ?>">
       Dibatalkan
    </a>

</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead><tr class="text-center">
                    <th>#</th><th class="text-start">Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                <?php
                $filtered = $sf ? array_filter($pesanan, fn($p) => $p['status'] === $sf) : $pesanan;
                if (empty($filtered)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada pesanan</td></tr>
                <?php else: foreach ($filtered as $p): ?>
                <tr class="text-center">
                    <td><span class="badge bg-info">#<?php echo $p['id_pesanan']; ?></span></td>
                    <td class="text-start fw-semibold"><i class="bi bi-person me-1 text-muted"></i><?php echo htmlspecialchars($p['pelanggan']); ?></td>
                    <td><?php echo tanggal(substr($p['tanggal']??'',0,10)); ?></td>
                    <td class="text-success fw-bold"><?php echo rupiah($p['total']); ?></td>
                    <td><?php echo badge_status($p['status']); ?></td>
                    <td>
                        <a href="pesanan-detail.php?id=<?php echo $p['id_pesanan']; ?>" 
                        class="btn btn-sm btn-outline-primary" 
                        title="Detail">
                        <i class="bi bi-eye"></i>
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