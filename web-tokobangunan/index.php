<?php
include "fungsi.php";
include "Dml.php";

$db = new Dml();

$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$kat  = isset($_GET['kat']) ? trim($_GET['kat']) : '';

$where = "1=1";

if ($cari) {
    $where .= " AND (nama_produk LIKE '%$cari%' 
                OR barcode LIKE '%$cari%')";
}

if ($kat) {
    $where .= " AND kategori='$kat'";
}

$produk = $db->query("
    SELECT 
        p.*,
        COALESCE(
            SUM(
                CASE 
                    WHEN sm.jenis='masuk' THEN sm.jumlah
                    WHEN sm.jenis='keluar' THEN -sm.jumlah
                END
            ),0
        ) as stok_saat_ini
    FROM produk p
    LEFT JOIN stok_mutasi sm 
        ON p.id_produk=sm.id_produk
    WHERE $where
    GROUP BY p.id_produk
    ORDER BY p.nama_produk ASC
");

$kategori = $db->query("
    SELECT DISTINCT kategori 
    FROM produk 
    ORDER BY kategori
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Shop Online | OMM-App</title>

<link rel="icon" type="image/png" href="assets/img/logo.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:#f5f6fa;
    font-family:'Poppins',sans-serif;
}

.shop-header{
    background:linear-gradient(135deg,#0d3d22,#1a6b3c);
    color:white;
    padding:40px 0;
}

.shop-header .input-group{
    background:white;
    border-radius:14px;
    overflow:hidden;
}

.shop-header .form-control{
    height:48px;
}

.shop-header .btn{
    border-radius:12px;
}

.shop-header .btn-warning{
    color:#000;
}

.product-card{
    border:none;
    border-radius:18px;
    overflow:hidden;
    transition:0.2s;
    height:100%;
}

.product-card:hover{
    transform:translateY(-4px);
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.product-image{
    height:200px;
    background:#e9ecef;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:4rem;
    color:#bbb;
}

.price{
    color:#198754;
    font-size:1.1rem;
    font-weight:700;
}

.stok{
    font-size:0.85rem;
}

.btn-cart{
    border-radius:10px;
    font-weight:600;
}
</style>
</head>

<body>

<div class="shop-header">

    <div class="container">

        <div class="row align-items-center g-3">

            <div class="col-lg-3">

                <div>
                    <h2 class="fw-bold mb-1 d-flex align-items-center">
                        <i class="bi bi-shop me-2"></i>
                        OMM Shop
                    </h2>

                    <p class="mb-0 opacity-75 small">
                        Toko Bangunan Our Muda Maju
                    </p>
                </div>

            </div>

            <div class="col-lg-5">

            <form method="get">

                <div class="input-group">

                    <span class="input-group-text border-0 bg-white">
                        <i class="bi bi-search"></i>
                    </span>

                    <input type="text"
                        name="cari"
                        class="form-control border-0 shadow-none"
                        placeholder="Cari semen, cat, paku..."
                        value="<?php echo htmlspecialchars($cari); ?>">

                    <select name="kat"
                            class="form-select border-0 shadow-none"
                            style="max-width:220px;">

                        <option value="">
                            Semua Kategori
                        </option>

                        <?php foreach($kategori as $k): ?>

                        <option value="<?php echo $k['kategori']; ?>"
                            <?php echo $kat==$k['kategori']?'selected':''; ?>>

                            <?php echo $k['kategori']; ?>

                        </option>

                        <?php endforeach; ?>

                    </select>

                    <button class="btn btn-warning px-4 fw-semibold">
                        Cari
                    </button>

                </div>

            </form>

            </div>

            <!-- MENU -->
            <div class="col-lg-4">

                <div class="d-flex justify-content-lg-end gap-2 flex-wrap">

                    <?php
                    $jumlah_cart = 0;

                    if (isset($_SESSION['cart'])) {

                        foreach ($_SESSION['cart'] as $c) {

                            $jumlah_cart += $c['qty'];
                        }
                    }
                    ?>

                    <!-- CEK PESANAN -->
                    <a href="cek-pesanan.php"
                       class="btn btn-light position-relative px-3">

                        <i class="bi bi-search me-1"></i>
                        Cek Pesanan

                    </a>

                    <!-- KERANJANG -->
                    <a href="keranjang.php"
                       class="btn btn-light position-relative px-3">

                        <i class="bi bi-cart3 me-1"></i>
                        Keranjang

                        <?php if($jumlah_cart > 0): ?>

                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                            <?php echo $jumlah_cart; ?>

                        </span>

                        <?php endif; ?>

                    </a>

                    <!-- LOGIN -->
                    <a href="login.php"
                       class="btn btn-warning fw-semibold px-3">

                        <i class="bi bi-person-lock me-1"></i>
                        Login

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

    <!-- PRODUK -->
    <div class="row g-4">

        <?php if(empty($produk)): ?>

        <div class="col-12">
            <div class="alert alert-warning text-center">
                Produk tidak ditemukan
            </div>
        </div>

        <?php else: ?>

        <?php foreach($produk as $p): ?>

        <?php $stok = intval($p['stok_saat_ini']); ?>

        <div class="col-md-3">

            <div class="card product-card shadow-sm">

                <div class="product-image">

                    <?php if(!empty($p['foto'])): ?>

                    <img src="upload/<?php echo $p['foto']; ?>"
                        class="w-100 h-100 object-fit-cover">

                    <?php else: ?>

                    <i class="bi bi-box-seam"></i>

                    <?php endif; ?>

                </div>

                <div class="card-body d-flex flex-column">

                    <div class="mb-2">
                        <span class="badge bg-light text-dark border">
                            <?php echo $p['kategori']; ?>
                        </span>
                    </div>

                    <h6 class="fw-bold">
                        <?php echo htmlspecialchars($p['nama_produk']); ?>
                    </h6>

                    <div class="price mb-2">
                        <?php echo rupiah($p['harga_jual']); ?>
                    </div>

                    <div class="stok mb-3">

                        <?php if($stok <= 0): ?>

                        <span class="badge bg-danger">
                            Stok Habis
                        </span>

                        <?php elseif($stok <= 10): ?>

                        <span class="badge bg-warning text-dark">
                            Stok <?php echo $stok; ?>
                        </span>

                        <?php else: ?>

                        <span class="badge bg-success">
                            Tersedia
                        </span>

                        <?php endif; ?>

                    </div>

                    <div class="mt-auto">

                        <?php if($stok > 0): ?>

                        <a href="cart-add.php?id=<?php echo $p['id_produk']; ?>"
                           class="btn btn-success w-100 btn-cart">

                            <i class="bi bi-cart-plus me-1"></i>
                            Tambah ke Keranjang

                        </a>

                        <?php else: ?>

                        <button class="btn btn-secondary w-100" disabled>
                            Stok Habis
                        </button>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

        <?php endforeach; ?>
        <?php endif; ?>

    </div>

</div>

</body>
</html>