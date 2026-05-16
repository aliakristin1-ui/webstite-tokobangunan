<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();

$cari  = $_GET['cari'] ?? '';
$where = $cari
    ? "WHERE nama LIKE '%$cari%' OR no_hp LIKE '%$cari%' OR email LIKE '%$cari%'"
    : '';

$pelanggan = $db->query(
    "SELECT * FROM pelanggan $where ORDER BY id_pelanggan DESC"
);

$bisa_aksi = isAdmin() && !isReadOnly();

include "header.php";
?>
<main id="main">
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-people-fill me-2"></i>Data Pelanggan</h4>
        <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / Pelanggan</div>
    </div>
    <div class="d-flex gap-2">
        <form method="get" class="d-flex gap-2">
            <input type="text" name="cari" class="form-control form-control-sm"
                   placeholder="Cari pelanggan..."
                   value="<?php echo htmlspecialchars($cari); ?>">
            <button class="btn btn-sm btn-btj"><i class="bi bi-search"></i></button>
        </form>
    </div>
</div>

<?php if (isReadOnly()): ?>
<div class="alert alert-info">
    <i class="bi bi-eye me-2"></i>
    Anda login sebagai <strong>Owner</strong> — halaman ini hanya dapat dilihat.
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th class="text-start">Nama</th>
                        <th>No. HP</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <?php if ($bisa_aksi): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($pelanggan)): ?>
                <tr>
                    <td colspan="<?php echo $bisa_aksi ? 6 : 5; ?>"
                        class="text-center text-muted py-4">
                        Tidak ada pelanggan ditemukan
                    </td>
                </tr>
                <?php else: foreach ($pelanggan as $p): ?>
                <tr class="text-center">
                    <td><?php echo $p['id_pelanggan']; ?></td>
                    <td class="text-start fw-semibold">
                        <i class="bi bi-person-circle me-1 text-muted"></i>
                        <?php echo htmlspecialchars($p['nama']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($p['no_hp']); ?></td>
                    <td><?php echo htmlspecialchars($p['email']); ?></td>
                    <td>
                        <small class="text-muted">
                            <?php
                            $alamat = $p['alamat'];
                            echo htmlspecialchars(
                                substr($alamat, 0, 40) . (strlen($alamat) > 40 ? '…' : '')
                            );
                            ?>
                        </small>
                    </td>
                    <?php if ($bisa_aksi): ?>
                    <td>
                        <a href="pelanggan-edit.php?id=<?php echo $p['id_pelanggan']; ?>"
                           class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="#"
                           onclick="hapus('pelanggan-hapus.php?id=<?php echo $p['id_pelanggan']; ?>','<?php echo htmlspecialchars($p['nama']); ?>')"
                           class="btn btn-sm btn-outline-danger">
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