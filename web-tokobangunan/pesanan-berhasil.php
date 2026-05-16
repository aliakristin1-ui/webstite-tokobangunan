<?php
session_start();

$id_pesanan = $_SESSION['id_pesanan_terakhir'] ?? 0;

if (!$id_pesanan) {
    header("Location: index.php");
    exit;
}

unset($_SESSION['id_pesanan_terakhir']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pesanan Berhasil</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:#f5f6fa;
}

.success-box{
    max-width:650px;
    margin:auto;
}

.id-pesanan{
    font-size:3rem;
    font-weight:800;
    color:#198754;
}

.success-icon{
    width:120px;
    height:120px;
    background:#e9f9ef;
    color:#198754;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
    font-size:70px;
    box-shadow:0 10px 30px rgba(25,135,84,0.15);
}
</style>
</head>

<body>

<div class="container py-5">

    <div class="success-box">

        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body p-5 text-center">

                <div class="mb-4">

                    <div class="success-icon">

                        <i class="bi bi-check2-circle"></i>

                    </div>

                    <h2 class="fw-bold mt-4">
                        Pesanan Berhasil Dibuat
                    </h2>

                    <p class="text-muted">
                        Simpan nomor pesanan berikut
                        untuk cek status pesanan Anda.
                    </p>

                </div>

                <div class="bg-light rounded-4 p-4 mb-4">

                    <div class="text-muted mb-2">
                        Nomor Pesanan
                    </div>

                    <div class="id-pesanan">
                        #<?php echo $id_pesanan; ?>
                    </div>

                </div>

                <div class="d-flex gap-2 justify-content-center flex-wrap">

                    <a href="cek-pesanan.php"
                       class="btn btn-outline-success btn-lg rounded-3 px-4">

                        <i class="bi bi-search me-1"></i>
                        Cek Pesanan

                    </a>

                    <a href="index.php"
                       class="btn btn-success btn-lg rounded-3 px-4">

                        <i class="bi bi-shop me-1"></i>
                        Belanja Lagi

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>