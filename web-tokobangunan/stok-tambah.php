<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();
$produk_list = $db->query("SELECT id_produk, nama_produk, satuan FROM produk ORDER BY nama_produk");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->insert([
        'id_produk'  => $_POST['id_produk'],
        'jenis'      => $_POST['jenis'],
        'jumlah'     => intval($_POST['jumlah']),
        'referensi'  => $_POST['referensi'],
        'keterangan' => $_POST['keterangan'],
    ])->from_into('stok_mutasi')->create();
    header('Location: stok.php?success=Mutasi stok berhasil dicatat');
    exit;
}
include "header.php";
?>
<main id="main">
<div class="mb-4">
    <h4 class="page-title mb-0"><i class="bi bi-plus-circle me-2"></i>Input Mutasi Stok</h4>
    <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / <a href="stok.php">Stok</a> / Input</div>
</div>
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header bg-white py-3"><span class="fw-bold">Form Mutasi Stok (Masuk / Keluar)</span></div>
    <div class="card-body">
        <form method="post">
            <div class="mb-3">
                <label class="form-label fw-semibold">Produk <span class="text-danger">*</span></label>
                <select name="id_produk" class="form-select" required>
                    <option value="">-- Pilih Produk --</option>
                    <?php foreach ($produk_list as $p): ?>
                    <option value="<?php echo $p['id_produk']; ?>"><?php echo htmlspecialchars($p['nama_produk']); ?> (<?php echo $p['satuan']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Jenis Mutasi <span class="text-danger">*</span></label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="jenis" id="masuk" value="masuk" checked required>
                        <label class="form-check-label text-success fw-semibold" for="masuk"><i class="bi bi-arrow-down-circle me-1"></i>Stok Masuk</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="jenis" id="keluar" value="keluar">
                        <label class="form-check-label text-danger fw-semibold" for="keluar"><i class="bi bi-arrow-up-circle me-1"></i>Stok Keluar</label>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
                <input type="number" name="jumlah" class="form-control" min="1" placeholder="0" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Referensi (No. PO / Invoice / dll)</label>
                <input type="text" name="referensi" class="form-control" placeholder="Contoh: PO-2026-001">
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan..."></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-btj"><i class="bi bi-check-circle me-1"></i>Simpan</button>
                <a href="stok.php" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
</main>
<?php include "footer.php"; ?>