<?php
include "fungsi.php";
validasi();
if (strtolower($_SESSION['nama_role']) != 'owner') {
    header("Location: beranda.php?error=Akses ditolak");
    exit;
}   

include "Dml.php";
$db = new Dml();

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['simpan'])) {
    $db->insert([
        'tanggal'     => $_POST['tanggal'],
        'jenis'       => $_POST['jenis'],
        'sumber'      => $_POST['sumber'],
        'jumlah'      => $_POST['jumlah'],
        'keterangan'  => $_POST['keterangan'],
    ])->from_into('keuangan')->create();
    header('Location: keuangan.php?success=Pencatatan keuangan berhasil');
    exit;
}
if (isset($_GET['hapus'])) {
    $db->deleteData()->from_into('keuangan')->where("id_keuangan=".intval($_GET['hapus']))->del();
    header('Location: keuangan.php?success=Data berhasil dihapus');
    exit;
}

$laba = $db->queryOne("SELECT * FROM view_laba_rugi");
$keuangan = $db->query("SELECT * FROM keuangan ORDER BY tanggal DESC");

// Chart bulanan
$chart_raw = $db->query("SELECT DATE_FORMAT(tanggal,'%Y-%m') as bln, jenis, SUM(jumlah) as total FROM keuangan WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY bln, jenis ORDER BY bln ASC");
$bulan_set=[]; $pem=[]; $peng=[];
foreach ($chart_raw as $r) { $bulan_set[$r['bln']] = true; }
foreach (array_keys($bulan_set) as $b) {
    $pem[$b] = 0; $peng[$b] = 0;
}
foreach ($chart_raw as $r) {
    if ($r['jenis']==='Pemasukan') $pem[$r['bln']] = floatval($r['total']);
    else $peng[$r['bln']] = floatval($r['total']);
}
$chart_labels = json_encode(array_keys($bulan_set));
$chart_pem  = json_encode(array_values($pem));
$chart_peng = json_encode(array_values($peng));

include "header.php";
?>
<main id="main">
<div class="mb-4">
    <h4 class="page-title mb-0"><i class="bi bi-cash-coin me-2"></i>Kas & Keuangan</h4>
    <div class="page-breadcrumb"><a href="beranda.php">Beranda</a> / Keuangan</div>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card bg-btj-green"><div class="stat-label">Total Pemasukan</div><div class="stat-val" style="font-size:1.2rem;"><?php echo rupiah($laba['total_pemasukan']??0); ?></div><i class="bi bi-arrow-down-circle stat-icon"></i></div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-btj-red"><div class="stat-label">Total Pengeluaran</div><div class="stat-val" style="font-size:1.2rem;"><?php echo rupiah($laba['total_pengeluaran']??0); ?></div><i class="bi bi-arrow-up-circle stat-icon"></i></div>
    </div>
    <div class="col-md-4">
        <?php $lb=floatval($laba['laba_bersih']??0); ?>
        <div class="stat-card <?php echo $lb>=0?'bg-btj-blue':'bg-btj-orange'; ?>"><div class="stat-label">Laba Bersih</div><div class="stat-val" style="font-size:1.2rem;"><?php echo rupiah($lb); ?></div><i class="bi bi-wallet2 stat-icon"></i></div>
    </div>
</div>

<!-- CHART -->
<div class="card mb-4">
    <div class="card-header bg-white py-3"><span class="fw-bold"><i class="bi bi-bar-chart me-2 text-primary"></i>Grafik Keuangan 6 Bulan Terakhir</span></div>
    <div class="card-body"><div id="chartKeuangan"></div></div>
</div>

<div class="row g-3">
<!-- FORM INPUT -->
<div class="col-lg-4">
    <div class="card">
        <div class="card-header bg-white py-3"><span class="fw-bold"><i class="bi bi-plus-circle me-2 text-success"></i>Catat Keuangan</span></div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jenis</label>
                    <div class="d-flex gap-3">
                        <div class="form-check"><input type="radio" class="form-check-input" name="jenis" value="Pemasukan" checked><label class="form-check-label text-success fw-semibold">Pemasukan</label></div>
                        <div class="form-check"><input type="radio" class="form-check-input" name="jenis" value="Pengeluaran"><label class="form-check-label text-danger fw-semibold">Pengeluaran</label></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sumber / Kategori</label>
                    <input type="text" name="sumber" class="form-control" placeholder="Penjualan, Operasional, Gaji..." list="sumber-list" required>
                    <datalist id="sumber-list">
                        <option value="Penjualan"><option value="Pesanan Online"><option value="Operasional">
                        <option value="Gaji Karyawan"><option value="Pembelian Stok"><option value="Utilitas"><option value="Lainnya">
                    </datalist>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jumlah (Rp)</label>
                    <input type="number" name="jumlah" class="form-control" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" name="simpan" class="btn btn-btj w-100"><i class="bi bi-check-circle me-1"></i>Catat</button>
            </form>
        </div>
    </div>
</div>
<!-- TABEL -->
<div class="col-lg-8">
    <div class="card">
        <div class="card-header bg-white py-3"><span class="fw-bold"><i class="bi bi-table me-2"></i>Riwayat Keuangan</span></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover datatable mb-0">
                    <thead><tr class="text-center"><th>Tanggal</th><th>Jenis</th><th>Sumber</th><th>Jumlah</th><th>Keterangan</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php if (empty($keuangan)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data</td></tr>
                    <?php else: foreach ($keuangan as $k): ?>
                    <tr class="text-center">
                        <td><?php echo tanggal($k['tanggal']); ?></td>
                        <td><span class="badge bg-<?php echo $k['jenis']==='Pemasukan'?'success':'danger'; ?>"><?php echo $k['jenis']; ?></span></td>
                        <td><?php echo htmlspecialchars($k['sumber']); ?></td>
                        <td class="fw-bold text-<?php echo $k['jenis']==='Pemasukan'?'success':'danger'; ?>"><?php echo rupiah($k['jumlah']); ?></td>
                        <td><small><?php echo htmlspecialchars($k['keterangan']); ?></small></td>
                        <td><a href="?hapus=<?php echo $k['id_keuangan']; ?>" onclick="return confirm('Hapus data ini?')" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a></td>
                    </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</main>
<?php include "footer.php"; ?>
<script>
new ApexCharts(document.querySelector("#chartKeuangan"), {
    chart: { type: 'bar', height: 280, toolbar:{show:false}, fontFamily:'Nunito,sans-serif' },
    series: [
        { name: 'Pemasukan', data: <?php echo $chart_pem ?: '[0]'; ?> },
        { name: 'Pengeluaran', data: <?php echo $chart_peng ?: '[0]'; ?> }
    ],
    xaxis: { categories: <?php echo $chart_labels ?: '[""]'; ?> },
    colors: ['#27ae60', '#e74c3c'],
    plotOptions: { bar: { columnWidth: '60%', borderRadius: 4 } },
    yaxis: { labels: { formatter: v => 'Rp '+v.toLocaleString('id-ID') } },
    legend: { position: 'top' },
    dataLabels: { enabled: false },
}).render();
</script>