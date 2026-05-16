<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();

// Owner hanya bisa lihat, tidak bisa ubah status
$bisa_aksi = !isReadOnly();

// Mapping status pengiriman → status pesanan online
function map_status_pesanan(string $status_pengiriman): string {
    return match ($status_pengiriman) {
        'Sedang diproses'  => 'diproses',
        'Dalam pengiriman' => 'dikirim',
        'Selesai'          => 'selesai',
        default            => ''
    };
}

// Update status — hanya jika bukan Owner
if (!isReadOnly() && ($_GET['action'] ?? '') === 'update') {
    $id_pg       = intval($_GET['id']);
    $status_baru = $_GET['status'] ?? '';

    // 1. Update status pengiriman
    $db->updateData(['status' => $status_baru])
       ->from_into('pengiriman')
       ->where("id_pengiriman=$id_pg")
       ->set();

    // 2. Sinkronisasi status pesanan online
    $status_pesanan = map_status_pesanan($status_baru);
    if ($status_pesanan !== '') {
        $pg_row = $db->queryOne(
            "SELECT id_pesanan FROM pengiriman WHERE id_pengiriman=?",
            [$id_pg]
        );
        if ($pg_row && !empty($pg_row['id_pesanan'])) {
            $db->updateData(['status' => $status_pesanan])
               ->from_into('pesanan_online')
               ->where("id_pesanan=" . intval($pg_row['id_pesanan']))
               ->set();
        }
    }

    // 3. Jika selesai, update truk — tapi hanya jika truk tidak punya pesanan aktif lain
    if ($status_baru === 'Selesai') {
        $pg = $db->queryOne(
            "SELECT id_truk FROM pengiriman WHERE id_pengiriman=?",
            [$id_pg]
        );
        if ($pg) {
            $masih_aktif = $db->queryOne(
                "SELECT COUNT(*) as jml FROM pengiriman
                 WHERE id_truk=? AND id_pengiriman!=? AND status!='Selesai'",
                [$pg['id_truk'], $id_pg]
            );
            if ((int)($masih_aktif['jml'] ?? 0) === 0) {
                // Tidak ada pesanan lain yang masih aktif → truk kembali Selesai
                $db->updateData(['status' => 'Selesai'])
                   ->from_into('armada_truk')
                   ->where("id_truk=" . $pg['id_truk'])
                   ->set();
            }
        }
    }

    header('Location: pengiriman.php?success=Status+pengiriman+diperbarui');
    exit;
}

$pengiriman = $db->query(
    "SELECT * FROM view_pengiriman ORDER BY tanggal_kirim DESC"
);

include "header.php";
?>
<main id="main">
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-truck me-2"></i>Manajemen Pengiriman</h4>
        <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / Pengiriman</div>
    </div>
    <?php if ($bisa_aksi): ?>
    <a href="pengiriman-tambah.php" class="btn btn-btj">
        <i class="bi bi-plus-circle me-1"></i>Buat Pengiriman
    </a>
    <?php endif; ?>
</div>

<?php if (isReadOnly()): ?>
<div class="alert alert-info">
    <i class="bi bi-eye me-2"></i>
    Anda login sebagai <strong>Owner</strong> — halaman ini hanya dapat dilihat.
</div>
<?php endif; ?>

<?php if (!empty($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i>
    <?php echo htmlspecialchars(urldecode($_GET['success'])); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-3 mb-4">
<?php
$jml_proses  = count(array_filter($pengiriman, fn($x) => $x['status'] === 'Sedang diproses'));
$jml_kirim   = count(array_filter($pengiriman, fn($x) => $x['status'] === 'Dalam pengiriman'));
$jml_selesai = count(array_filter($pengiriman, fn($x) => $x['status'] === 'Selesai'));
?>
    <div class="col-md-4">
        <div class="stat-card bg-btj-orange">
            <div class="stat-label">Sedang Diproses</div>
            <div class="stat-val"><?php echo $jml_proses; ?></div>
            <i class="bi bi-hourglass-split stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-btj-blue">
            <div class="stat-label">Dalam Pengiriman</div>
            <div class="stat-val"><?php echo $jml_kirim; ?></div>
            <i class="bi bi-truck stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-btj-green">
            <div class="stat-label">Selesai</div>
            <div class="stat-val"><?php echo $jml_selesai; ?></div>
            <i class="bi bi-check-circle stat-icon"></i>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th class="text-start">Pelanggan</th>
                        <th>Truk (Polisi)</th>
                        <th>Tgl Kirim</th>
                        <th>Status</th>
                        <?php if ($bisa_aksi): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($pengiriman)): ?>
                <tr>
                    <td colspan="<?php echo $bisa_aksi ? 6 : 5; ?>"
                        class="text-center text-muted py-4">
                        Belum ada data pengiriman
                    </td>
                </tr>
                <?php else: foreach ($pengiriman as $p): ?>
                <tr class="text-center">
                    <td><?php echo $p['id_pengiriman']; ?></td>
                    <td class="text-start fw-semibold">
                        <?php echo htmlspecialchars($p['pelanggan']); ?>
                    </td>
                    <td><code><?php echo htmlspecialchars($p['nomor_polisi']); ?></code></td>
                    <td><?php echo tanggal($p['tanggal_kirim']); ?></td>
                    <td><?php echo badge_status($p['status']); ?></td>
                    <?php if ($bisa_aksi): ?>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                Ubah Status
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item"
                                       href="?action=update&id=<?php echo $p['id_pengiriman']; ?>&status=Sedang+diproses">
                                        <i class="bi bi-hourglass me-2"></i>Sedang diproses
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="?action=update&id=<?php echo $p['id_pengiriman']; ?>&status=Dalam+pengiriman">
                                        <i class="bi bi-truck me-2"></i>Dalam pengiriman
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-success"
                                       href="?action=update&id=<?php echo $p['id_pengiriman']; ?>&status=Selesai">
                                        <i class="bi bi-check-circle me-2"></i>Selesai
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>
<?php include "footer.php"; ?>