<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();
$id = intval($_GET['id'] ?? 0);
$produk = $db->queryOne("SELECT * FROM produk WHERE id_produk=?", [$id]);
if (!$produk) { header('Location: produk.php?error=Produk tidak ditemukan'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->updateData([
        'nama_produk' => $_POST['nama_produk'],
        'kategori'    => $_POST['kategori'],
        'satuan'      => $_POST['satuan'],
        'harga_beli'  => $_POST['harga_beli'],
        'harga_jual'  => $_POST['harga_jual'],
        'barcode'     => $_POST['barcode'],
    ])->from_into('produk')->where("id_produk=$id")->set();
    header('Location: produk.php?success=Produk berhasil diperbarui');
    exit;
}
include "header.php";
?>
<main id="main">
<div class="mb-4">
    <h4 class="page-title mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Produk</h4>
    <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / <a href="produk.php">Produk</a> / Edit</div>
</div>
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header bg-white py-3"><span class="fw-bold text-dark">Edit Produk: <?php echo htmlspecialchars($produk['nama_produk']); ?></span></div>
    <div class="card-body">
        <form method="post">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" value="<?php echo htmlspecialchars($produk['nama_produk']); ?>" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kategori</label>
                    <input type="text" name="kategori" class="form-control" value="<?php echo htmlspecialchars($produk['kategori']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Satuan</label>
                    <input type="text" name="satuan" class="form-control" value="<?php echo htmlspecialchars($produk['satuan']); ?>" required>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Harga Beli (Rp)</label>
                    <input type="number" name="harga_beli" class="form-control" value="<?php echo $produk['harga_beli']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Harga Jual (Rp)</label>
                    <input type="number" name="harga_jual" class="form-control" value="<?php echo $produk['harga_jual']; ?>" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Barcode</label>
                <input type="text" name="barcode" class="form-control" value="<?php echo htmlspecialchars($produk['barcode']); ?>" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-btj"><i class="bi bi-check-circle me-1"></i>Simpan Perubahan</button>
                <a href="produk.php" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
</main>
<?php include "footer.php"; ?>