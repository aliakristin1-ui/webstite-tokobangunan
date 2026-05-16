<?php
include "fungsi.php";
validasi();
include "Dml.php";

$db = new Dml();

if (!isset($_GET['id'])) {

    echo "
    <h3 style='text-align:center;margin-top:50px;'>
        ID Pesanan tidak ditemukan!
    </h3>";
    exit;
}

$id_pesanan = intval($_GET['id']);

$pesanan = $db->query("
    SELECT 
        po.*,
        pl.nama AS pelanggan,
        pl.no_hp,
        pl.alamat,
        pl.email
    FROM pesanan_online po
    JOIN pelanggan pl
        ON po.id_pelanggan = pl.id_pelanggan
    WHERE po.id_pesanan = $id_pesanan
    LIMIT 1
");

if (empty($pesanan)) {

    echo "
    <h3 style='text-align:center;margin-top:50px;'>
        Data pesanan tidak ditemukan!
    </h3>";
    exit;
}

$p = $pesanan[0];

$detail = $db->query("
    SELECT 
        dpo.*,
        COALESCE(pr.nama_produk, 'Produk Dihapus') AS nama_produk
    FROM detail_pesanan_online dpo
    LEFT JOIN produk pr
        ON dpo.id_produk = pr.id_produk
    WHERE dpo.id_pesanan = $id_pesanan
");

include "header.php";
?>

<main id="main">

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">

    <div>

        <h4 class="page-title mb-0">
            <i class="bi bi-receipt-cutoff me-2"></i>
            Detail Pesanan #<?php echo $p['id_pesanan']; ?>
        </h4>

        <div class="page-breadcrumb">

            <a href="beranda.php">
                Beranda
            </a>

            /

            <a href="pesanan-online.php">
                Pesanan Online
            </a>

            /

            Detail

        </div>

    </div>

    <a href="pesanan-online.php"
       class="btn btn-outline-secondary">

        <i class="bi bi-arrow-left-circle me-1"></i>
        Kembali

    </a>

</div>

<div class="row justify-content-center">

    <div class="col-lg-7">

        <div class="card shadow-sm border-0">

            <div class="card-body p-4">

                <h5 class="mb-4">

                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Pesanan

                </h5>

                <table class="table table-borderless align-middle">

                    <tr>

                        <td width="35%" class="text-muted">
                            ID Pesanan
                        </td>

                        <td>

                            <span class="badge bg-info fs-6 px-3 py-2">

                                #<?php echo $p['id_pesanan']; ?>

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">
                            Tanggal Pesanan
                        </td>

                        <td class="fw-semibold">
                            <?php echo $p['tanggal']; ?>
                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">
                            Total Belanja
                        </td>

                        <td class="text-success fw-bold fs-5">

                            <?php echo rupiah($p['total']); ?>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">
                            Status Pesanan
                        </td>

                        <td>

                            <?php echo badge_status($p['status']); ?>

                        </td>

                    </tr>

                </table>

                <hr class="my-4">

                <h5 class="mb-4">

                    <i class="bi bi-box-seam me-2"></i>
                    Barang Dipesan

                </h5>

                <?php if(empty($detail)): ?>

                <div class="alert alert-warning">

                    Tidak ada detail barang

                </div>

                <?php else: ?>

                <ul class="list-group mb-4">

                    <?php foreach($detail as $d): ?>

                    <li class="list-group-item d-flex justify-content-between align-items-center">

                        <span>

                            <?php echo htmlspecialchars($d['nama_produk']); ?>

                        </span>

                        <span class="badge bg-success rounded-pill">

                            x<?php echo $d['jumlah']; ?>

                        </span>

                    </li>

                    <?php endforeach; ?>

                </ul>

                <?php endif; ?>

                <hr class="my-4">

                <h5 class="mb-4">

                    <i class="bi bi-person-circle me-2"></i>
                    Data Pelanggan

                </h5>

                <table class="table table-borderless align-middle">

                    <tr>

                        <td width="35%" class="text-muted">
                            Nama Pelanggan
                        </td>

                        <td class="fw-semibold">

                            <?php echo htmlspecialchars($p['pelanggan']); ?>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">
                            Nomor HP
                        </td>

                        <td>

                            <?php echo htmlspecialchars($p['no_hp']); ?>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">
                            Email
                        </td>

                        <td>

                            <?php echo htmlspecialchars($p['email'] ?? '-'); ?>

                        </td>

                    </tr>

                    <tr>

                        <td class="text-muted">
                            Alamat
                        </td>

                        <td>

                            <?php echo htmlspecialchars($p['alamat']); ?>

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

</main>

<?php include "footer.php"; ?>