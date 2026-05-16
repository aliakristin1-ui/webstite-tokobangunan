<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();

$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$kat  = isset($_GET['kat'])  ? trim($_GET['kat'])  : '';

$where = "1=1";
if ($cari) $where .= " AND (nama_produk LIKE '%$cari%' OR barcode LIKE '%$cari%')";
if ($kat)  $where .= " AND kategori='$kat'";

$produk = $db->query("SELECT p.*, COALESCE(SUM(CASE WHEN sm.jenis='masuk' THEN sm.jumlah WHEN sm.jenis='keluar' THEN -sm.jumlah END),0) as stok_saat_ini FROM produk p LEFT JOIN stok_mutasi sm ON p.id_produk=sm.id_produk WHERE $where GROUP BY p.id_produk ORDER BY p.id_produk ASC");
$kategori = $db->query("SELECT DISTINCT kategori FROM produk ORDER BY kategori");

include "header.php";
?>
<main id="main">
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="page-title mb-0"><i class="bi bi-box-seam-fill me-2"></i>Manajemen Produk</h4>
        <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / Produk</div>
    </div>
    <?php if (!isReadOnly()): ?>
<a href="produk-tambah.php" class="btn btn-btj">
    <i class="bi bi-plus-circle me-1"></i>Tambah Produk
</a>
<?php endif; ?>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="cari" class="form-control" placeholder="Cari nama produk / barcode..." value="<?php echo htmlspecialchars($cari); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="kat" class="form-select form-select-sm">
                    <option value="">-- Semua Kategori --</option>
                    <?php foreach ($kategori as $k): ?>
                    <option value="<?php echo $k['kategori']; ?>" <?php echo $kat==$k['kategori']?'selected':''; ?>><?php echo $k['kategori']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-auto">
                <button class="btn btn-sm btn-btj me-1"><i class="bi bi-funnel"></i> Filter</button>
                <a href="produk.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead><tr class="text-center">
                    <th>ID</th><th>Barcode</th><th>Nama Produk</th><th>Kategori</th><th>Satuan</th>
                    <th>Harga Beli</th><th>Harga Jual</th><th>Stok</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                <?php if (empty($produk)): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada produk ditemukan</td></tr>
                <?php else: foreach ($produk as $p): ?>
                <tr class="text-center">
                    <td><?php echo $p['id_produk']; ?></td>
                    <td><code><?php echo htmlspecialchars($p['barcode']); ?></code></td>
                    <td class="text-start fw-semibold"><?php echo htmlspecialchars($p['nama_produk']); ?></td>
                    <td><span class="badge bg-light text-dark border"><?php echo $p['kategori']; ?></span></td>
                    <td><?php echo $p['satuan']; ?></td>
                    <td><?php echo rupiah($p['harga_beli']); ?></td>
                    <td class="text-success fw-semibold"><?php echo rupiah($p['harga_jual']); ?></td>
                    <td>
                        <?php $stok = intval($p['stok_saat_ini']); ?>
                        <span class="badge bg-<?php echo $stok<=0?'danger':($stok<=10?'warning':'success'); ?>">
                            <?php echo $stok.' '.$p['satuan']; ?>
                        </span>
                    </td>
                    <td>
<?php if (!isReadOnly()): ?>
    <a href="produk-edit.php?id=<?php echo $p['id_produk']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>

    <a href="#"
       onclick="hapus('produk-hapus.php?id=<?php echo $p['id_produk']; ?>','<?php echo htmlspecialchars($p['nama_produk']); ?>')"
       class="btn btn-sm btn-outline-danger"
       title="Hapus">
       <i class="bi bi-trash"></i>
    </a>
<?php else: ?>
   
<?php endif; ?>
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