<?php
session_start();

include "fungsi.php";
include "Dml.php";

$db = new Dml();

$data = null;

if (isset($_POST['cek'])) {

    $id_pesanan = intval($_POST['id_pesanan']);

    $data = $db->queryOne("
        SELECT 
            po.*,
            p.nama,
            p.no_hp,
            p.alamat,
            p.email
        FROM pesanan_online po
        JOIN pelanggan p
            ON po.id_pelanggan = p.id_pelanggan
        WHERE po.id_pesanan = ?
    ", [
        $id_pesanan
    ]);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cek Pesanan</title>

<link rel="icon" type="image/png" href="assets/img/logo.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
    background:#f5f6fa;
}

.track-box{
    max-width:700px;
    margin:auto;
}

.step{
    flex:1;
    text-align:center;
    position:relative;
}

.step::after{
    content:'';
    position:absolute;
    top:20px;
    right:-50%;
    width:100%;
    height:3px;
    background:#ddd;
    z-index:1;
}

.step:last-child::after{
    display:none;
}

.step-circle{
    width:45px;
    height:45px;
    border-radius:50%;
    background:#ddd;
    color:white;
    margin:auto;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
    z-index:2;
    font-weight:bold;
    font-size:18px;
}

.step.active .step-circle{
    background:#198754;
}

.step-text{
    margin-top:10px;
    font-size:14px;
    font-weight:500;
}

.order-number{
    font-size:2rem;
    font-weight:800;
    color:#198754;
}

</style>

</head>
<body>

<div class="container py-5">

    <div class="track-box">

        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body p-4 p-md-5">

                <div class="text-center mb-4">

                    <h2 class="fw-bold">
                        <i class="bi bi-box-seam me-2"></i>
                        Cek Status Pesanan
                    </h2>

                    <p class="text-muted mb-0">
                        Masukkan nomor pesanan Anda
                    </p>

                </div>

                <form method="post" class="row g-3 mb-4">

                    <div class="col-12">

                        <label class="form-label fw-semibold">
                            Nomor Pesanan
                        </label>

                        <input type="number"
                               name="id_pesanan"
                               class="form-control form-control-lg"
                               placeholder="Tulis Nomor"
                               required>

                    </div>

                    <div class="col-12">

                        <button type="submit"
                                name="cek"
                                class="btn btn-success btn-lg w-100">

                            <i class="bi bi-search me-1"></i>
                            Cek Pesanan

                        </button>

                    </div>

                </form>
                <div class="text-center mb-4">

                    <a href="index.php"
                    class="btn btn-outline-secondary">

                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali Belanja

                    </a>

                </div>

                <?php if(isset($_POST['cek'])): ?>

                    <?php if($data): ?>

                        <div class="alert alert-success border-0 rounded-4 p-4">

                            <div class="text-center mb-3">

                                <div class="text-muted">
                                    Nomor Pesanan
                                </div>

                                <div class="order-number">
                                    #<?php echo $data['id_pesanan']; ?>
                                </div>

                            </div>

                            <hr>

                            <div class="row">

                                <div class="col-md-6 mb-2">
                                    <strong>Nama:</strong><br>
                                    <?php echo htmlspecialchars($data['nama']); ?>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <strong>No HP:</strong><br>
                                    <?php echo htmlspecialchars($data['no_hp']); ?>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <strong>Total:</strong><br>
                                    <?php echo rupiah($data['total']); ?>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <strong>Status:</strong><br>
                                    <?php echo badge_status($data['status']); ?>
                                </div>

                            </div>

                        </div>

                        <?php
                        $status = strtolower($data['status']);

                        $step1 = true;
                        $step2 = in_array($status, ['diproses','dikirim','selesai']);
                        $step3 = in_array($status, ['dikirim','selesai']);
                        $step4 = $status == 'selesai';
                        ?>

                        <?php if($status == 'dibatalkan'): ?>

                            <div class="alert alert-danger text-center rounded-4 mt-4">

                                <h5 class="mb-1">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Pesanan Dibatalkan
                                </h5>

                                <small>
                                    Pesanan Anda telah dibatalkan.
                                </small>

                            </div>

                        <?php else: ?>

                        <div class="mt-5">

                            <div class="d-flex justify-content-between">

                                <div class="step <?php echo $step1?'active':''; ?>">

                                    <div class="step-circle">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>

                                    <div class="step-text">
                                        Pending
                                    </div>

                                </div>

                                <div class="step <?php echo $step2?'active':''; ?>">

                                    <div class="step-circle">
                                        <i class="bi bi-gear"></i>
                                    </div>

                                    <div class="step-text">
                                        Diproses
                                    </div>

                                </div>

                                <div class="step <?php echo $step3?'active':''; ?>">

                                    <div class="step-circle">
                                        <i class="bi bi-truck"></i>
                                    </div>

                                    <div class="step-text">
                                        Dikirim
                                    </div>

                                </div>

                                <div class="step <?php echo $step4?'active':''; ?>">

                                    <div class="step-circle">
                                        <i class="bi bi-check2"></i>
                                    </div>

                                    <div class="step-text">
                                        Selesai
                                    </div>

                                </div>

                            </div>

                        </div>

                        <?php endif; ?>

                    <?php else: ?>

                        <div class="alert alert-danger rounded-4">

                            <i class="bi bi-exclamation-circle me-1"></i>
                            Pesanan tidak ditemukan

                        </div>

                    <?php endif; ?>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

</body>
</html>