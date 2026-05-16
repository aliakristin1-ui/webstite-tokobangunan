<?php
include "fungsi.php";
validasi();

if (strtolower($_SESSION['nama_role']) != 'owner') {
    header("Location: beranda.php?error=Akses ditolak");
    exit;
}

include "Dml.php";
$db = new Dml();

$role  = (int)($_SESSION['id_role'] ?? 0);
$aksi  = $_GET['aksi']  ?? '';
$id    = intval($_GET['id'] ?? 0);
$pesan = $_GET['pesan'] ?? '';
$error = '';

$bisa_edit = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $bisa_edit) {
    $id_karyawan = intval($_POST['id_karyawan'] ?? 0);
    $tanggal     = trim($_POST['tanggal']     ?? '');
    $jumlah      = floatval(str_replace(['.', ','], ['', '.'], $_POST['jumlah'] ?? '0'));

    if ($id_karyawan < 1 || $tanggal === '' || $jumlah <= 0) {
        $error = 'Semua kolom wajib diisi dengan benar.';
    } else {
        $data = ['id_karyawan' => $id_karyawan, 'tanggal' => $tanggal, 'jumlah' => $jumlah];
        if ($id > 0) {
            $db->updateData($data)->from_into('penggajian')->where("id_penggajian=$id")->set();
            header('Location: penggajian.php?pesan=Data+penggajian+berhasil+diperbarui'); exit;
        } else {
            $db->insert($data)->from_into('penggajian')->create();
            header('Location: penggajian.php?pesan=Data+penggajian+berhasil+disimpan'); exit;
        }
    }
}

// Hapus
if ($aksi === 'hapus' && $id > 0 && $bisa_edit) {
    $db->deleteData()->from_into('penggajian')->where("id_penggajian=$id")->del();
    header('Location: penggajian.php?pesan=Data+penggajian+berhasil+dihapus'); exit;
}

// Data edit
$edit = null;
if ($aksi === 'edit' && $id > 0 && $bisa_edit) {
    $edit = $db->queryOne("SELECT * FROM penggajian WHERE id_penggajian=?", [$id]);
}

// Data karyawan (untuk dropdown)
$karyawan_list = $db->query("SELECT id_karyawan, nama, jabatan, gaji FROM karyawan ORDER BY nama ASC");

// Data penggajian
$gaji_list = $db->query(
    "SELECT p.id_penggajian, k.nama, k.jabatan, p.tanggal, p.jumlah
     FROM penggajian p
     JOIN karyawan k ON p.id_karyawan = k.id_karyawan
     ORDER BY p.tanggal DESC"
);

$total_bulan_ini = 0;
$bulan_ini = date('Y-m');
foreach ($gaji_list as $g) {
    if (substr($g['tanggal'], 0, 7) === $bulan_ini) {
        $total_bulan_ini += $g['jumlah'];
    }
}

include "header.php";
?>
<main id="main">
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-wallet2 me-2"></i>Penggajian Karyawan</h4>
        <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / Penggajian</div>
    </div>
    <?php if ($bisa_edit && $aksi !== 'tambah' && $aksi !== 'edit'): ?>
    <a href="?aksi=tambah" class="btn btn-btj"><i class="bi bi-plus-circle me-1"></i>Input Gaji</a>
    <?php endif; ?>
</div>

<?php if ($pesan): ?>
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars(urldecode($pesan)); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if (!$bisa_edit): ?>
<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Anda login sebagai <strong>Owner</strong> — hanya dapat melihat data penggajian.</div>
<?php endif; ?>

<!-- FORM TAMBAH / EDIT (Admin saja) -->
<?php if ($bisa_edit && ($aksi === 'tambah' || $aksi === 'edit')): ?>
<div class="card mb-4">
    <div class="card-header bg-white py-3 fw-bold">
        <i class="bi bi-<?php echo $edit ? 'pencil-square' : 'plus-circle'; ?> me-2 text-success"></i>
        <?php echo $edit ? 'Edit Data Gaji' : 'Input Gaji Baru'; ?>
    </div>
    <div class="card-body">
        <form method="POST" action="?aksi=<?php echo $aksi; ?>&id=<?php echo $id; ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                    <select name="id_karyawan" class="form-select" required id="selectKaryawan" onchange="isiGajiPokok()">
                        <option value="">-- Pilih Karyawan --</option>
                        <?php foreach ($karyawan_list as $k): ?>
                        <option value="<?php echo $k['id_karyawan']; ?>"
                                data-gaji="<?php echo $k['gaji']; ?>"
                                <?php echo ($edit['id_karyawan'] ?? '') == $k['id_karyawan'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($k['nama']); ?> — <?php echo htmlspecialchars($k['jabatan']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control"
                           value="<?php echo htmlspecialchars($edit['tanggal'] ?? date('Y-m-d')); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jumlah (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" class="form-control" id="inputJumlah"
                           placeholder="Contoh: 2500000" min="1"
                           value="<?php echo htmlspecialchars($edit['jumlah'] ?? ''); ?>" required>
                    <small class="text-muted">Gaji pokok karyawan akan otomatis terisi</small>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-btj"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="penggajian.php" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Batal</a>
            </div>
        </form>
    </div>
</div>
<script>
function isiGajiPokok() {
    const sel = document.getElementById('selectKaryawan');
    const opt = sel.options[sel.selectedIndex];
    const gaji = opt ? opt.dataset.gaji : '';
    if (gaji) document.getElementById('inputJumlah').value = gaji;
}
</script>
<?php endif; ?>

<!-- STATISTIK -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card bg-btj-green">
            <div class="stat-label">Total Karyawan</div>
            <div class="stat-val"><?php echo count($karyawan_list); ?></div>
            <i class="bi bi-person-badge stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-btj-blue">
            <div class="stat-label">Total Transaksi Gaji</div>
            <div class="stat-val"><?php echo count($gaji_list); ?></div>
            <i class="bi bi-receipt stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-btj-orange">
            <div class="stat-label">Total Gaji Bulan Ini</div>
            <div class="stat-val" style="font-size:1.3rem"><?php echo rupiah($total_bulan_ini); ?></div>
            <i class="bi bi-cash-coin stat-icon"></i>
        </div>
    </div>
</div>

<!-- TABEL -->
<div class="card">
    <div class="card-header bg-white py-3 fw-bold">
        <i class="bi bi-list-ul me-2 text-primary"></i>Riwayat Penggajian
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th class="text-start">Nama Karyawan</th>
                        <th>Jabatan</th>
                        <th>Tanggal</th>
                        <th>Jumlah Gaji</th>
                        <?php if ($bisa_edit): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($gaji_list)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data penggajian</td></tr>
                <?php else: foreach ($gaji_list as $i => $g): ?>
                <tr class="text-center">
                    <td><?php echo $i + 1; ?></td>
                    <td class="text-start fw-semibold"><?php echo htmlspecialchars($g['nama']); ?></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($g['jabatan']); ?></span></td>
                    <td><?php echo tanggal($g['tanggal']); ?></td>
                    <td class="fw-bold text-success"><?php echo rupiah($g['jumlah']); ?></td>
                    <?php if ($bisa_edit): ?>
                    <td>
                        <a href="?aksi=edit&id=<?php echo $g['id_penggajian']; ?>" class="btn btn-sm btn-outline-warning me-1">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="?aksi=hapus&id=<?php echo $g['id_penggajian']; ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Hapus data gaji ini?')">
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
