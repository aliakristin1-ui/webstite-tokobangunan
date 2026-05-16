<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();
$pesanan_list = $db->query("
    SELECT 
        po.id_pesanan,
        pl.nama,
        pl.alamat
    FROM pesanan_online po
    JOIN pelanggan pl 
    ON po.id_pelanggan = pl.id_pelanggan

    WHERE po.id_pesanan NOT IN (
        SELECT id_pesanan FROM pengiriman
    )

    ORDER BY po.id_pesanan DESC
");
$truk_list = $db->query("SELECT * FROM armada_truk ORDER BY nomor_polisi");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->insert([
        'id_pesanan'    => $_POST['id_pesanan'],
        'alamat'        => $_POST['alamat'],
        'id_truk'       => $_POST['id_truk'],
        'tanggal_kirim' => $_POST['tanggal_kirim'],
        'status'        => 'Sedang diproses',
    ])->from_into('pengiriman')->create();
    
    // Update status pesanan
    $db->updateData(['status'=>'dikirim'])
        ->from_into('pesanan_online')
        ->where("id_pesanan=".$_POST['id_pesanan'])
        ->set();
    header('Location: pengiriman.php?success=Pengiriman berhasil dijadwalkan'); exit;
}
include "header.php";
?>
<main id="main">
<div class="mb-4">
    <h4 class="page-title mb-0"><i class="bi bi-truck me-2"></i>Buat Pengiriman Baru</h4>
    <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / <a href="pengiriman.php">Pengiriman</a> / Buat</div>
</div>
<div class="row justify-content-center"><div class="col-lg-6"><div class="card">
<div class="card-header bg-white py-3"><span class="fw-bold">Form Pengiriman Barang</span></div>
<div class="card-body">
 <form method="post" enctype="multipart/form-data">
<div class="mb-3">
    <label class="form-label fw-semibold">Pesanan Online <span class="text-danger">*</span></label>
    <select 
        name="id_pesanan"
        id="id_pesanan"
        class="form-select"
        onchange="isiAlamat()"
        required>

        <option value="">-- Pilih Pesanan --</option>

        <?php foreach ($pesanan_list as $ps): ?>

        <option 
            value="<?php echo $ps['id_pesanan']; ?>"
            data-alamat="<?php echo htmlspecialchars((string)($ps['alamat'] ?? '')); ?>">

            #<?php echo $ps['id_pesanan']; ?> - 
            <?php echo htmlspecialchars((string)($ps['nama'] ?? 'Tanpa Nama')); ?>

        </option>
        <?php endforeach; ?>

    </select>
    <?php if (empty($pesanan_list)): ?><small class="text-muted">Tidak ada pesanan pending</small><?php endif; ?>
</div>
<div class="mb-3">
    <label class="form-label fw-semibold">
        Alamat Pengiriman <span class="text-danger"></span>
    </label>

    <input 
        type="text"
        name="alamat"
        id="alamat"
        class="form-control"
        placeholder="Alamat tujuan pengiriman"
        required>
</div>
<div class="mb-3">
    <label class="form-label fw-semibold">Armada Truk <span class="text-danger">*</span></label>
    <select name="id_truk" class="form-select" required>
        <option value="">-- Pilih Truk --</option>
        <?php foreach ($truk_list as $t): ?>
        <option value="<?php echo $t['id_truk']; ?>"><?php echo htmlspecialchars($t['nomor_polisi']); ?> (<?php echo $t['kapasitas']; ?>)</option>
        <?php endforeach; ?>
    </select>
</div>
<div class="mb-4">
    <label class="form-label fw-semibold">Tanggal Kirim <span class="text-danger">*</span></label>
    <input type="date" name="tanggal_kirim" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
</div>
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-btj"><i class="bi bi-truck me-1"></i>Jadwalkan Pengiriman</button>
    <a href="pengiriman.php" class="btn btn-outline-secondary">Batal</a>
</div>
</form>
</div>
</div></div></div>
</main>
<script>
function isiAlamat() {

    const select = document.getElementById('id_pesanan');

    const alamat =
        select.options[select.selectedIndex]
        .getAttribute('data-alamat');

    document.getElementById('alamat').value =
        alamat ?? '';
}
</script>
<?php include "footer.php"; ?>