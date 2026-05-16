<?php
include "fungsi.php";
validasi();
include "Dml.php";
$db = new Dml();
$produk_list = $db->query("SELECT p.id_produk, p.nama_produk, p.harga_jual, p.satuan, COALESCE(SUM(CASE WHEN sm.jenis='masuk' THEN sm.jumlah WHEN sm.jenis='keluar' THEN -sm.jumlah END),0) as stok FROM produk p LEFT JOIN stok_mutasi sm ON p.id_produk=sm.id_produk GROUP BY p.id_produk HAVING stok > 0 ORDER BY p.nama_produk");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_karyawan = $_SESSION['id_karyawan'];
    $metode = $_POST['metode_pembayaran'];
    
    // Insert transaksi header
    $db->execute("INSERT INTO transaksi (tanggal, total, metode_pembayaran, id_kasir) VALUES (NOW(), 0, ?, ?)", [$metode, $id_karyawan]);
    $id_transaksi = $db->queryOne("SELECT LAST_INSERT_ID() as id")['id'];
    
    $total = 0;
    $items = $_POST['items'] ?? [];
    foreach ($items as $item) {
        if (empty($item['id_produk']) || empty($item['jumlah'])) continue;
        $harga = floatval($item['harga']);
        $jumlah = intval($item['jumlah']);
        $subtotal = $harga * $jumlah;
        $total += $subtotal;
        $db->execute("INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga, subtotal) VALUES (?, ?, ?, ?, ?)",
            [$id_transaksi, $item['id_produk'], $jumlah, $harga, $subtotal]);
        // Kurangi stok
        $db->insert([
            'id_produk'  => $item['id_produk'],
            'jenis'      => 'keluar',
            'jumlah'     => $jumlah,
            'referensi'  => 'TRX-'.$id_transaksi,
            'keterangan' => 'Penjualan kasir',
        ])->from_into('stok_mutasi')->create();
    }
    $db->execute("UPDATE transaksi SET total=? WHERE id_transaksi=?", [$total, $id_transaksi]);
    header("Location: transaksi-detail.php?id=$id_transaksi&success=Transaksi berhasil disimpan");
    exit;
}
include "header.php";
?>
<main id="main">
<div class="mb-4">
    <h4 class="page-title mb-0"><i class="bi bi-cart-plus me-2"></i>Transaksi Baru (POS)</h4>
    <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / <a href="transaksi.php">Transaksi</a> / Baru</div>
</div>

<div class="row g-3">
<!-- PRODUK PICKER -->
<div class="col-lg-5">
    <div class="card">
        <div class="card-header bg-white py-3"><span class="fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Pilih Produk</span></div>
        <div class="card-body" style="max-height:500px;overflow-y:auto;">
            <input type="text" id="cariProduk" class="form-control form-control-sm mb-3" placeholder="Cari produk...">
            <div id="produkGrid" class="row g-2">
                <?php foreach ($produk_list as $p): ?>
                <div class="col-6 produk-item" data-nama="<?php echo strtolower($p['nama_produk']); ?>">
                    <div class="card border" onclick="tambahItem(<?php echo $p['id_produk']; ?>,'<?php echo addslashes($p['nama_produk']); ?>',<?php echo $p['harga_jual']; ?>,'<?php echo $p['satuan']; ?>')" style="cursor:pointer;transition:0.2s;" onmouseover="this.style.borderColor='#1a6b3c'" onmouseout="this.style.borderColor=''">
                        <div class="card-body p-2 text-center">
                            <div class="text-primary" style="font-size:1.5rem;"><i class="bi bi-box-seam"></i></div>
                            <div style="font-size:0.78rem;font-weight:600;"><?php echo htmlspecialchars($p['nama_produk']); ?></div>
                            <div class="text-success" style="font-size:0.75rem;font-weight:700;"><?php echo rupiah($p['harga_jual']); ?></div>
                            <small class="text-muted">Stok: <?php echo $p['stok']; ?> <?php echo $p['satuan']; ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- CART -->
<div class="col-lg-7">
    <div class="card">
        <div class="card-header bg-white py-3">
            <span class="fw-bold"><i class="bi bi-cart3 me-2 text-success"></i>Keranjang Belanja</span>
        </div>
        <div class="card-body">
            <form method="post" id="formTransaksi">
                <div class="table-responsive mb-3" style="min-height:200px;">
                    <table class="table table-sm mb-0" id="tblCart">
                        <thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
                        <tbody id="cartBody">
                            <tr id="emptyRow"><td colspan="5" class="text-center text-muted py-4">Belum ada produk dipilih</td></tr>
                        </tbody>
                    </table>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold fs-5">Total:</span>
                    <span class="fw-bold fs-4 text-success" id="totalDisplay">Rp 0</span>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Metode Pembayaran</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check"><input class="form-check-input" type="radio" name="metode_pembayaran" value="Tunai" checked required><label class="form-check-label fw-semibold text-success"><i class="bi bi-cash me-1"></i>Tunai</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="metode_pembayaran" value="Transfer"><label class="form-check-label fw-semibold text-info"><i class="bi bi-bank me-1"></i>Transfer</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="metode_pembayaran" value="QRIS"><label class="form-check-label fw-semibold text-warning"><i class="bi bi-qr-code me-1"></i>QRIS</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="metode_pembayaran" value="Debit"><label class="form-check-label fw-semibold text-primary"><i class="bi bi-credit-card me-1"></i>Debit</label></div>
                    </div>
                </div>
                <div id="itemsContainer"></div>
                <div class="d-flex gap-2">
                    <button type="button" onclick="submitTransaksi()" class="btn btn-btj btn-lg flex-fill"><i class="bi bi-check-circle me-2"></i>Simpan Transaksi</button>
                    <a href="transaksi.php" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</main>
<?php include "footer.php"; ?>
<script>
let cart = {};
function tambahItem(id, nama, harga, satuan) {
    if (cart[id]) { cart[id].qty++; }
    else { cart[id] = { id, nama, harga, satuan, qty: 1 }; }
    renderCart();
}
function hapusItem(id) { delete cart[id]; renderCart(); }
function ubahQty(id, val) { cart[id].qty = Math.max(1, parseInt(val)||1); renderCart(); }
function renderCart() {
    const tbody = document.getElementById('cartBody');
    const empty = document.getElementById('emptyRow');
    const container = document.getElementById('itemsContainer');
    const keys = Object.keys(cart);
    if (keys.length === 0) {
        tbody.innerHTML = '<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-4">Belum ada produk dipilih</td></tr>';
        document.getElementById('totalDisplay').textContent = 'Rp 0';
        container.innerHTML = '';
        return;
    }
    let rows = ''; let total = 0; let hidden = '';
    keys.forEach((id, i) => {
        const item = cart[id];
        const sub = item.harga * item.qty;
        total += sub;
        rows += `<tr>
            <td><small>${item.nama}</small></td>
            <td><small>${item.harga.toLocaleString('id-ID')}</small></td>
            <td><input type="number" value="${item.qty}" min="1" onchange="ubahQty(${id},this.value)" class="form-control form-control-sm" style="width:65px;"></td>
            <td><small class="text-success fw-bold">${sub.toLocaleString('id-ID')}</small></td>
            <td><button type="button" onclick="hapusItem(${id})" class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button></td>
        </tr>`;
        hidden += `<input type="hidden" name="items[${i}][id_produk]" value="${id}">
                   <input type="hidden" name="items[${i}][jumlah]" value="${item.qty}">
                   <input type="hidden" name="items[${i}][harga]" value="${item.harga}">`;
    });
    tbody.innerHTML = rows;
    document.getElementById('totalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
    container.innerHTML = hidden;
}
function submitTransaksi() {
    if (Object.keys(cart).length === 0) { Swal.fire('Perhatian','Keranjang masih kosong!','warning'); return; }
    document.getElementById('formTransaksi').submit();
}
document.getElementById('cariProduk').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.produk-item').forEach(el => {
        el.style.display = el.dataset.nama.includes(q) ? '' : 'none';
    });
});
</script>