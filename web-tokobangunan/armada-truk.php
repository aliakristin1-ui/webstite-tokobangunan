<?php
include "fungsi.php";
validasi();

// Hak akses: Gudang(4), Admin(2), Owner(1)
if (!isGudang()) {
    echo "<script>alert('Akses ditolak!');window.location='beranda.php';</script>";
    exit;
}

include "Dml.php";
$db = new Dml();

$aksi  = $_GET['aksi']  ?? '';
$id    = intval($_GET['id'] ?? 0);
$pesan = $_GET['pesan'] ?? '';
$error = '';

$bisa_aksi = isAdmin() && !isReadOnly(); 

// ——— PROSES FORM ———
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $bisa_aksi) {
    $nomor_polisi = trim($_POST['nomor_polisi'] ?? '');
    $kapasitas    = trim($_POST['kapasitas']    ?? '');
    $status       = $_POST['status']            ?? 'Selesai';

    if ($nomor_polisi === '' || $kapasitas === '') {
        $error = 'Nomor polisi dan kapasitas wajib diisi.';
    } else {
        $data = ['nomor_polisi' => $nomor_polisi, 'kapasitas' => $kapasitas, 'status' => $status];
        if ($id > 0) {
            $db->updateData($data)->from_into('armada_truk')->where("id_truk=$id")->set();
            header('Location: armada-truk.php?pesan=Data+truk+berhasil+diperbarui'); exit;
        } else {
            $db->insert($data)->from_into('armada_truk')->create();
            header('Location: armada-truk.php?pesan=Truk+berhasil+ditambahkan'); exit;
        }
    }
}

if ($aksi === 'hapus' && $id > 0 && $bisa_aksi) {
    $db->deleteData()->from_into('armada_truk')->where("id_truk=$id")->del();
    header('Location: armada-truk.php?pesan=Data+truk+berhasil+dihapus'); exit;
}

if (isReadOnly() && in_array($aksi, ['tambah', 'edit', 'hapus'])) {
    header('Location: armada-truk.php');
    exit;
}

$edit = null;
if ($aksi === 'edit' && $id > 0 && $bisa_aksi) {
    $edit = $db->queryOne("SELECT * FROM armada_truk WHERE id_truk=?", [$id]);
}

$truk_list = $db->query(
    "SELECT 
        at.*,
        COUNT(pg.id_pengiriman) AS jumlah_pesanan_aktif
     FROM armada_truk at
     LEFT JOIN pengiriman pg
        ON at.id_truk = pg.id_truk
        AND pg.status != 'Selesai'
     GROUP BY at.id_truk
     ORDER BY at.id_truk ASC"
);

include "header.php";
?>
<main id="main">
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-truck-front-fill me-2"></i>Armada Truk</h4>
        <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / Armada Truk</div>
    </div>
    <?php if ($bisa_aksi && $aksi !== 'tambah' && $aksi !== 'edit'): ?>
    <a href="?aksi=tambah" class="btn btn-btj">
        <i class="bi bi-plus-circle me-1"></i>Tambah Truk
    </a>
    <?php endif; ?>
</div>

<?php if (isReadOnly()): ?>
<div class="alert alert-info">
    <i class="bi bi-eye me-2"></i>
    Anda login sebagai <strong>Owner</strong> — halaman ini hanya dapat dilihat.
</div>
<?php endif; ?>

<?php if ($pesan): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i>
    <?php echo htmlspecialchars(urldecode($pesan)); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-circle me-2"></i>
    <?php echo htmlspecialchars($error); ?>
</div>
<?php endif; ?>

<!-- FORM TAMBAH / EDIT (Admin saja, bukan Owner) -->
<?php if ($bisa_aksi && ($aksi === 'tambah' || $aksi === 'edit')): ?>
<div class="card mb-4">
    <div class="card-header bg-white py-3 fw-bold">
        <i class="bi bi-<?php echo $edit ? 'pencil-square' : 'plus-circle'; ?> me-2 text-success"></i>
        <?php echo $edit ? 'Edit Data Truk' : 'Tambah Truk Baru'; ?>
    </div>
    <div class="card-body">
        <form method="POST" action="?aksi=<?php echo $aksi; ?>&id=<?php echo $id; ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nomor Polisi <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_polisi" class="form-control" placeholder="Contoh: DA 1234 AB"
                           value="<?php echo htmlspecialchars($edit['nomor_polisi'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kapasitas <span class="text-danger">*</span></label>
                    <input type="text" name="kapasitas" class="form-control" placeholder="Contoh: 2000 kg"
                           value="<?php echo htmlspecialchars($edit['kapasitas'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach (['Selesai','Sedang diproses','Dalam pengiriman'] as $s): ?>
                        <option value="<?php echo $s; ?>"
                            <?php echo ($edit['status'] ?? 'Selesai') === $s ? 'selected' : ''; ?>>
                            <?php echo $s; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-btj"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="armada-truk.php" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Batal</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- STATISTIK -->
<?php
$jml_tersedia = count(array_filter($truk_list, fn($t) => $t['status'] === 'Selesai'));
$jml_kirim    = count(array_filter($truk_list, fn($t) => $t['status'] === 'Dalam pengiriman'));
$jml_proses   = count(array_filter($truk_list, fn($t) => $t['status'] === 'Sedang diproses'));
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card bg-btj-green">
            <div class="stat-label">Tersedia</div>
            <div class="stat-val"><?php echo $jml_tersedia; ?></div>
            <i class="bi bi-check-circle stat-icon"></i>
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
        <div class="stat-card bg-btj-orange">
            <div class="stat-label">Sedang Diproses</div>
            <div class="stat-val"><?php echo $jml_proses; ?></div>
            <i class="bi bi-hourglass-split stat-icon"></i>
        </div>
    </div>
</div>

<!-- TABEL -->
<div class="card">
    <div class="card-header bg-white py-3 fw-bold">
        <i class="bi bi-list-ul me-2 text-primary"></i>Daftar Armada Truk
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th class="text-start">Nomor Polisi</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        <th>Pesanan Aktif</th>
                        <?php if ($bisa_aksi): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($truk_list)): ?>
                <tr>
                    <td colspan="<?php echo $bisa_aksi ? 6 : 5; ?>"
                        class="text-center text-muted py-4">
                        Belum ada data armada truk
                    </td>
                </tr>
                <?php else: foreach ($truk_list as $i => $t): ?>
                <tr class="text-center">
                    <td><?php echo $i + 1; ?></td>
                    <td class="text-start fw-bold">
                        <code><?php echo htmlspecialchars($t['nomor_polisi']); ?></code>
                    </td>
                    <td><?php echo htmlspecialchars($t['kapasitas']); ?></td>
                    <td><?php echo badge_status($t['status']); ?></td>
                    <td>
                        <?php if ((int)$t['jumlah_pesanan_aktif'] > 0): ?>
                        <span class="badge bg-primary">
                            <?php echo $t['jumlah_pesanan_aktif']; ?> pesanan
                        </span>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <?php if ($bisa_aksi): ?>
                    <td>
                        <a href="?aksi=edit&id=<?php echo $t['id_truk']; ?>"
                           class="btn btn-sm btn-outline-warning me-1">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="?aksi=hapus&id=<?php echo $t['id_truk']; ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Hapus data truk <?php echo htmlspecialchars($t['nomor_polisi']); ?>?')">
                            <i class="bi bi-trash"></i>
                        </a>
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